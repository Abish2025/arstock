@extends('layouts.app')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Historial y Ajustes de Stock</h1>
        <p class="mt-1 text-sm text-slate-500">Auditoría completa de movimientos de inventario (ventas, compras, mermas y ajustes).</p>
    </div>
    @if (auth()->user()->isAdmin())
    <a href="{{ route('stock.ajuste') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Nuevo Ajuste / Merma
    </a>
    @endif
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    {{-- Filtros --}}
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row gap-3 justify-between items-center">
        <form method="GET" class="relative w-full sm:max-w-md">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por producto o código..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none">
        </form>

        <form method="GET" class="flex gap-2 w-full sm:w-auto">
            <select name="tipo" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700">
                <option value="">Todos los tipos</option>
                <option value="venta" {{ request('tipo') === 'venta' ? 'selected' : '' }}>Ventas</option>
                <option value="compra" {{ request('tipo') === 'compra' ? 'selected' : '' }}>Compras / Ingreso</option>
                <option value="anulacion_venta" {{ request('tipo') === 'anulacion_venta' ? 'selected' : '' }}>Anulación Venta</option>
                <option value="merma" {{ request('tipo') === 'merma' ? 'selected' : '' }}>Mermas</option>
                <option value="rotura" {{ request('tipo') === 'rotura' ? 'selected' : '' }}>Roturas</option>
                <option value="ajuste_manual" {{ request('tipo') === 'ajuste_manual' ? 'selected' : '' }}>Ajuste Manual</option>
            </select>
        </form>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Fecha</th>
                    <th class="px-6 py-3.5">Producto</th>
                    <th class="px-6 py-3.5">Tipo</th>
                    <th class="px-6 py-3.5 text-center">Cant.</th>
                    <th class="px-6 py-3.5 text-center">Stock Antes</th>
                    <th class="px-6 py-3.5 text-center">Stock Después</th>
                    <th class="px-6 py-3.5">Motivo / Operación</th>
                    <th class="px-6 py-3.5">Usuario</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($movimientos as $m)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 text-xs text-slate-500 font-mono">
                        {{ $m->created_at->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-900">
                        {{ $m->producto->nombre ?? 'Producto Eliminado' }}
                        <span class="block text-[11px] font-normal text-slate-400">{{ $m->producto->codigo ?? '' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $badgeClasses = match($m->tipo) {
                                'venta' => 'bg-rose-50 text-rose-700 border-rose-200',
                                'compra' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'anulacion_venta' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'merma', 'rotura' => 'bg-amber-50 text-amber-700 border-amber-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $badgeClasses }}">
                            {{ ucfirst(str_replace('_', ' ', $m->tipo)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-slate-900">
                        {{ $m->cantidad }}
                    </td>
                    <td class="px-6 py-4 text-center text-slate-500">
                        {{ $m->stock_anterior }}
                    </td>
                    <td class="px-6 py-4 text-center font-bold {{ $m->stock_nuevo < $m->stock_anterior ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $m->stock_nuevo }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-700">
                        {{ $m->motivo }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $m->usuario->name ?? 'Sistema' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">
                        No hay movimientos de inventario registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($movimientos->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $movimientos->links() }}
    </div>
    @endif
</div>

@endsection
