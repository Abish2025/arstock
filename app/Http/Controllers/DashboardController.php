<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Métricas reales del inventario desde la base de datos
        $totalProductos = Producto::count();
        $productosBajoStock = Producto::whereColumn('stock', '<=', 'stock_minimo')->count();
        
        // Suma total del valor del inventario (stock * precio de compra)
        $valorInventario = Producto::select(DB::raw('COALESCE(SUM(stock * precio_compra), 0) as total'))
            ->value('total') ?? 0;

        // Métricas de ventas y clientes (valores iniciales representativos del negocio)
        $ventasTotales = 328000;
        $totalClientes = 892;
        $margenGanancia = 24.8;

        // Datos para los gráficos de Chart.js
        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
        $ventasMensuales = [42000, 48000, 44000, 56000, 52000, 64000];
        $tendenciaVentas = [38000, 46000, 49000, 53000, 58000, 65000];

        // Ventas recientes para la tabla inferior
        $ventasRecientes = [
            [
                'cliente' => 'María García',
                'producto' => 'Laptop HP ProBook',
                'monto' => 1299,
                'fecha' => 'Hoy, 10:30 AM',
                'estado' => 'Completada',
            ],
            [
                'cliente' => 'Carlos Rodríguez',
                'producto' => 'Mouse Logitech MX',
                'monto' => 29,
                'fecha' => 'Hoy, 09:15 AM',
                'estado' => 'Completada',
            ],
            [
                'cliente' => 'Ana Martínez',
                'producto' => 'Teclado Mecánico RGB',
                'monto' => 89,
                'fecha' => 'Ayer, 18:40 PM',
                'estado' => 'Completada',
            ],
            [
                'cliente' => 'Luis Fernández',
                'producto' => 'Monitor Samsung 27"',
                'monto' => 349,
                'fecha' => 'Ayer, 14:20 PM',
                'estado' => 'Pendiente',
            ],
        ];

        return view('dashboard', compact(
            'totalProductos',
            'productosBajoStock',
            'valorInventario',
            'ventasTotales',
            'totalClientes',
            'margenGanancia',
            'meses',
            'ventasMensuales',
            'tendenciaVentas',
            'ventasRecientes'
        ));
    }
}
