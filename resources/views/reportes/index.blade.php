@extends('layouts.app')

@section('content')

{{-- Encabezado con Filtros por Período --}}
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Reportes de Rendimiento</h1>
        <p class="mt-1 text-sm text-slate-500">Márgenes de ganancia reales, flujo de cobro y auditoría de reposición.</p>
    </div>

    {{-- Filtros de Período y Rango de Fechas --}}
    <form method="GET" class="flex flex-wrap items-center gap-2 bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm">
        <select name="periodo" onchange="toggleFechas(this.value); this.form.submit()" class="rounded-xl border-0 bg-slate-50 px-3 py-2 text-xs font-bold text-slate-700 focus:ring-1 focus:ring-slate-900">
            <option value="hoy" {{ $periodo === 'hoy' ? 'selected' : '' }}>Hoy</option>
            <option value="ayer" {{ $periodo === 'ayer' ? 'selected' : '' }}>Ayer</option>
            <option value="semana" {{ $periodo === 'semana' ? 'selected' : '' }}>Últimos 7 días</option>
            <option value="mes" {{ $periodo === 'mes' ? 'selected' : '' }}>Este Mes</option>
            <option value="anio" {{ $periodo === 'anio' ? 'selected' : '' }}>Este Año</option>
            <option value="todo" {{ $periodo === 'todo' ? 'selected' : '' }}>Histórico Completo</option>
            <option value="personalizado" {{ $periodo === 'personalizado' ? 'selected' : '' }}>Rango Personalizado...</option>
        </select>

        <div id="contenedorFechas" class="{{ $periodo === 'personalizado' ? 'flex' : 'hidden' }} items-center gap-2">
            <input type="date" name="fecha_desde" value="{{ $fechaDesdeInput }}" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs text-slate-700">
            <span class="text-xs text-slate-400">hasta</span>
            <input type="date" name="fecha_hasta" value="{{ $fechaHastaInput }}" class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs text-slate-700">
            <button type="submit" class="rounded-xl bg-slate-900 px-3 py-1.5 text-xs font-bold text-white hover:bg-slate-800">Filtrar</button>
        </div>
    </form>
</div>

