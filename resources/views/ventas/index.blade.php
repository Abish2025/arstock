@extends('layouts.app')

@section('content')

{{-- Encabezado de Ventas --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Ventas</h1>
        <p class="mt-1 text-sm text-slate-500">Gestiona tus transacciones de venta y cobros diarios.</p>
    </div>
    <a href="{{ route('ventas.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all duration-150">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nueva Venta
    </a>
</div>

{{-- 4 Tarjetas de Métricas (KPIs de Ventas como en Figma) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    {{-- Total Ingresos --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Total Ingresos</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <span class="text-base font-bold">$</span>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">${{ number_format($totalIngresos, 0, ',', '.') }}</h3>
        </div>
    </div>

    {{-- Transacciones --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Transacciones</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="16" height="20" x="4" y="2" rx="2"/><line x1="8" x2="16" y1="6" y2="6"/><line x1="8" x2="16" y1="10" y2="10"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalTransacciones }}</h3>
        </div>
    </div>

    {{-- Completadas --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Completadas</span>
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-emerald-600">{{ $totalCompletadas }}</h3>
        </div>
    </div>

    {{-- Pendientes / Fiados --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Pendientes (Fiados)</span>
            <div class="h-10 w-10 rounded-xl {{ $totalPendientes > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold {{ $totalPendientes > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $totalPendientes }}</h3>
        </div>
    </div>

</div>

{{-- Contenedor Principal: Buscador y Tabla --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

    {{-- Barra de búsqueda --}}
    <div class="p-5 border-b border-slate-100">
        <form method="GET" class="relative max-w-md">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por código, cliente o método..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
        </form>
    </div>

    {{-- Tabla de Ventas --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Fecha</th>
                    <th class="px-6 py-4">Cliente</th>
                    <th class="px-6 py-4">Productos</th>
                    <th class="px-6 py-4">Método</th>
                    <th class="px-6 py-4 text-right">Total</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($ventas as $venta)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        {{-- Código --}}
                        <td class="px-6 py-4 font-mono font-bold text-xs text-slate-900">
                            {{ $venta->codigo }}
                        </td>

                        {{-- Fecha --}}
                        <td class="px-6 py-4 text-slate-500 text-xs font-medium">
                            {{ $venta->created_at->format('d/m/Y H:i') }}
                        </td>

                        {{-- Cliente --}}
                        <td class="px-6 py-4">
                            @if ($venta->id_cliente)
                                <a href="{{ route('clientes.show', $venta->id_cliente) }}" class="font-semibold text-slate-900 hover:text-emerald-600 transition-colors">
                                    {{ $venta->cliente_nombre }}
                                </a>
                            @else
                                <span class="text-slate-700 font-medium">{{ $venta->cliente_nombre }}</span>
                            @endif
                        </td>

                        {{-- Productos --}}
                        <td class="px-6 py-4 text-slate-600 text-xs max-w-xs truncate">
                            {{ $venta->resumen_productos }}
                        </td>

                        {{-- Método de Pago --}}
                        <td class="px-6 py-4 text-xs font-medium">
                            @php
                                $metodo = [
                                    'efectivo'      => ['label' => 'Efectivo', 'color' => 'text-slate-700'],
                                    'transferencia' => ['label' => 'Transferencia', 'color' => 'text-blue-700'],
                                    'tarjeta'       => ['label' => 'Tarjeta', 'color' => 'text-purple-700'],
                                    'fiado'         => ['label' => 'Fiado', 'color' => 'text-amber-700 font-bold'],
                                ][$venta->metodo_pago] ?? ['label' => ucfirst($venta->metodo_pago), 'color' => 'text-slate-700'];
                            @endphp
                            <span class="{{ $metodo['color'] }}">{{ $metodo['label'] }}</span>
                        </td>

                        {{-- Total --}}
                        <td class="px-6 py-4 text-right font-bold text-slate-900 text-sm">
                            ${{ number_format($venta->total, 0, ',', '.') }}
                        </td>

                        {{-- Estado --}}
                        <td class="px-6 py-4 text-center">
                            @if ($venta->estado === 'completada')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Completada
                                </span>
                            @elseif ($venta->estado === 'pendiente')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                    Pendiente
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                    Anulada
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('ventas.show', $venta) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Ver Comprobante">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                <form action="{{ route('ventas.destroy', $venta) }}" method="POST"
                                      onsubmit="return confirm('¿Anular esta venta? El stock se devolverá al inventario.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Anular venta">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                            <p class="text-sm">No se han registrado ventas todavía.</p>
                            <a href="{{ route('ventas.create') }}" class="mt-2 inline-block font-semibold text-emerald-600 hover:text-emerald-700">
                                + Registrar la primera venta
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($ventas->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $ventas->links() }}
        </div>
    @endif

</div>

@endsection
