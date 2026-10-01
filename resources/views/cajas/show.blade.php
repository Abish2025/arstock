@extends('layouts.app')

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('cajas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Cajas
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Arqueo de Turno #{{ $caja->id_caja }}</h1>
        <p class="text-xs text-slate-500">Cajero: <strong class="text-slate-800">{{ $caja->usuario->name ?? 'Usuario' }}</strong> | Apertura: {{ $caja->fecha_apertura->format('d/m/Y H:i') }} hs</p>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
            </svg>
            Imprimir Arqueo
        </button>
    </div>
</div>

{{-- Tarjetas del Arqueo --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Fondo Inicial</span>
        <h3 class="text-2xl font-black text-slate-900">${{ number_format($caja->monto_apertura, 2, ',', '.') }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Ventas en Efectivo</span>
        <h3 class="text-2xl font-black text-emerald-600">${{ number_format($ventasEfectivo, 2, ',', '.') }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Efectivo Esperado</span>
        <h3 class="text-2xl font-black text-slate-900">${{ number_format($esperadoEfectivo, 2, ',', '.') }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Diferencia / Cierre</span>
        @if ($caja->estado === 'cerrada')
            <h3 class="text-2xl font-black {{ $caja->diferencia == 0 ? 'text-emerald-600' : ($caja->diferencia > 0 ? 'text-blue-600' : 'text-rose-600') }}">
                {{ $caja->diferencia >= 0 ? '+$' : '-$' }}{{ number_format(abs($caja->diferencia), 2, ',', '.') }}
            </h3>
            <span class="text-[11px] text-slate-400">Real: ${{ number_format($caja->monto_cierre_real, 2, ',', '.') }}</span>
        @else
            <span class="text-xs font-bold text-amber-600">Turno en curso</span>
        @endif
    </div>
</div>

{{-- Desglose por Método de Pago --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 mb-8">
    <h3 class="text-base font-bold text-slate-900 mb-4">Desglose de Ventas del Turno</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-slate-50">
            <span class="text-xs text-slate-500 block">💵 Efectivo</span>
            <span class="text-lg font-bold text-slate-900 block mt-1">${{ number_format($ventasEfectivo, 2, ',', '.') }}</span>
        </div>
        <div class="p-4 rounded-xl bg-slate-50">
            <span class="text-xs text-slate-500 block">📲 Transferencia / QR</span>
            <span class="text-lg font-bold text-slate-900 block mt-1">${{ number_format($ventasTransf, 2, ',', '.') }}</span>
        </div>
        <div class="p-4 rounded-xl bg-slate-50">
            <span class="text-xs text-slate-500 block">💳 Débito / Tarjeta</span>
            <span class="text-lg font-bold text-slate-900 block mt-1">${{ number_format($ventasTarjeta, 2, ',', '.') }}</span>
        </div>
        <div class="p-4 rounded-xl bg-slate-50">
            <span class="text-xs text-slate-500 block">📒 Fiado Otorgado</span>
            <span class="text-lg font-bold text-slate-900 block mt-1">${{ number_format($ventasFiado, 2, ',', '.') }}</span>
        </div>
    </div>

    @if ($caja->notas_apertura || $caja->notas_cierre)
        <div class="mt-6 pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-600">
            @if ($caja->notas_apertura)
                <div><strong>Notas de apertura:</strong> {{ $caja->notas_apertura }}</div>
            @endif
            @if ($caja->notas_cierre)
                <div><strong>Notas de cierre:</strong> {{ $caja->notas_cierre }}</div>
            @endif
        </div>
    @endif
</div>

{{-- Tabla de Ventas asociadas a la Caja --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100">
        <h3 class="text-base font-bold text-slate-900">Ventas Registradas en este Turno ({{ $ventasValidas->count() }})</h3>
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
                    <th class="px-6 py-3.5 text-right">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($ventasValidas as $v)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 font-mono font-bold text-slate-900">
                        {{ $v->codigo }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-500">
                        {{ $v->created_at->format('H:i') }} hs
                    </td>
                    <td class="px-6 py-4 font-semibold text-slate-800">
                        {{ $v->cliente_nombre }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="capitalize text-xs font-medium text-slate-700">{{ $v->metodo_pago }}</span>
                    </td>
                    <td class="px-6 py-4 text-right font-black text-slate-900">
                        ${{ number_format($v->total, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('ventas.show', $v) }}" class="text-xs font-bold text-slate-900 hover:underline">
                            Ticket &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-400">
                        Aún no se realizaron ventas en este turno.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
