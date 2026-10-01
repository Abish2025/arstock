<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    // GET /compras → Lista de compras e ingresos de mercadería
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'usuario', 'detalles'])->orderByDesc('fecha')->orderByDesc('id_compra');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('comprobante_numero', 'ilike', "%{$buscar}%")
                  ->orWhereHas('proveedor', function ($sub) use ($buscar) {
                      $sub->where('empresa', 'ilike', "%{$buscar}%");
                  });
            });
        }

        $compras = $query->paginate(10)->withQueryString();

        $totalCompras = Compra::count();
        $totalInvertido = Compra::sum('total');

        return view('compras.index', compact('compras', 'totalCompras', 'totalInvertido'));
    }

    // GET /compras/create → Formulario de ingreso de mercadería
    public function create()
    {
        $proveedores = Proveedor::orderBy('empresa')->get();
        $productos = Producto::orderBy('nombre')->get();

        return view('compras.create', compact('proveedores', 'productos'));
    }

    // POST /compras → Guardar compra, incrementar stock y actualizar costos
    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor'        => 'required|exists:proveedores,id_proveedor',
            'comprobante_numero'  => 'nullable|string|max:50',
            'fecha'               => 'required|date',
            'notas'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.id_producto' => 'required|exists:productos,id_producto',
            'items.*.cantidad'    => 'required|integer|min:1',
            'items.*.costo'       => ['required', 'numeric', 'gt:0', 'regex:/^\d+(\.\d{1,2})?$/'],
        ], [
            'id_proveedor.required' => 'Debes seleccionar el proveedor que entrega la mercadería.',
            'items.required'        => 'Debes incluir al menos un producto recibido.',
            'items.*.costo.gt'      => 'El costo unitario debe ser mayor a $0.',
        ]);

        $compra = DB::transaction(function () use ($request) {
            $totalCompra = 0;

            // 1. Crear cabecera de la compra
            $compra = Compra::create([
                'id_proveedor'       => $request->id_proveedor,
                'id_user'            => Auth::id(),
                'comprobante_numero' => $request->comprobante_numero ?: 'REM-' . time(),
                'fecha'              => $request->fecha,
                'total'              => 0,
                'notas'              => $request->notas,
            ]);

            // 2. Procesar cada renglón recibido
            foreach ($request->items as $item) {
                $prod = Producto::lockForUpdate()->find($item['id_producto']);
                $cantidad = (int) $item['cantidad'];
                $costoUnitario = (float) $item['costo'];
                $subtotal = $cantidad * $costoUnitario;
                $totalCompra += $subtotal;

                // Crear detalle
                DetalleCompra::create([
                    'id_compra'      => $compra->id_compra,
                    'id_producto'    => $prod->id_producto,
                    'cantidad'       => $cantidad,
                    'costo_unitario' => $costoUnitario,
                    'subtotal'       => $subtotal,
                ]);

                // Registrar auditoría de movimiento de inventario
                $stockAnterior = $prod->stock;
                $stockNuevo    = $stockAnterior + $cantidad;

                MovimientoStock::create([
                    'id_producto'    => $prod->id_producto,
                    'id_user'        => Auth::id(),
                    'tipo'           => 'compra',
                    'cantidad'       => $cantidad,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo'    => $stockNuevo,
                    'motivo'         => "Recepción compra Comprobante: {$compra->comprobante_numero}",
                ]);

                // Incrementar stock y actualizar precio de compra de referencia
                $prod->update([
                    'stock'         => $stockNuevo,
                    'precio_compra' => $costoUnitario,
                    'id_proveedor'  => $request->id_proveedor, // Asociar producto a este proveedor
                ]);
            }

            // Actualizar total final
            $compra->update(['total' => $totalCompra]);

            return $compra;
        });

        return redirect()->route('compras.show', $compra)
                         ->with('success', "Ingreso de mercadería registrado exitosamente. Stock actualizado.");
    }

    // GET /compras/{compra} → Ver comprobante de ingreso
    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'usuario', 'detalles.producto']);

        return view('compras.show', compact('compra'));
    }
}
