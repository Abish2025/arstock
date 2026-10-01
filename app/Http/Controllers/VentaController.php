<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Caja;
use App\Models\MovimientoCuentaCorriente;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    // GET /ventas → Lista de ventas realizadas con KPIs y buscador
    public function index(Request $request)
    {
        $query = Venta::with(['cliente', 'usuario', 'detalles'])->latest();

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'ilike', "%{$buscar}%")
                  ->orWhere('cliente_nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('metodo_pago', 'ilike', "%{$buscar}%")
                  ->orWhere('referencia_pago', 'ilike', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        $ventas = $query->paginate(10)->withQueryString();

        // KPIs reales (excluyendo ventas anuladas para ingresos)
        $totalIngresos       = Venta::where('estado', 'completada')->sum('total');
        $totalTransacciones  = Venta::count();
        $totalCompletadas    = Venta::where('estado', 'completada')->count();
        $totalPendientes     = Venta::where('estado', 'pendiente')->count();
        $totalAnuladas       = Venta::where('estado', 'anulada')->count();

        return view('ventas.index', compact(
            'ventas',
            'totalIngresos',
            'totalTransacciones',
            'totalCompletadas',
            'totalPendientes',
            'totalAnuladas'
        ));
    }

    // GET /ventas/create → Punto de Cobro (POS)
    public function create()
    {
        $productos = Producto::with('categoria')->orderBy('nombre')->get();
        $clientes = Cliente::orderBy('nombre')->get();

        // Obtener la caja activa del usuario logueado (si tiene)
        $cajaActual = Caja::where('id_user', Auth::id())
                          ->where('estado', 'abierta')
                          ->latest('fecha_apertura')
                          ->first();

        return view('ventas.create', compact('productos', 'clientes', 'cajaActual'));
    }

    // POST /ventas → Registrar venta atómica con validación estricta
    public function store(Request $request)
    {
        $request->validate([
            'metodo_pago'         => 'required|in:efectivo,transferencia,tarjeta,fiado',
            'id_cliente'          => 'nullable|exists:clientes,id_cliente',
            'cliente_nombre'      => 'nullable|string|max:100',
            'notas'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:productos,id_producto',
            'items.*.cantidad'    => 'required|integer|min:1',
            'monto_recibido'      => 'nullable|numeric|min:0',
            'referencia_pago'     => 'nullable|string|max:100',
        ], [
            'items.required'          => 'Debes agregar al menos un producto al ticket.',
            'items.min'               => 'Debes agregar al menos un producto al ticket.',
            'items.*.cantidad.min'    => 'La cantidad mínima por producto es de 1 unidad.',
            'metodo_pago.required'    => 'Selecciona la forma de pago.',
        ]);

        // Si es fiado, es obligatorio seleccionar un cliente registrado
        if ($request->metodo_pago === 'fiado' && !$request->filled('id_cliente')) {
            return back()->withInput()->with('error', 'Para ventas a fiado debes seleccionar un cliente registrado en el cuaderno.');
        }

        // 1. Consolidar productos duplicados para evitar descontar stock varias veces de forma inconsistente
        $itemsConsolidados = [];
        foreach ($request->items as $item) {
            $idProd = (int) $item['id_producto'];
            $cant = (int) $item['cantidad'];
            if ($cant < 1) continue;

            if (isset($itemsConsolidados[$idProd])) {
                $itemsConsolidados[$idProd] += $cant;
            } else {
                $itemsConsolidados[$idProd] = $cant;
            }
        }

        if (empty($itemsConsolidados)) {
            return back()->withInput()->with('error', 'No se indicaron cantidades válidas de productos para vender.');
        }

        // 2. Transacción Atómica Completa
        return DB::transaction(function () use ($request, $itemsConsolidados) {
            // Verificar stock con bloqueo pesimista lockForUpdate()
            $productosData = [];
            $totalVenta = 0;

            foreach ($itemsConsolidados as $idProd => $cantidad) {
                $prod = Producto::lockForUpdate()->find($idProd);

                if ($prod->stock < $cantidad) {
                    abort(422, "Stock insuficiente para '{$prod->nombre}'. Disponible: {$prod->stock}, Solicitado: {$cantidad}.");
                }

                $subtotal = $prod->precio_venta * $cantidad;
                $totalVenta += $subtotal;

                $productosData[] = [
                    'producto' => $prod,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                ];
            }

            // Si es fiado, validar límite de crédito del cliente
            $cliente = null;
            if ($request->filled('id_cliente')) {
                $cliente = Cliente::lockForUpdate()->find($request->id_cliente);
            }

            if ($request->metodo_pago === 'fiado' && $cliente) {
                if ($cliente->limite_credito && $cliente->limite_credito > 0) {
                    $saldoProyectado = (float)$cliente->saldo + (float)$totalVenta;
                    if ($saldoProyectado > (float)$cliente->limite_credito) {
                        $disponible = max(0, (float)$cliente->limite_credito - (float)$cliente->saldo);
                        abort(422, "El cliente '{$cliente->nombre}' superaría su límite de crédito de $" .
                            number_format($cliente->limite_credito, 2, ',', '.') .
                            ". Saldo actual: $" . number_format($cliente->saldo, 2, ',', '.') .
                            ". Disponible para fiar: $" . number_format($disponible, 2, ',', '.') . ".");
                    }
                }
            }

            // Identificar caja abierta del usuario
            $cajaActual = Caja::where('id_user', Auth::id())
                              ->where('estado', 'abierta')
                              ->latest('fecha_apertura')
                              ->first();

            // Generar código de venta único correlativo
            $ultimoId = Venta::max('id_venta') ?? 0;
            $codigo = 'VT-' . str_pad($ultimoId + 1, 4, '0', STR_PAD_LEFT);

            // Determinar nombre del cliente
            $clienteNombre = $cliente ? $cliente->nombre : ($request->filled('cliente_nombre') ? $request->cliente_nombre : 'Consumidor Final');

            $esFiado = ($request->metodo_pago === 'fiado');
            $estado  = $esFiado ? 'pendiente' : 'completada';

            // Cálculos de efectivo y vuelto
            $montoRecibido = 0;
            $vuelto = 0;
            if ($request->metodo_pago === 'efectivo') {
                $montoRecibido = (float) $request->input('monto_recibido', $totalVenta);
                if ($montoRecibido < $totalVenta) {
                    $montoRecibido = $totalVenta;
                }
                $vuelto = max(0, $montoRecibido - $totalVenta);
            }

            // Crear cabecera de la venta
            $venta = Venta::create([
                'codigo'          => $codigo,
                'id_user'         => Auth::id(),
                'id_caja'         => $cajaActual ? $cajaActual->id_caja : null,
                'id_cliente'      => $cliente ? $cliente->id_cliente : null,
                'cliente_nombre'  => $clienteNombre,
                'metodo_pago'     => $request->metodo_pago,
                'total'           => $totalVenta,
                'monto_recibido'  => $montoRecibido,
                'vuelto'          => $vuelto,
                'referencia_pago' => $request->referencia_pago,
                'estado'          => $estado,
                'notas'           => $request->notas,
            ]);

            // Guardar detalles y descontar stock registrando auditoría
            foreach ($productosData as $item) {
                $prod = $item['producto'];
                $cant = $item['cantidad'];

                DetalleVenta::create([
                    'id_venta'        => $venta->id_venta,
                    'id_producto'     => $prod->id_producto,
                    'producto_nombre' => $prod->nombre,
                    'cantidad'        => $cant,
                    'precio_unitario' => $prod->precio_venta,
                    'costo_unitario'  => $prod->precio_compra, // Costo histórico registrado en el momento exacto de la venta
                    'subtotal'        => $item['subtotal'],
                ]);

                // Descontar inventario
                $stockAnterior = $prod->stock;
                $stockNuevo    = $stockAnterior - $cant;
                $prod->update(['stock' => $stockNuevo]);

                // Registrar en historial de stock
                MovimientoStock::create([
                    'id_producto'    => $prod->id_producto,
                    'id_user'        => Auth::id(),
                    'tipo'           => 'venta',
                    'cantidad'       => $cant,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'    => $stockNuevo,
                    'motivo'         => "Venta {$codigo} ({$clienteNombre})",
                ]);
            }

            // Si es fiado, actualizar saldo del cliente y asentar movimiento de cuenta corriente
            if ($esFiado && $cliente) {
                $saldoAnterior = (float)$cliente->saldo;
                $saldoNuevo    = $saldoAnterior + $totalVenta;

                $cliente->update(['saldo' => $saldoNuevo]);

                MovimientoCuentaCorriente::create([
                    'id_cliente'     => $cliente->id_cliente,
                    'tipo'           => 'fiado',
                    'monto'          => $totalVenta,
                    'concepto'       => "Compra a fiado según Ticket {$codigo}",
                    'saldo_anterior' => $saldoAnterior,
                    'saldo_nuevo'    => $saldoNuevo,
                ]);
            }

            return redirect()->route('ventas.show', $venta)
                             ->with('success', "Venta {$codigo} registrada exitosamente.");
        });
    }

    // GET /ventas/{venta} → Comprobante y ticket para impresión
    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'usuario', 'detalles.producto', 'caja']);

        return view('ventas.show', compact('venta'));
    }

    // DELETE /ventas/{venta} → Anular venta (Solo Admin)
    public function destroy(Venta $venta)
    {
        // Solo el administrador puede anular ventas
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Solo un Administrador puede anular ventas.');
        }

        if ($venta->estado === 'anulada') {
            return redirect()->route('ventas.index')
                             ->with('error', "La venta {$venta->codigo} ya fue anulada anteriormente.");
        }

        DB::transaction(function () use ($venta) {
            // 1. Revertir y restituir stock de cada producto
            foreach ($venta->detalles as $detalle) {
                if ($detalle->producto) {
                    $prod = Producto::lockForUpdate()->find($detalle->id_producto);
                    if ($prod) {
                        $stockAnterior = $prod->stock;
                        $stockNuevo    = $stockAnterior + $detalle->cantidad;
                        $prod->update(['stock' => $stockNuevo]);

                        MovimientoStock::create([
                            'id_producto'    => $prod->id_producto,
                            'id_user'        => Auth::id(),
                            'tipo'           => 'anulacion_venta',
                            'cantidad'       => $detalle->cantidad,
                            'stock_anterior' => $stockAnterior,
                            'stock_nuevo'    => $stockNuevo,
                            'motivo'         => "Anulación de Venta {$venta->codigo}",
                        ]);
                    }
                }
            }

            // 2. Si fue fiado y estaba pendiente, revertir el saldo deudor del cliente
            if ($venta->estado === 'pendiente' && $venta->id_cliente && $venta->cliente) {
                $cliente = Cliente::lockForUpdate()->find($venta->id_cliente);
                if ($cliente) {
                    $saldoAnterior = (float)$cliente->saldo;
                    $saldoNuevo    = max(0, $saldoAnterior - (float)$venta->total);
                    $cliente->update(['saldo' => $saldoNuevo]);

                    MovimientoCuentaCorriente::create([
                        'id_cliente'     => $cliente->id_cliente,
                        'tipo'           => 'pago',
                        'monto'          => (float)$venta->total,
                        'concepto'       => "Reversión por anulación de Ticket {$venta->codigo}",
                        'saldo_anterior' => $saldoAnterior,
                        'saldo_nuevo'    => $saldoNuevo,
                    ]);
                }
            }

            // 3. Cambiar estado a anulada preservando el registro histórico
            $venta->update(['estado' => 'anulada']);
        });

        return redirect()->route('ventas.index')
                         ->with('success', "Venta {$venta->codigo} anulada. El stock fue restituido al inventario y se ajustaron los saldos.");
    }
}