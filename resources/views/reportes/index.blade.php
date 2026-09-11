@extends('layouts.app')

@section('content')

{{-- Encabezado de Reportes y Filtros --}}
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Reportes & Analíticas</h1>
        <p class="mt-1 text-sm text-slate-500">Métricas de rentabilidad, reposición de stock y rendimiento de ventas.</p>
    </div>

    {{-- Botones de Acción Superior --}}
    <div class="flex items-center gap-2">
        <a href="{{ route('reportes.reposicion.imprimir') }}" target="_blank"
           class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
            </svg>
            Imprimir Lista de Compras
        </a>
    </div>
</div>

{{-- Barra de Selección de Período --}}
<div class="bg-white rounded-2xl p-2 border border-slate-200/80 shadow-sm mb-8 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap items-center gap-1.5 p-1 bg-slate-100/80 rounded-xl">
        <a href="{{ route('reportes.index', ['periodo' => 'hoy']) }}"
           class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $periodo === 'hoy' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            Hoy
        </a>
        <a href="{{ route('reportes.index', ['periodo' => 'semana']) }}"
           class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $periodo === 'semana' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            Últimos 7 días
        </a>
        <a href="{{ route('reportes.index', ['periodo' => 'mes']) }}"
           class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $periodo === 'mes' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            Este Mes
        </a>
        <a href="{{ route('reportes.index', ['periodo' => 'anio']) }}"
           class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $periodo === 'anio' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            Todo el Año
        </a>
        <a href="{{ route('reportes.index', ['periodo' => 'todo']) }}"
           class="px-4 py-2 text-xs font-bold rounded-lg transition-all {{ $periodo === 'todo' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
            Histórico Completo
        </a>
    </div>

    <div class="px-3 text-xs text-slate-400 font-medium">
        Mostrando datos calculados para: <span class="text-slate-700 font-bold capitalize">{{ $periodo === 'semana' ? 'últimos 7 días' : ($periodo === 'anio' ? 'año en curso' : $periodo) }}</span>
    </div>
</div>

