@extends('layouts.app')

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Ingresos de Mercadería y Compras</h1>
        <p class="mt-1 text-sm text-slate-500">Registro de recepciones a proveedores, actualización de costos y stock.</p>
    </div>
    <a href="{{ route('compras.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Registrar Ingreso / Compra
    </a>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Recepciones Registradas</span>
        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ $totalCompras }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Total Inversión en Compras</span>
        <h3 class="text-3xl font-bold text-slate-900 mt-2">${{ number_format($totalInvertido, 2, ',', '.') }}</h3>
    </div>
</div>

{{-- Buscador y Tabla --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <form method="GET" class="relative max-w-md">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por N° comprobante o proveedor..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Fecha</th>
                    <th class="px-6 py-3.5">Comprobante</th>
                    <th class="px-6 py-3.5">Proveedor</th>
                    <th class="px-6 py-3.5 text-center">Ítems</th>
                    <th class="px-6 py-3.5 text-right">Total Invertido</th>
                    <th class="px-6 py-3.5">Registrado Por</th>
                    <th class="px-6 py-3.5 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($compras as $c)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 text-xs font-mono text-slate-500">
                        {{ $c->fecha->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 font-mono font-bold text-slate-900">
                        {{ $c->comprobante_numero }}
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-800">
                        {{ $c->proveedor->empresa ?? 'Proveedor General' }}
                    </td>
                    <td class="px-6 py-4 text-center font-bold text-slate-700">
                        {{ $c->detalles->count() }} productos
                    </td>
                    <td class="px-6 py-4 text-right font-black text-slate-900">
                        ${{ number_format($c->total, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $c->usuario->name ?? 'Admin' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('compras.show', $c) }}" class="text-xs font-bold text-slate-900 hover:underline">
                            Ver Detalle &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        No hay ingresos de mercadería registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($compras->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $compras->links() }}
    </div>
    @endif
</div>

@endsection
