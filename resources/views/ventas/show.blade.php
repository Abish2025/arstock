@extends('layouts.app')

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('ventas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Ventas
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Comprobante de Venta {{ $venta->codigo }}</h1>
        <p class="text-xs text-slate-500">Emitido el {{ $venta->created_at->format('d/m/Y \a \l\a\s H:i') }} hs</p>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
            </svg>
            Imprimir Ticket
        </button>
        <a href="{{ route('ventas.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-colors">
            + Nueva Venta
        </a>
    </div>
</div>

{{-- Tarjeta del Comprobante / Ticket --}}
<div class="max-w-2xl bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-8">

    {{-- Encabezado del Ticket --}}
    <div class="flex items-center justify-between pb-6 border-b border-slate-100">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white font-bold text-base shadow-sm">
                AR
            </div>
            <div>
                <h2 class="text-lg font-bold text-slate-900 leading-tight">ArStock</h2>
                <p class="text-xs text-slate-400">Comprobante de Venta</p>
            </div>
        </div>
        <div class="text-right">
            <span class="text-xs font-mono font-bold text-slate-900 text-sm block">{{ $venta->codigo }}</span>
            <span class="text-[11px] text-slate-400">{{ $venta->created_at->format('d/m/Y H:i') }}</span>
        </div>
    </div>

    {{-- Datos del Cliente y Método de Pago --}}
    <div class="grid grid-cols-2 gap-4 py-6 border-b border-slate-100 text-sm">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Cliente</span>
            <p class="font-bold text-slate-800">{{ $venta->cliente_nombre }}</p>
            @if ($venta->cliente && $venta->cliente->telefono)
                <p class="text-xs text-slate-500">{{ $venta->cliente->telefono }}</p>
            @endif
        </div>
        <div class="text-right">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Forma de Pago</span>
            <div class="flex items-center justify-end gap-2">
                <span class="font-bold text-slate-800 capitalize">{{ $venta->metodo_pago }}</span>
                @if ($venta->estado === 'completada')
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                        Completada
                    </span>
                @elseif ($venta->estado === 'pendiente')
                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200/60">
                        Fiado Pendiente
                    </span>
                @endif
            </div>
            @if ($venta->es_fiado && $venta->cliente)
                <a href="{{ route('clientes.show', $venta->cliente) }}" class="mt-1 text-xs text-emerald-600 hover:underline inline-block">
                    Ver en cuaderno &rarr;
                </a>
            @endif
        </div>
    </div>

    {{-- Detalle de Ítems Vendidos --}}
    <div class="py-6">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase font-semibold text-slate-400 border-b border-slate-100 pb-2">
                <tr>
                    <th class="py-2">Producto</th>
                    <th class="py-2 text-center">Cant.</th>
                    <th class="py-2 text-right">P. Unit.</th>
                    <th class="py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($venta->detalles as $detalle)
                    <tr>
                        <td class="py-3 font-medium text-slate-900">
                            {{ $detalle->producto_nombre }}
                        </td>
                        <td class="py-3 text-center text-slate-600 font-semibold">
                            {{ $detalle->cantidad }}
                        </td>
                        <td class="py-3 text-right text-slate-600">
                            ${{ number_format($detalle->precio_unitario, 0, ',', '.') }}
                        </td>
                        <td class="py-3 text-right font-bold text-slate-900">
                            ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Total Final --}}
    <div class="pt-6 border-t-2 border-slate-900 flex items-baseline justify-between">
        <span class="text-base font-bold text-slate-900">TOTAL:</span>
        <span class="text-3xl font-black text-slate-900">${{ number_format($venta->total, 0, ',', '.') }}</span>
    </div>

    @if ($venta->notas)
        <div class="mt-6 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-500">
            <strong>Notas:</strong> {{ $venta->notas }}
        </div>
    @endif

</div>

@endsection
