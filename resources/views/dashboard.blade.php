@extends('layouts.app')

@section('content')

{{-- Encabezado del Dashboard --}}
<div class="mb-8">
    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Dashboard</h1>
    <p class="mt-1 text-sm text-slate-500">Resumen general y estado en vivo de tu negocio.</p>
</div>

{{-- Cuadrícula de Métricas Principales (KPIs) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- Tarjeta 1: Ventas Totales --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Ventas Totales</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl font-bold text-slate-900">${{ number_format($ventasTotales, 0, ',', '.') }}</h3>
            <p class="mt-1 flex items-center gap-1 text-xs font-semibold text-emerald-600">
                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 01-1 1H6a1 1 0 010-2h5a1 1 0 011 1zm-1.293 4.707a1 1 0 01-1.414 0l-3-3a1 1 0 011.414-1.414L10 9.586l4.293-4.293a1 1 0 011.414 1.414l-5 5z" clip-rule="evenodd"/>
                </svg>
                <span>+12.5% desde el mes pasado</span>
            </p>
        </div>
    </div>

    {{-- Tarjeta 2: Productos --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Productos en Stock</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalProductos > 0 ? $totalProductos : 1234, 0, ',', '.') }}</h3>
            <p class="mt-1 flex items-center gap-1 text-xs font-semibold {{ $productosBajoStock > 0 ? 'text-amber-600' : 'text-emerald-600' }}">
                @if ($productosBajoStock > 0)
                    <span>⚠️ {{ $productosBajoStock }} con stock bajo</span>
                @else
                    <span>+3.2% nuevos este mes</span>
                @endif
            </p>
        </div>
    </div>

    {{-- Tarjeta 3: Clientes --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Clientes</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl font-bold text-slate-900">{{ number_format($totalClientes, 0, ',', '.') }}</h3>
            <p class="mt-1 flex items-center gap-1 text-xs font-semibold text-emerald-600">
                <span>↗ 81% clientes activos</span>
            </p>
        </div>
    </div>

    {{-- Tarjeta 4: Margen de Ganancia --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Margen de Ganancia</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl font-bold text-slate-900">{{ $margenGanancia }}%</h3>
            <p class="mt-1 flex items-center gap-1 text-xs font-semibold text-slate-500">
                <span>Promedio estimado del mes</span>
            </p>
        </div>
    </div>

</div>

{{-- Cuadrícula de Gráficos (Ventas Mensuales y Tendencia) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- Gráfico de Barras: Ventas Mensuales --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Ventas Mensuales</h3>
                <p class="text-xs text-slate-500">Ingresos brutos por mes</p>
            </div>
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Últimos 6 meses</span>
        </div>
        <div class="h-64 w-full">
            <canvas id="ventasMensualesChart"></canvas>
        </div>
    </div>

    {{-- Gráfico de Línea: Tendencia de Ventas --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Tendencia de Ventas</h3>
                <p class="text-xs text-slate-500">Evolución y ritmo de crecimiento</p>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60 font-medium">Crecimiento constante</span>
        </div>
        <div class="h-64 w-full">
            <canvas id="tendenciaVentasChart"></canvas>
        </div>
    </div>

</div>

{{-- Tabla de Ventas Recientes --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Ventas Recientes</h3>
            <p class="text-xs text-slate-500">Últimas transacciones registradas</p>
        </div>
        <a href="#" class="text-xs font-semibold text-slate-900 hover:text-emerald-600 transition-colors">
            Ver todas &rarr;
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Cliente</th>
                    <th class="px-6 py-3.5">Producto</th>
                    <th class="px-6 py-3.5 text-right">Total</th>
                    <th class="px-6 py-3.5 text-center">Estado</th>
                    <th class="px-6 py-3.5 text-right">Fecha / Hora</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($ventasRecientes as $venta)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($venta['cliente'], 0, 1)) }}
                                </div>
                                <span class="font-semibold text-slate-800">{{ $venta['cliente'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600">
                            {{ $venta['producto'] }}
                        </td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">
                            ${{ number_format($venta['monto'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($venta['estado'] === 'Completada')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Completada
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Pendiente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-xs text-slate-400 font-medium">
                            {{ $venta['fecha'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Scripts para renderizar los gráficos con Chart.js --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const labels = @json($meses);
        const dataBarras = @json($ventasMensuales);
        const dataLineas = @json($tendenciaVentas);

        // 1. Gráfico de Barras: Ventas Mensuales
        const ctxBarras = document.getElementById('ventasMensualesChart').getContext('2d');
        new Chart(ctxBarras, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: dataBarras,
                    backgroundColor: '#0f172a',
                    borderRadius: 8,
                    borderSkipped: false,
                    barPercentage: 0.65,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#64748b',
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });

        // 2. Gráfico de Líneas: Tendencia de Ventas
        const ctxLineas = document.getElementById('tendenciaVentasChart').getContext('2d');
        new Chart(ctxLineas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Tendencia ($)',
                    data: dataLineas,
                    borderColor: '#10b981',
                    borderWidth: 2.5,
                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 4,
                    pointBackgroundColor: '#10b981',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 }, color: '#64748b' }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: '#64748b',
                            callback: function(value) { return '$' + value.toLocaleString(); }
                        }
                    }
                }
            }
        });
    });
</script>

@endsection
