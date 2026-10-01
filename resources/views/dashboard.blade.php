@extends('layouts.app')

@section('content')

@if (auth()->user()->isCajero())
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- PANEL DEL CAJERO / EMPLEADO (OPERATIVO)                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Hola, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-1 text-sm text-slate-500">Panel operativo de cobro y estado de tu turno de caja.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('ventas.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-md hover:bg-emerald-700 transition-all">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
                PUNTO DE COBRO (NUEVA VENTA)
            </a>
        </div>
    </div>

    {{-- Estado de la Caja --}}
    <div class="mb-8">
        @if ($cajaActual)
            <div class="bg-white rounded-2xl border-2 border-emerald-500/50 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                        </span>
                        <h2 class="text-base font-bold text-slate-900">Turno de Caja Abierto ({{ $cajaActual->fecha_apertura->format('H:i') }} hs)</h2>
                    </div>
                    <a href="{{ route('cajas.index') }}" class="text-xs font-bold text-slate-700 hover:underline">
                        Administrar Caja &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4">
                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-500 block">Fondo Inicial</span>
                        <span class="text-lg font-bold text-slate-900 mt-0.5 block">${{ number_format($cajaActual->monto_apertura, 2, ',', '.') }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-500 block">Cobrado en Efectivo</span>
                        <span class="text-lg font-bold text-emerald-600 mt-0.5 block">${{ number_format($resumenTurno['efectivo'], 2, ',', '.') }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-slate-50">
                        <span class="text-xs text-slate-500 block">Cobrado Digital</span>
                        <span class="text-lg font-bold text-slate-900 mt-0.5 block">${{ number_format($resumenTurno['transferencia'] + $resumenTurno['tarjeta'], 2, ',', '.') }}</span>
                    </div>
                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200">
                        <span class="text-xs font-bold text-emerald-800 block">Total en Caja Físico</span>
                        <span class="text-lg font-black text-emerald-700 mt-0.5 block">${{ number_format($cajaActual->monto_apertura + $resumenTurno['efectivo'], 2, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-amber-300 bg-amber-50/50 p-6 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="h-3 w-3 rounded-full bg-amber-400"></span>
                    <div>
                        <h2 class="text-base font-bold text-amber-900">Tu caja se encuentra cerrada</h2>
                        <p class="text-xs text-amber-700">Abre turno indicando el fondo inicial para mantener el arqueo al día.</p>
                    </div>
                </div>
                <a href="{{ route('cajas.index') }}" class="rounded-xl bg-slate-900 px-5 py-2 text-xs font-bold text-white hover:bg-slate-800 transition-colors shrink-0">
                    Abrir Caja
                </a>
            </div>
        @endif
    </div>

    {{-- Accesos Rápidos del Cajero --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <a href="{{ route('productos.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-slate-400 transition-all flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-slate-900 block">Consultar Catálogo</span>
                <span class="text-xs text-slate-500">Precios y stock de artículos</span>
            </div>
        </a>

        <a href="{{ route('clientes.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-slate-400 transition-all flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-slate-900 block">Cuaderno de Fiados</span>
                <span class="text-xs text-slate-500">Consultar y cobrar deudas</span>
            </div>
        </a>

        <a href="{{ route('ventas.index') }}" class="p-6 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:border-slate-400 transition-all flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                </svg>
            </div>
            <div>
                <span class="text-sm font-bold text-slate-900 block">Tus Ventas del Día</span>
                <span class="text-xs text-slate-500">${{ number_format($ventasHoyCajero, 2, ',', '.') }} cobrados hoy</span>
            </div>
        </a>
    </div>

    {{-- Tus Ventas Recientes --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Tus Últimas Ventas</h3>
            <a href="{{ route('ventas.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Ver todas &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Código</th>
                        <th class="px-6 py-3.5">Hora</th>
                        <th class="px-6 py-3.5">Cliente</th>
                        <th class="px-6 py-3.5">Método</th>
                        <th class="px-6 py-3.5 text-right">Total</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ventasRecientes as $v)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $v->codigo }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $v->created_at->format('H:i') }} hs</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $v->cliente_nombre }}</td>
                        <td class="px-6 py-4 capitalize text-xs text-slate-600">{{ $v->metodo_pago }}</td>
                        <td class="px-6 py-4 text-right font-black text-slate-900">${{ number_format($v->total, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $v->estado === 'completada' ? 'bg-emerald-50 text-emerald-700' : ($v->estado === 'pendiente' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                {{ ucfirst($v->estado) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">No registraste ventas recientemente.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@else

    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    {{-- PANEL DEL ADMINISTRADOR (MÉTRICAS 100% REALES DEL NEGOCIO)                 --}}
    {{-- ══════════════════════════════════════════════════════════════════════════ --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Panel de Control</h1>
            <p class="mt-1 text-sm text-slate-500">Métricas reales consolidadas de ventas, inventario y rentabilidad.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('ventas.create') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition-colors shadow-sm">
                + Nueva Venta
            </a>
            <a href="{{ route('reportes.index') }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                Ver Reportes
            </a>
        </div>
    </div>

    {{-- 4 Métricas Principales (KPIs REALES) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- KPI 1: Ventas Totales --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ventas Cobradas</span>
                <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-black">
                    $
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($ventasTotales, 2, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Hoy: <strong class="text-emerald-600">${{ number_format($ventasHoy, 2, ',', '.') }}</strong></p>
            </div>
        </div>

        {{-- KPI 2: Ganancia Neta Real y Margen --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ganancia Bruta Real</span>
                <div class="h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($gananciaReal, 2, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">Margen real: <strong class="text-indigo-600">{{ $margenGanancia }}%</strong></p>
            </div>
        </div>

        {{-- KPI 3: Valor de Inventario y Stock --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Valor Inventario (Costo)</span>
                <div class="h-9 w-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black text-slate-900">${{ number_format($valorInventario, 2, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $totalProductos }} productos en catálogo</p>
            </div>
        </div>

        {{-- KPI 4: Deuda en la Calle (Fiados) --}}
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Deuda Fiados por Cobrar</span>
                <div class="h-9 w-9 rounded-xl {{ $totalDeuda > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl font-black {{ $totalDeuda > 0 ? 'text-amber-600' : 'text-slate-900' }}">${{ number_format($totalDeuda, 2, ',', '.') }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $clientesConDeuda }} clientes con saldo deudor</p>
            </div>
        </div>

    </div>

    {{-- Gráfico de Ventas Mensuales Reales y Alertas de Stock --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">

        {{-- Gráfico: Últimos 6 meses reales (7 columnas) --}}
        <div class="lg:col-span-7 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Evolución de Ventas Reales (Últimos 6 Meses)</h3>
                    <p class="text-xs text-slate-400">Total recaudado mensual registrado en la base de datos.</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="graficoVentas"></canvas>
            </div>
        </div>

        {{-- Alertas de Stock Urgente (5 columnas) --}}
        <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <h3 class="text-base font-bold text-slate-900">Alertas de Stock</h3>
                    <a href="{{ route('productos.index', ['alerta' => 'critico']) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
                        Ver todo &rarr;
                    </a>
                </div>

                @if ($productosAlerta->isNotEmpty())
                    <div class="space-y-3">
                        @foreach ($productosAlerta as $p)
                        <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/50 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $p->nombre }}</p>
                                <p class="text-[11px] text-slate-400">{{ $p->categoria->nombre ?? 'General' }} &bull; Mín: {{ $p->stock_minimo }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $p->stock <= $p->stock_critico ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $p->stock }} en stock
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-12 text-center text-slate-400 text-xs">
                        ✓ Todos los productos tienen stock saludable por encima del umbral mínimo.
                    </div>
                @endif
            </div>

            <div class="pt-4 border-t border-slate-100 mt-4 flex items-center justify-between">
                <span class="text-xs text-slate-500">{{ $stockCritico }} en nivel crítico / {{ $stockBajo }} en bajo</span>
                <a href="{{ route('compras.create') }}" class="text-xs font-bold text-slate-900 hover:underline">
                    + Cargar Reposición
                </a>
            </div>
        </div>

    </div>

    {{-- Últimas Ventas Reales --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Transacciones Recientes</h3>
            <a href="{{ route('ventas.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">Ver todas &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Código</th>
                        <th class="px-6 py-3.5">Fecha y Hora</th>
                        <th class="px-6 py-3.5">Cliente</th>
                        <th class="px-6 py-3.5">Método</th>
                        <th class="px-6 py-3.5 text-right">Total</th>
                        <th class="px-6 py-3.5 text-center">Estado</th>
                        <th class="px-6 py-3.5 text-right">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ventasRecientes as $v)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-4 font-mono font-bold text-slate-900">{{ $v->codigo }}</td>
                        <td class="px-6 py-4 text-xs text-slate-500">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-800">{{ $v->cliente_nombre }}</td>
                        <td class="px-6 py-4 capitalize text-xs text-slate-600">{{ $v->metodo_pago }}</td>
                        <td class="px-6 py-4 text-right font-black text-slate-900">${{ number_format($v->total, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $v->estado === 'completada' ? 'bg-emerald-50 text-emerald-700' : ($v->estado === 'pendiente' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                                {{ ucfirst($v->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('ventas.show', $v) }}" class="text-xs font-bold text-slate-900 hover:underline">Ticket &rarr;</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400">No hay ventas registradas en el sistema.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script para renderizar Chart.js con datos 100% reales --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('graficoVentas');
            if (!ctx) return;

            const meses = @json($meses);
            const ventas = @json($ventasMensuales);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: meses,
                    datasets: [{
                        label: 'Ventas ($)',
                        data: ventas,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        hoverBackgroundColor: 'rgba(5, 150, 105, 1)',
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '$ ' + context.parsed.y.toLocaleString('es-AR', {minimumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$ ' + value.toLocaleString('es-AR');
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endif

@endsection