{{-- 4 Tarjetas de Métricas Principales (KPIs de Rentabilidad) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- Total Facturado --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Total Facturado</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-bold text-slate-900">${{ number_format($totalIngresos, 0, ',', '.') }}</h3>
            <p class="mt-1 text-xs text-slate-400">{{ $cantidadVentas }} ventas registradas</p>
        </div>
    </div>

    {{-- Costo de Mercadería --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Costo de Mercadería</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-bold text-slate-700">${{ number_format($totalCosto, 0, ',', '.') }}</h3>
            <p class="mt-1 text-xs text-slate-400">Inversión en los productos vendidos</p>
        </div>
    </div>

    {{-- Ganancia Estimada --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Ganancia Bruta</span>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                {{ $margenPorcentaje }}% Margen
            </span>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-bold text-emerald-600">${{ number_format($gananciaBruta, 0, ',', '.') }}</h3>
            <p class="mt-1 text-xs text-slate-400">Ingresos menos costo de reposición</p>
        </div>
    </div>

    {{-- Reposición Urgente --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">A Reponer Urgente</span>
            <div class="h-10 w-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-2xl sm:text-3xl font-bold {{ $articulosCriticosCount > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                {{ $articulosCriticosCount }}
            </h3>
            <p class="mt-1 text-xs text-slate-400">Inversión estimada: ${{ number_format($totalInversionReposicion, 0, ',', '.') }}</p>
        </div>
    </div>

</div>

{{-- Fila Secundaria: Métodos de Pago y Top Productos Más Vendidos --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Desglose por Método de Pago (1 columna) --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Métodos de Cobro</h2>
            <p class="text-xs text-slate-500 mt-0.5">Distribución de ingresos en el período</p>

            <div class="mt-6 space-y-4">
                @forelse ($metodosPago as $metodo)
                    <div>
                        <div class="flex justify-between items-center text-sm font-medium mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="capitalize text-slate-800 font-semibold">{{ $metodo->metodo_pago }}</span>
                                <span class="text-xs text-slate-400">({{ $metodo->transacciones }} transacciones)</span>
                            </div>
                            <span class="font-bold text-slate-900">${{ number_format($metodo->total, 0, ',', '.') }} ({{ $metodo->porcentaje }}%)</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $metodo->metodo_pago === 'efectivo' ? 'bg-emerald-500' : ($metodo->metodo_pago === 'transferencia' ? 'bg-blue-500' : ($metodo->metodo_pago === 'fiado' ? 'bg-amber-500' : 'bg-indigo-500')) }}"
                                 style="width: {{ $metodo->porcentaje }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic py-6 text-center">No hay ventas registradas en este período.</p>
                @endforelse
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Total Recaudado:</span>
            <span class="font-bold text-slate-700">${{ number_format($totalIngresos, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Ranking de Productos Más Vendidos (2 columnas) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Productos Más Vendidos (Top 10)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Artículos de mayor rotación y recaudación</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 w-12 text-center">#</th>
                            <th class="px-6 py-3.5">Producto</th>
                            <th class="px-6 py-3.5 text-center">Unidades Vendidas</th>
                            <th class="px-6 py-3.5 text-right">Recaudación Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($topProductos as $idx => $prod)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full text-xs font-bold {{ $idx === 0 ? 'bg-amber-100 text-amber-800' : ($idx === 1 ? 'bg-slate-200 text-slate-700' : ($idx === 2 ? 'bg-amber-50 text-amber-700' : 'text-slate-400')) }}">
                                        {{ $idx + 1 }}
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 font-semibold text-slate-900">
                                    {{ $prod->producto_nombre }}
                                </td>
                                <td class="px-6 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                        {{ $prod->total_unidades }} un.
                                    </span>
                                </td>
                                <td class="px-6 py-3.5 text-right font-bold text-slate-900">
                                    ${{ number_format($prod->total_recaudado, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-xs text-slate-400 italic">
                                    No se registraron ventas de productos en este período seleccionado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- Tabla de Reposición Urgente a Proveedores --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-slate-900">Alerta de Reposición a Proveedores</h2>
                @if ($articulosCriticosCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60">
                        {{ $articulosCriticosCount }} urgentes
                    </span>
                @endif
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Productos con stock por debajo del límite mínimo configurado.</p>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-xs text-slate-500 font-medium">Inversión necesaria: <strong class="text-slate-900 font-bold">${{ number_format($totalInversionReposicion, 0, ',', '.') }}</strong></span>
            <a href="{{ route('reportes.reposicion.imprimir') }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-2 text-xs font-semibold text-white hover:bg-slate-800 transition-colors">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
                </svg>
                Imprimir Orden de Reposición
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Producto</th>
                    <th class="px-6 py-4">Proveedor Habitual</th>
                    <th class="px-6 py-4 text-center">Stock Actual / Mínimo</th>
                    <th class="px-6 py-4 text-center">Pedir Sugerido</th>
                    <th class="px-6 py-4 text-right">Costo Estimado</th>
                    <th class="px-6 py-4 text-right">Contacto Rápido</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($productosReponer as $prod)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        {{-- Producto --}}
                        <td class="px-6 py-4">
                            <div class="font-semibold text-slate-900">{{ $prod->nombre }}</div>
                            <div class="text-xs text-slate-400 font-mono">Cód: {{ $prod->codigo ?? '—' }} • {{ $prod->categoria->nombre ?? 'Sin categoría' }}</div>
                        </td>

                        {{-- Proveedor --}}
                        <td class="px-6 py-4">
                            @if ($prod->proveedor)
                                <a href="{{ route('proveedores.show', $prod->proveedor) }}" class="font-semibold text-slate-800 hover:text-emerald-600 transition-colors">
                                    {{ $prod->proveedor->empresa }}
                                </a>
                                @if ($prod->proveedor->dias_visita)
                                    <div class="text-[11px] text-slate-400">Reparto: {{ $prod->proveedor->dias_visita }}</div>
                                @endif
                            @else
                                <span class="text-xs text-slate-400 italic">No asignado</span>
                            @endif
                        </td>

                        {{-- Stock Actual vs Mínimo --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="font-bold {{ $prod->stock <= $prod->stock_critico ? 'text-rose-600' : 'text-amber-600' }}">
                                    {{ $prod->stock }}
                                </span>
                                <span class="text-slate-400">/</span>
                                <span class="text-xs text-slate-500 font-medium">{{ $prod->stock_minimo }} min</span>
                            </div>
                        </td>

                        {{-- Pedir Sugerido --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200/60">
                                +{{ $prod->sugerido_reponer }} un.
                            </span>
                        </td>

                        {{-- Costo Estimado --}}
                        <td class="px-6 py-4 text-right">
                            <div class="font-bold text-slate-900">${{ number_format($prod->costo_reposicion, 0, ',', '.') }}</div>
                            <div class="text-[11px] text-slate-400">a ${{ number_format($prod->precio_compra, 0, ',', '.') }} c/u</div>
                        </td>

                        {{-- Botón WhatsApp / Pedido --}}
                        <td class="px-6 py-4 text-right">
                            @if ($prod->proveedor && $prod->proveedor->telefono)
                                @php
                                    $telefonoLimpio = preg_replace('/[^0-9]/', '', $prod->proveedor->telefono);
                                    $mensajeWa = rawurlencode("Hola {$prod->proveedor->contacto}, te escribo de ARStock para pedirte {$prod->sugerido_reponer} unidades de {$prod->nombre}. ¿Podrán incluirlo en el próximo reparto?");
                                @endphp
                                <a href="https://wa.me/{{ $telefonoLimpio }}?text={{ $mensajeWa }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors"
                                   title="Hacer pedido por WhatsApp">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    Pedir por WhatsApp
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">Sin teléfono</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center">
                            <div class="inline-flex h-10 w-10 rounded-full bg-emerald-50 text-emerald-600 items-center justify-center mb-2">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-900">¡Inventario en niveles óptimos!</p>
                            <p class="text-xs text-slate-400 mt-0.5">No hay productos que requieran reposición urgente en este momento.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
