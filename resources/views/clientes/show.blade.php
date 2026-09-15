@extends('layouts.app')

@section('content')

{{-- Navegación superior --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Clientes
        </a>
        <div class="mt-2 flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                {{ strtoupper(substr($cliente->nombre, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ $cliente->nombre }}</h1>
                <p class="text-xs text-slate-400 font-mono">CLI-{{ str_pad($cliente->id_cliente, 3, '0', STR_PAD_LEFT) }} • Cuaderno de Cuenta Corriente</p>
            </div>
        </div>
    </div>

    {{-- Botones de contacto y edición --}}
    <div class="flex items-center gap-2">
        @if ($cliente->telefono)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cliente->telefono) }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                WhatsApp
            </a>
        @endif
        <a href="{{ route('clientes.edit', $cliente) }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
            </svg>
            Editar
        </a>
    </div>
</div>

{{-- Panel de Saldo y Formularios Rápidos --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Tarjeta de Saldo Actual --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm flex flex-col justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Saldo Adeudado Actual</span>
            <div class="mt-3 flex items-baseline gap-2">
                <h2 class="text-4xl font-extrabold tracking-tight {{ $cliente->saldo > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    ${{ number_format($cliente->saldo, 0, ',', '.') }}
                </h2>
            </div>
            <div class="mt-2">
                @if ($cliente->saldo <= 0)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Cliente al día (sin deuda)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                        Deuda pendiente de cobro
                    </span>
                @endif
            </div>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-1.5">
            @if ($cliente->limite_credito)
                <p class="flex justify-between">
                    <span>Límite de fiado:</span>
                    <span class="font-bold text-slate-700">${{ number_format($cliente->limite_credito, 0, ',', '.') }}</span>
                </p>
            @endif
            @if ($cliente->direccion)
                <p class="flex justify-between items-center gap-2">
                    <span>Dirección:</span>
                    <span class="font-medium text-slate-700 text-right flex items-center justify-end gap-2">
                        {{ $cliente->direccion }}
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($cliente->direccion) }}" target="_blank"
                           class="inline-flex items-center justify-center h-6 w-6 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:text-emerald-700 transition-colors"
                           title="Ver en Google Maps">
                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                            </svg>
                        </a>
                    </span>
                </p>
            @endif
            @if ($cliente->notas)
                <p class="pt-1 text-slate-400 italic">"{{ $cliente->notas }}"</p>
            @endif
        </div>
    </div>

    {{-- Formulario Rápido: REGISTRAR PAGO / ENTREGA (Descuenta) --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <div class="h-8 w-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                ↓
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Registrar Pago / Entrega</h3>
                <p class="text-[11px] text-slate-500">El cliente entrega dinero (resta a la deuda)</p>
            </div>
        </div>

        <form action="{{ route('clientes.movimiento', $cliente) }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="tipo" value="pago">

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Monto entregado ($)</label>
                <input type="number" step="0.01" name="monto" placeholder="Ej: 5000" required
                       min="0.01" max="99999999.99"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Concepto / Detalle</label>
                <input type="text" name="concepto" value="Entrega en efectivo" required
                       minlength="2" maxlength="255"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-sm hover:bg-emerald-700 transition-all">
                Registrar Pago (-$)
            </button>
        </form>
    </div>

    {{-- Formulario Rápido: ANOTAR FIADO (Suma) --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center gap-2 mb-4">
            <div class="h-8 w-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold text-sm">
                ↑
            </div>
            <div>
                <h3 class="text-sm font-bold text-slate-900">Anotar Compra Fiada</h3>
                <p class="text-[11px] text-slate-500">Compra mercadería a crédito (suma a la deuda)</p>
            </div>
        </div>

        <form action="{{ route('clientes.movimiento', $cliente) }}" method="POST" class="space-y-3">
            @csrf
            <input type="hidden" name="tipo" value="fiado">

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Monto fiado ($)</label>
                <input type="number" step="0.01" name="monto" placeholder="Ej: 3200" required
                       min="0.01" max="99999999.99"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Concepto / Detalle</label>
                <input type="text" name="concepto" value="Compra fiada de almacén" required
                       minlength="2" maxlength="255"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-sm text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
            </div>

            <button type="submit"
                    class="w-full rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold uppercase tracking-wider text-white shadow-sm hover:bg-slate-800 transition-all">
                Anotar Fiado (+$$)
            </button>
        </form>
    </div>

</div>

{{-- Historial de Movimientos de la Cuenta Corriente --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Historial del Cuaderno</h3>
            <p class="text-xs text-slate-500">Registro cronológico de todas las compras fiadas y pagos realizados</p>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Fecha y Hora</th>
                    <th class="px-6 py-3.5">Tipo</th>
                    <th class="px-6 py-3.5">Detalle / Concepto</th>
                    <th class="px-6 py-3.5 text-right">Monto</th>
                    <th class="px-6 py-3.5 text-right">Saldo Anterior</th>
                    <th class="px-6 py-3.5 text-right">Saldo Resultante</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($movimientos as $movimiento)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        {{-- Fecha --}}
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">
                            {{ $movimiento->created_at->format('d/m/Y H:i') }}
                        </td>

                        {{-- Tipo --}}
                        <td class="px-6 py-4">
                            @if ($movimiento->tipo === 'pago')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <span>↓</span> Pago / Entrega
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200/60">
                                    <span>↑</span> Fiado / Compra
                                </span>
                            @endif
                        </td>

                        {{-- Concepto --}}
                        <td class="px-6 py-4 font-medium text-slate-800">
                            {{ $movimiento->concepto }}
                        </td>

                        {{-- Monto --}}
                        <td class="px-6 py-4 text-right font-bold {{ $movimiento->tipo === 'pago' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $movimiento->tipo === 'pago' ? '-' : '+' }}${{ number_format($movimiento->monto, 0, ',', '.') }}
                        </td>

                        {{-- Saldo Anterior --}}
                        <td class="px-6 py-4 text-right text-xs text-slate-400 font-mono">
                            ${{ number_format($movimiento->saldo_anterior, 0, ',', '.') }}
                        </td>

                        {{-- Saldo Resultante --}}
                        <td class="px-6 py-4 text-right font-mono font-bold text-slate-900">
                            ${{ number_format($movimiento->saldo_nuevo, 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <p class="text-sm">No hay movimientos registrados en esta cuenta corriente.</p>
                            <p class="text-xs text-slate-400 mt-1">Usa los formularios de arriba para anotar el primer fiado o entrega.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($movimientos->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $movimientos->links() }}
        </div>
    @endif
</div>

@endsection
