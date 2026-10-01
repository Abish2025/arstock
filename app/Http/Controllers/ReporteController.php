<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\Cliente;
use App\Models\MovimientoStock;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->get('periodo', 'mes');
        $fechaDesdeInput = $request->get('fecha_desde');
        $fechaHastaInput = $request->get('fecha_hasta');

        $fechaInicio = null;
        $fechaFin = null;

        if ($periodo === 'personalizado' && $fechaDesdeInput) {
            $fechaInicio = Carbon::parse($fechaDesdeInput)->startOfDay();
            $fechaFin = $fechaHastaInput ? Carbon::parse($fechaHastaInput)->endOfDay() : Carbon::now()->endOfDay();
        } else {
            match ($periodo) {
                'hoy'    => [$fechaInicio = Carbon::today()->startOfDay(), $fechaFin = Carbon::today()->endOfDay()],
                'ayer'   => [$fechaInicio = Carbon::yesterday()->startOfDay(), $fechaFin = Carbon::yesterday()->endOfDay()],
                'semana' => [$fechaInicio = Carbon::now()->subDays(7)->startOfDay(), $fechaFin = Carbon::now()->endOfDay()],
                'mes'    => [$fechaInicio = Carbon::now()->startOfMonth(), $fechaFin = Carbon::now()->endOfDay()],
                'anio'   => [$fechaInicio = Carbon::now()->startOfYear(), $fechaFin = Carbon::now()->endOfDay()],
                'todo'   => [$fechaInicio = null, $fechaFin = null],
                default  => [$fechaInicio = Carbon::now()->startOfMonth(), $fechaFin = Carbon::now()->endOfDay()],
            };
        }

        // Query base para ventas válidas (excluyendo anuladas)
        $ventasQuery = Venta::where('estado', '!=', 'anulada');
        if ($fechaInicio) {
            $ventasQuery->where('created_at', '>=', $fechaInicio);
        }
        if ($fechaFin) {
            $ventasQuery->where('created_at', '<=', $fechaFin);
        }

        $ventasIds = (clone $ventasQuery)->pluck('id_venta');
        $totalIngresos = (float) (clone $ventasQuery)->sum('total');
        $cantidadVentas = (clone $ventasQuery)->count();

        // 1. Rentabilidad y Ganancias reales con Costo Histórico (CMV)
        $detalles = DetalleVenta::whereIn('id_venta', $ventasIds)->with('producto')->get();

        $totalCosto = 0.0;
        foreach ($detalles as $detalle) {
            $costoUnitario = (float) ($detalle->costo_unitario > 0 ? $detalle->costo_unitario : ($detalle->producto?->precio_compra ?? 0));
            $totalCosto += ($costoUnitario * $detalle->cantidad);
        }

        $gananciaBruta = $totalIngresos - $totalCosto;
        $margenPorcentaje = $totalIngresos > 0 ? round(($gananciaBruta / $totalIngresos) * 100, 1) : 0;

        // 2. Desglose por Método de Pago
        $metodosPago = (clone $ventasQuery)
            ->selectRaw('metodo_pago, count(*) as transacciones, sum(total) as total')
            ->groupBy('metodo_pago')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalIngresos) {
                $item->porcentaje = $totalIngresos > 0 ? round(($item->total / $totalIngresos) * 100, 1) : 0;
                return $item;
            });

        // 3. Top 10 Productos Más Vendidos
        $topProductos = DetalleVenta::whereIn('id_venta', $ventasIds)
            ->selectRaw('producto_nombre, sum(cantidad) as total_unidades, sum(subtotal) as total_recaudado')
            ->groupBy('producto_nombre')
            ->orderByDesc('total_unidades')
            ->limit(10)
            ->get();

        // 4. Lista de Reposición Urgente a Proveedores (Stock bajo o crítico)
        $productosReponer = Producto::with(['categoria', 'proveedor'])
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'stock_minimo')
                  ->orWhereColumn('stock', '<=', 'stock_critico');
            })
            ->orderBy('stock')
            ->get()
            ->map(function ($producto) {
                $sugerido = max(1, ($producto->stock_minimo * 2) - $producto->stock);
                $costoEstimado = $sugerido * (float) $producto->precio_compra;
                $producto->sugerido_reponer = $sugerido;
                $producto->costo_reposicion = $costoEstimado;
                return $producto;
            });

        $totalInversionReposicion = $productosReponer->sum('costo_reposicion');
        $articulosCriticosCount = $productosReponer->count();

        // 5. Clientes con Deuda (Cuentas Corrientes activas)
        $clientesDeudores = Cliente::where('saldo', '>', 0)
            ->orderByDesc('saldo')
            ->get();
        $totalDeudaClientes = $clientesDeudores->sum('saldo');

        return view('reportes.index', compact(
            'periodo',
            'fechaDesdeInput',
            'fechaHastaInput',
            'totalIngresos',
            'cantidadVentas',
            'totalCosto',
            'gananciaBruta',
            'margenPorcentaje',
            'metodosPago',
            'topProductos',
            'productosReponer',
            'totalInversionReposicion',
            'articulosCriticosCount',
            'clientesDeudores',
            'totalDeudaClientes'
        ));
    }

    // Vista de impresión limpia (para reposición de mercadería con proveedores)
    public function imprimirReposicion()
    {
        $productosReponer = Producto::with(['categoria', 'proveedor'])
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'stock_minimo')
                  ->orWhereColumn('stock', '<=', 'stock_critico');
            })
            ->orderBy('id_proveedor')
            ->orderBy('nombre')
            ->get()
            ->map(function ($producto) {
                $sugerido = max(1, ($producto->stock_minimo * 2) - $producto->stock);
                $producto->sugerido_reponer = $sugerido;
                $producto->costo_reposicion = $sugerido * (float) $producto->precio_compra;
                return $producto;
            });

        $totalInversion = $productosReponer->sum('costo_reposicion');

        return view('reportes.reposicion_print', compact('productosReponer', 'totalInversion'));
    }
}
