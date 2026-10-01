<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ─── DASHBOARD DEL CAJERO / EMPLEADO ──────────────────────────────────
        if ($user->isCajero()) {
            $cajaActual = Caja::where('id_user', $user->id)
                              ->where('estado', 'abierta')
                              ->latest('fecha_apertura')
                              ->first();

            $resumenTurno = [
                'efectivo'      => 0,
                'transferencia' => 0,
                'tarjeta'       => 0,
                'fiado'         => 0,
                'total_cobrado' => 0,
                'cantidad'      => 0,
            ];

            if ($cajaActual) {
                $ventasTurno = Venta::where('id_caja', $cajaActual->id_caja)
                                    ->where('estado', '!=', 'anulada')
                                    ->get();

                $resumenTurno['efectivo']      = $ventasTurno->where('metodo_pago', 'efectivo')->sum('total');
                $resumenTurno['transferencia'] = $ventasTurno->where('metodo_pago', 'transferencia')->sum('total');
                $resumenTurno['tarjeta']       = $ventasTurno->where('metodo_pago', 'tarjeta')->sum('total');
                $resumenTurno['fiado']         = $ventasTurno->where('metodo_pago', 'fiado')->sum('total');
                $resumenTurno['total_cobrado'] = $resumenTurno['efectivo'] + $resumenTurno['transferencia'] + $resumenTurno['tarjeta'];
                $resumenTurno['cantidad']      = $ventasTurno->count();
            }

            // Ventas de hoy del cajero
            $ventasHoyCajero = Venta::where('id_user', $user->id)
                                    ->whereDate('created_at', today())
                                    ->where('estado', '!=', 'anulada')
                                    ->sum('total');

            $ventasRecientes = Venta::where('id_user', $user->id)
                                    ->latest()
                                    ->take(5)
                                    ->get();

            $productosCriticos = Producto::whereColumn('stock', '<=', 'stock_critico')->count();

            return view('dashboard', compact(
                'user',
                'cajaActual',
                'resumenTurno',
                'ventasHoyCajero',
                'ventasRecientes',
                'productosCriticos'
            ));
        }

        // ─── DASHBOARD DEL ADMINISTRADOR (MÉTRICAS 100% REALES) ──────────────
        $totalProductos = Producto::count();
        $stockBajo = Producto::whereColumn('stock', '<=', 'stock_minimo')
                             ->whereColumn('stock', '>', 'stock_critico')
                             ->count();
        $stockCritico = Producto::whereColumn('stock', '<=', 'stock_critico')->count();
        $productosBajoStock = $stockBajo + $stockCritico;

        // Valor real del inventario (stock * costo de compra)
        $valorInventario = Producto::selectRaw('COALESCE(SUM(stock * precio_compra), 0) as total')->value('total') ?? 0;

        // Total clientes y deuda acumulada
        $totalClientes = Cliente::count();
        $clientesConDeuda = Cliente::where('saldo', '>', 0)->count();
        $totalDeuda = Cliente::sum('saldo') ?? 0;

        // Ventas reales completadas (sin datos ficticios)
        $ventasTotales = (float) Venta::where('estado', 'completada')->sum('total');
        $ventasHoy = (float) Venta::where('estado', 'completada')->whereDate('created_at', today())->sum('total');
        $ventasPendientes = (float) Venta::where('estado', 'pendiente')->sum('total');

        // Cálculo real de Ganancia Bruta y Margen sobre ventas completadas
        // ganancia = sum(subtotal - (cantidad * costo_unitario))
        $detallesCompletados = DetalleVenta::whereHas('venta', fn($q) => $q->where('estado', 'completada'))->get();

        $costoMercaderiaVendida = 0.0;
        foreach ($detallesCompletados as $det) {
            $costoUnit = (float) ($det->costo_unitario > 0 ? $det->costo_unitario : ($det->producto?->precio_compra ?? 0));
            $costoMercaderiaVendida += ($costoUnit * $det->cantidad);
        }

        $gananciaReal = $ventasTotales - $costoMercaderiaVendida;
        $margenGanancia = $ventasTotales > 0 ? round(($gananciaReal / $ventasTotales) * 100, 1) : 0;

        // Series mensuales reales de los últimos 6 meses para Chart.js
        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $meses = [];
        $ventasMensuales = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mesNum = (int) $fecha->format('n');
            $anio   = (int) $fecha->format('Y');

            $meses[] = $mesesNombres[$mesNum] . ' ' . substr((string)$anio, 2);

            $totalMes = Venta::where('estado', 'completada')
                             ->whereYear('created_at', $anio)
                             ->whereMonth('created_at', $mesNum)
                             ->sum('total');

            $ventasMensuales[] = (float) $totalMes;
        }

        // Ventas recientes reales
        $ventasRecientes = Venta::with('detalles')->latest()->take(6)->get();

        // Productos en alerta para la tabla inferior
        $productosAlerta = Producto::with(['categoria', 'proveedor'])
            ->where(function ($q) {
                $q->whereColumn('stock', '<=', 'stock_minimo')
                  ->orWhereColumn('stock', '<=', 'stock_critico');
            })
            ->orderBy('stock')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'user',
            'totalProductos',
            'productosBajoStock',
            'stockBajo',
            'stockCritico',
            'valorInventario',
            'totalClientes',
            'clientesConDeuda',
            'totalDeuda',
            'ventasTotales',
            'ventasHoy',
            'ventasPendientes',
            'costoMercaderiaVendida',
            'gananciaReal',
            'margenGanancia',
            'meses',
            'ventasMensuales',
            'ventasRecientes',
            'productosAlerta'
        ));
    }
}
