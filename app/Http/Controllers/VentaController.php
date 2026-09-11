<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\MovimientoCuentaCorriente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // GET /ventas → Lista de ventas con KPIs, buscador y filtros
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'detalles'])->orderByDesc('created_at');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'ilike', "%{$buscar}%")
                  ->orWhere('cliente_nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('metodo_pago', 'ilike', "%{$buscar}%");
            });
        }

        $ventas = $query->paginate(10)->withQueryString();

        // 4 KPIs superiores (como en Figma)
        $totalIngresos = Venta::where('estado', 'completada')->sum('total');
        $totalTransacciones = Venta::count();
        $totalCompletadas = Venta::where('estado', 'completada')->count();
        $totalPendientes = Venta::where('estado', 'pendiente')->count();

        return view('ventas.index', compact(
            'ventas',
            'totalIngresos',
            'totalTransacciones',
            'totalCompletadas',
            'totalPendientes'
        ));
    }

    // GET /ventas/create → Punto de Venta (POS) para cobrar
    public function create()
    {
        $productos = Producto::with('categoria')->orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre')->get();

        return view('ventas.create', compact('productos', 'clientes'));
    }

    // POST /ventas → Procesar y guardar la venta
    public function store(Request $request)
    {
        $request->validate([
            'metodo_pago'             => 'required|in:efectivo,transferencia,tarjeta,fiado',
            'id_cliente'              => 'nullable|exists:clientes,id_cliente',
            'cliente_nombre'          => 'nullable|max:100',
            'notas'                   => 'nullable|max:500',
            'items'                   => 'required|array|min:1',
            'items.*.id_producto'     => 'required|exists:productos,id_producto',
            'items.*.cantidad'        => 'required|integer|min:1',
        ], [
            'items.required'          => 'Debes agregar al menos un producto a la venta.',
            'items.min'               => 'Debes agregar al menos un producto a la venta.',
        ]);

        // Si el pago es fiado, es obligatorio seleccionar un cliente registrado
        if ($request->metodo_pago === 'fiado' && !$request->filled('id_cliente')) {
            return back()->withInput()->with('error', 'Para ventas a fiado debes seleccionar un cliente registrado de la lista.');
        }

        return DB::transaction(function () use ($request) {
            // Verificar stock disponible para cada producto antes de proceder
            foreach ($request->items as $item) {
                $prod = Producto::lockForUpdate()->find($item['id_producto']);
                if ($prod->stock < $item['cantidad']) {
                    return back()->withInput()->with('error', "Stock insuficiente para '{$prod->nombre}'. Disponibles: {$prod->stock}");
                }
            }

            // Generar código correlativo tipo VT-001
            $ultimoId = Venta::max('id_venta') ?? 0;
            $codigo = 'VT-' . str_pad($ultimoId + 1, 3, '0', STR_PAD_LEFT);

            // Determinar nombre del cliente
            $cliente = null;
            if ($request->filled('id_cliente')) {
                $cliente = Cliente::find($request->id_cliente);
                $clienteNombre = $cliente->nombre;
            } else {
                $clienteNombre = $request->filled('cliente_nombre') ? $request->cliente_nombre : 'Consumidor Final';
            }

            $esFiado = ($request->metodo_pago === 'fiado');
            $estado = $esFiado ? 'pendiente' : 'completada';

            // Crear cabecera de la venta
            $venta = Venta::create([
                'codigo'         => $codigo,
                'id_cliente'     => $cliente ? $cliente->id_cliente : null,
                'cliente_nombre' => $clienteNombre,
                'metodo_pago'    => $request->metodo_pago,
                'total'          => 0, // Se calcula abajo sumando renglones
                'estado'         => $estado,
                'notas'          => $request->notas,
            ]);

            $totalVenta = 0;

            // Procesar cada producto, calcular subtotales y descontar inventario
            foreach ($request->items as $item) {
                $prod = Producto::find($item['id_producto']);
                $subtotal = $prod->precio_venta * $item['cantidad'];
                $totalVenta += $subtotal;

                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_producto'     => $prod->id_producto,
                    'producto_nombre' => $prod->nombre,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $prod->precio_venta,
                    'subtotal'        => $subtotal,
                ]);

                // Descontar del inventario
                $prod->decrement('stock', $item['cantidad']);
            }

            // Actualizar el total final de la venta
            $venta->update(['total' => $totalVenta]);

            // Si fue fiado, sumarle la deuda al cliente y registrar en su cuenta corriente
            if ($esFiado && $cliente) {
                $saldoAnterior = $cliente->saldo;
                $saldoNuevo = $saldoAnterior + $totalVenta;

                $cliente->update(['saldo' => $saldoNuevo]);

                MovimientoCuentaCorriente::create([
                    'id_cliente'     => $cliente->id_cliente,
                    'tipo'           => 'fiado',
                    'monto'          => $totalVenta,
                    'concepto'       => "Compra fiada comprobante {$codigo}",
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_nuevo'    => $saldoNuevo,
                ]);
            }

            return redirect()->route('ventas.show', $venta)
                             ->with('success', "Venta {$codigo} registrada exitosamente.");
        });
    }

    // GET /ventas/{venta} → Ver comprobante / ticket
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'detalles.producto']);
        return view('ventas.show', compact('venta'));
    }

    // DELETE /ventas/{venta} → Anular venta (restituye stock y ajusta deuda)
    public function destroy(Venta $venta)
    {
        DB::transaction(function () use ($venta) {
            // 1. Devolver el stock a cada producto
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto) {
                    $detalle->producto->increment('stock', $detalle->cantidad);
                }
            }

            // 2. Si era una venta fiada pendiente, descontar de la deuda del cliente
            if ($venta->estado === 'pendiente' && $venta->id_cliente && $venta->cliente) {
                $cliente = $venta->cliente;
                $saldoAnterior = $cliente->saldo;
                $saldoNuevo = max(0, $saldoAnterior - $venta->total);

                $cliente->update(['saldo' => $saldoNuevo]);

                MovimientoCuentaCorriente::create([
                    'id_cliente'     => $cliente->id_cliente,
                    'tipo'           => 'pago',
                    'monto'          => $venta->total,
                    'concepto'       => "Anulación de venta fiada {$venta->codigo}",
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_nuevo'    => $saldoNuevo,
                ]);
            }

            $venta->delete();
        });

        return redirect()->route('ventas.index')
                         ->with('success', "Venta {$venta->codigo} anulada y stock restituido al inventario.");
    }
}
