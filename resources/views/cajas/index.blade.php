@extends('layouts.app')

@section('content')

{{-- Encabezado --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Control de Caja y Turnos</h1>
        <p class="mt-1 text-sm text-slate-500">Apertura, arqueos en vivo y cierres de turno de cajeros.</p>
    </div>
    @if ($cajaActual)
        <a href="{{ route('ventas.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 transition-all">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
            </svg>
            Ir al Punto de Cobro
        </a>
    @endif
</div>

{{-- Estado de la Caja Actual del Usuario --}}
<div class="mb-8">
    @if ($cajaActual)
        <div class="bg-white rounded-2xl border-2 border-emerald-500/40 p-6 sm:p-8 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <span class="relative flex h-3.5 w-3.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                    </span>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Caja de {{ auth()->user()->name }} (Abierta)</h2>
                        <p class="text-xs text-slate-500">Abierta hoy a las {{ $cajaActual->fecha_apertura->format('H:i') }} hs — Fondo Inicial: <strong class="text-slate-800">${{ number_format($cajaActual->monto_apertura, 2, ',', '.') }}</strong></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('cajas.show', $cajaActual) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                        Ver Detalle / Arqueo
                    </a>
                </div>
            </div>

            {{-- Resumen en vivo de transacciones del turno --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 block">💵 Ventas Efectivo</span>
                    <span class="text-xl font-black text-slate-900 mt-1 block">${{ number_format($resumenActual['efectivo'], 2, ',', '.') }}</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 block">📲 Transferencia / QR</span>
                    <span class="text-xl font-black text-slate-900 mt-1 block">${{ number_format($resumenActual['transferencia'], 2, ',', '.') }}</span>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-xs font-semibold text-slate-500 block">💳 Débito / Tarjeta</span>
                    <span class="text-xl font-black text-slate-900 mt-1 block">${{ number_format($resumenActual['tarjeta'], 2, ',', '.') }}</span>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200/60">
                    <span class="text-xs font-bold text-emerald-800 block">Total Efectivo en Caja</span>
                    <span class="text-xl font-black text-emerald-700 mt-1 block">${{ number_format($resumenActual['esperado_caja'], 2, ',', '.') }}</span>
                    <span class="text-[10px] text-emerald-600 block mt-0.5">(Fondo inicial + ventas)</span>
                </div>
            </div>

            {{-- Formulario para Cierre de Caja --}}
            <div class="mt-8 pt-6 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 mb-3">Cerrar este turno de caja</h3>
                <form action="{{ route('cajas.cerrar', $cajaActual) }}" method="POST"
                      onsubmit="return confirm('¿Confirmas el cierre del turno? Asegúrate de haber contado el dinero físico en caja.');"
                      class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
                    @csrf
                    <div class="sm:col-span-4">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Efectivo Real Contado en Caja ($) *</label>
                        <input type="number" step="0.01" name="monto_cierre_real" required placeholder="0.00"
                               class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 font-bold focus:border-slate-900 focus:outline-none">
                    </div>
                    <div class="sm:col-span-5">
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Notas de Cierre (Opcional)</label>
                        <input type="text" name="notas_cierre" placeholder="Ej: Faltante de $50 por vuelto..."
                               class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>
                    <div class="sm:col-span-3">
                        <button type="submit" class="w-full rounded-xl bg-rose-600 py-2.5 text-sm font-bold text-white hover:bg-rose-700 transition-colors shadow-sm">
                            Arqueo y Cerrar Caja
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        {{-- Caja Cerrada: Formulario de Apertura --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
            <div class="max-w-xl">
                <div class="flex items-center gap-3 mb-2">
                    <span class="h-3.5 w-3.5 rounded-full bg-slate-300"></span>
                    <h2 class="text-lg font-bold text-slate-900">Tu caja se encuentra actualmente cerrada</h2>
                </div>
                <p class="text-xs text-slate-500 mb-6">Para comenzar a cobrar y asociar las ventas a tu turno, ingresa el dinero en efectivo que tienes en el cajón.</p>

                <form action="{{ route('cajas.abrir') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Monto Inicial en Efectivo (Fondo de Cambio) *</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">$</span>
                            <input type="number" step="0.01" name="monto_apertura" value="{{ old('monto_apertura', '0') }}" required
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-8 pr-4 text-base font-bold text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Notas de Apertura (Opcional)</label>
                        <input type="text" name="notas_apertura" placeholder="Ej: Billetes chicos para cambio..."
                               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2 text-xs text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                    </div>
                    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white shadow-md hover:bg-slate-800 transition-all">
                        🔓 Abrir Turno de Caja
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>

{{-- Historial de Turnos de Caja --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-base font-bold text-slate-900">Historial de Turnos Anteriores</h3>
        <span class="text-xs text-slate-400">{{ $historialCajas->total() }} registros</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Cajero</th>
                    <th class="px-6 py-3.5">Apertura</th>
                    <th class="px-6 py-3.5">Cierre</th>
                    <th class="px-6 py-3.5 text-right">Fondo Inicial</th>
                    <th class="px-6 py-3.5 text-right">Esperado</th>
                    <th class="px-6 py-3.5 text-right">Real Contado</th>
                    <th class="px-6 py-3.5 text-right">Diferencia</th>
                    <th class="px-6 py-3.5 text-center">Estado</th>
                    <th class="px-6 py-3.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($historialCajas as $h)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-slate-900">
                        {{ $h->usuario->name ?? 'Usuario' }}
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600">
                        {{ $h->fecha_apertura->format('d/m/Y H:i') }} hs
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-600">
                        {{ $h->fecha_cierre ? $h->fecha_cierre->format('d/m/Y H:i') . ' hs' : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-slate-800">
                        ${{ number_format($h->monto_apertura, 2, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 text-right font-semibold text-slate-800">
                        {{ $h->monto_cierre_esperado !== null ? '$' . number_format($h->monto_cierre_esperado, 2, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right font-bold text-slate-900">
                        {{ $h->monto_cierre_real !== null ? '$' . number_format($h->monto_cierre_real, 2, ',', '.') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-right font-bold">
                        @if ($h->diferencia !== null)
                            @if ($h->diferencia == 0)
                                <span class="text-emerald-600">$0,00 (Exacto)</span>
                            @elseif ($h->diferencia > 0)
                                <span class="text-blue-600">+${{ number_format($h->diferencia, 2, ',', '.') }} (Sobrante)</span>
                            @else
                                <span class="text-rose-600">-${{ number_format(abs($h->diferencia), 2, ',', '.') }} (Faltante)</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $h->estado === 'abierta' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                            {{ ucfirst($h->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('cajas.show', $h) }}" class="text-xs font-bold text-slate-900 hover:underline">
                            Ver Arqueo &rarr;
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-12 text-center text-slate-400">
                        No hay turnos de caja registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($historialCajas->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $historialCajas->links() }}
    </div>
    @endif
</div>

@endsection