{{-- 4 Tarjetas Principales del Período --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Total Ingresos</span>
        <h3 class="text-2xl font-black text-slate-900">${{ number_format($totalIngresos, 2, ',', '.') }}</h3>
        <p class="text-xs text-slate-500 mt-1">{{ $cantidadVentas }} ventas en el período</p>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Costo Mercadería (CMV)</span>
        <h3 class="text-2xl font-black text-slate-700">${{ number_format($totalCosto, 2, ',', '.') }}</h3>
        <p class="text-xs text-slate-500 mt-1">Costo histórico al vender</p>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Ganancia Bruta Real</span>
        <h3 class="text-2xl font-black text-emerald-600">${{ number_format($gananciaBruta, 2, ',', '.') }}</h3>
        <p class="text-xs text-slate-500 mt-1">Ingresos menos CMV</p>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Margen Promedio</span>
        <h3 class="text-2xl font-black text-indigo-600">{{ $margenPorcentaje }}%</h3>
        <p class="text-xs text-slate-500 mt-1">Rentabilidad bruta sobre ventas</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">

    {{-- Desglose por Medio de Pago (5 columnas) --}}
    <div class="lg:col-span-5 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <h3 class="text-base font-bold text-slate-900 mb-1">Ventas por Forma de Cobro</h3>
        <p class="text-xs text-slate-400 mb-6">Distribución del dinero ingresado en el período.</p>

        <div class="space-y-4">
            @forelse ($metodosPago as $metodo)
            <div>
                <div class="flex items-center justify-between text-xs mb-1">
                    <span class="font-bold text-slate-800 capitalize">{{ $metodo->metodo_pago }} ({{ $metodo->transacciones }})</span>
                    <span class="font-black text-slate-900">${{ number_format($metodo->total, 2, ',', '.') }} <span class="text-slate-400 font-normal">({{ $metodo->porcentaje }}%)</span></span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ $metodo->porcentaje }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-center py-8 text-slate-400 text-xs">No hay ventas registradas en este período.</p>
            @endforelse
        </div>
    </div>

    {{-- Top 10 Productos Más Vendidos (7 columnas) --}}
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900">Top 10 Productos Más Vendidos</h3>
            <p class="text-xs text-slate-400">Artículos con mayor volumen y facturación.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">#</th>
                        <th class="px-6 py-3">Producto</th>
                        <th class="px-6 py-3 text-center">Unidades</th>
                        <th class="px-6 py-3 text-right">Recaudado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($topProductos as $idx => $tp)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-6 py-3 text-xs font-bold text-slate-400">{{ $idx + 1 }}</td>
                        <td class="px-6 py-3 font-bold text-slate-900">{{ $tp->producto_nombre }}</td>
                        <td class="px-6 py-3 text-center font-bold text-slate-700">{{ $tp->total_unidades }}</td>
                        <td class="px-6 py-3 text-right font-black text-slate-900">${{ number_format($tp->total_recaudado, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-slate-400 text-xs">No hay datos de productos en este período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Sección: Clientes con Deuda (Cuentas Corrientes) --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-8">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Clientes con Saldo Deudor (Cuaderno de Fiados)</h3>
            <p class="text-xs text-slate-400">Total en la calle pendiente de cobro: <strong class="text-slate-900">${{ number_format($totalDeudaClientes, 2, ',', '.') }}</strong></p>
        </div>
        <a href="{{ route('clientes.index', ['filtro' => 'con_deuda']) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            Ver todos los clientes &rarr;
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3">Cliente</th>
                    <th class="px-6 py-3">Teléfono</th>
                    <th class="px-6 py-3 text-right">Límite Crédito</th>
                    <th class="px-6 py-3 text-right">Deuda Actual</th>
                    <th class="px-6 py-3 text-center">Estado</th>
                    <th class="px-6 py-3 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($clientesDeudores->take(6) as $cd)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-3 font-bold text-slate-900">{{ $cd->nombre }}</td>
                    <td class="px-6 py-3 text-xs text-slate-500">{{ $cd->telefono ?? '-' }}</td>
                    <td class="px-6 py-3 text-right text-xs text-slate-600">
                        {{ $cd->limite_credito ? '$' . number_format($cd->limite_credito, 2, ',', '.') : 'Sin límite' }}
                    </td>
                    <td class="px-6 py-3 text-right font-black text-rose-600">
                        ${{ number_format($cd->saldo, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $cd->estado_deuda === 'limite_excedido' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $cd->estado_deuda === 'limite_excedido' ? 'Límite Superado' : 'Con Saldo' }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('clientes.show', $cd) }}" class="text-xs font-bold text-slate-900 hover:underline">
                            Ver Cuaderno &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs">No hay clientes con saldo deudor pendiente.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Sección: Reposición Urgente de Mercadería --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-900">Reposición Urgente a Proveedores</h3>
            <p class="text-xs text-slate-400">{{ $articulosCriticosCount }} artículos bajo o crítico &bull; Inversión estimada: ${{ number_format($totalInversionReposicion, 2, ',', '.') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reportes.reposicion.imprimir') }}" target="_blank"
               class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm inline-flex items-center gap-2">
                <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
                </svg>
                Imprimir Pedido
            </a>
            <a href="{{ route('compras.create') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white hover:bg-slate-800 transition-colors shadow-sm">
                + Cargar Ingreso
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3">Producto</th>
                    <th class="px-6 py-3">Proveedor</th>
                    <th class="px-6 py-3 text-center">Stock Actual</th>
                    <th class="px-6 py-3 text-center">Mínimo</th>
                    <th class="px-6 py-3 text-center">Sugerido Reponer</th>
                    <th class="px-6 py-3 text-right">Inversión Estimada</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($productosReponer as $pr)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-3 font-bold text-slate-900">
                        {{ $pr->nombre }}
                        <span class="block text-[11px] font-normal text-slate-400">{{ $pr->codigo }}</span>
                    </td>
                    <td class="px-6 py-3 text-xs text-slate-600">{{ $pr->proveedor->empresa ?? 'Sin asignar' }}</td>
                    <td class="px-6 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold {{ $pr->stock <= $pr->stock_critico ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $pr->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-3 text-center text-xs text-slate-500">{{ $pr->stock_minimo }}</td>
                    <td class="px-6 py-3 text-center font-bold text-emerald-600">+{{ $pr->sugerido_reponer }} u.</td>
                    <td class="px-6 py-3 text-right font-black text-slate-900">${{ number_format($pr->costo_reposicion, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400 text-xs">No hay productos en estado de alerta o reposición urgente.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleFechas(valor) {
        const contenedor = document.getElementById('contenedorFechas');
        if (valor === 'personalizado') {
            contenedor.classList.remove('hidden');
            contenedor.classList.add('flex');
        } else {
            contenedor.classList.add('hidden');
            contenedor.classList.remove('flex');
        }
    }
</script>

@endsection
