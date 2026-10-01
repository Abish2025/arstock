<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\MovimientoStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    // GET /stock/movimientos → Historial de todos los movimientos de inventario
    public function index(Request $request)
    {
        $query = MovimientoStock::with(['producto.categoria', 'usuario'])->latest();

        if ($request->filled('id_producto')) {
            $query->where('id_producto', $request->id_producto);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('producto', function ($q) use ($buscar) {
                $q->where('nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('codigo', 'ilike', "%{$buscar}%");
            });
        }

        $movimientos = $query->paginate(15)->withQueryString();
        $productos = Producto::orderBy('nombre')->get();

        return view('stock.index', compact('movimientos', 'productos'));
    }

    // GET /stock/ajuste → Formulario para registrar un ajuste manual o merma
    public function createAjuste(Request $request)
    {
        $productos = Producto::orderBy('nombre')->get();
        $productoSeleccionado = $request->filled('producto') ? Producto::find($request->producto) : null;

        return view('stock.ajuste', compact('productos', 'productoSeleccionado'));
    }

    // POST /stock/ajuste → Guardar ajuste manual
    public function storeAjuste(Request $request)
    {
        $validated = $request->validate([
            'id_producto' => 'required|exists:productos,id_producto',
            'tipo'        => 'required|in:entrada,salida,merma,rotura,ajuste_manual',
            'cantidad'    => 'required|integer|min:1',
            'motivo'      => 'required|string|min:3|max:255',
        ], [
            'id_producto.required' => 'Debes seleccionar un producto.',
            'cantidad.min'         => 'La cantidad debe ser de al menos 1 unidad.',
            'motivo.required'      => 'Indica el motivo detallado del ajuste (ej: producto vencido, conteo físico).',
        ]);

        DB::transaction(function () use ($validated) {
            $producto = Producto::lockForUpdate()->find($validated['id_producto']);
            $stockAnterior = $producto->stock;
            $cantidad = (int) $validated['cantidad'];

            // Si es salida, merma o rotura, resta del stock
            if (in_array($validated['tipo'], ['salida', 'merma', 'rotura'])) {
                if ($producto->stock < $cantidad) {
                    abort(422, "No puedes descontar {$cantidad} unidades. El stock actual es de {$producto->stock}.");
                }
                $stockNuevo = $stockAnterior - $cantidad;
            } elseif ($validated['tipo'] === 'entrada') {
                $stockNuevo = $stockAnterior + $cantidad;
            } else {
                // ajuste_manual fija el stock a la cantidad ingresada si se indica así, o suma/resta
                $stockNuevo = $cantidad; // fijar inventario exacto contado
            }

            $producto->update(['stock' => $stockNuevo]);

            MovimientoStock::create([
                'id_producto'    => $producto->id_producto,
                'id_user'        => Auth::id(),
                'tipo'           => $validated['tipo'],
                'cantidad'       => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_nuevo'    => $stockNuevo,
                'motivo'         => $validated['motivo'],
            ]);
        });

        return redirect()->route('stock.index')
                         ->with('success', 'Ajuste de inventario registrado correctamente.');
    }
}
