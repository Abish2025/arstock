@extends('layouts.app')

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('compras.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Ingresos
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Comprobante de Ingreso #{{ $compra->id_compra }}</h1>
        <p class="text-xs text-slate-500">Comprobante / Remito: <strong class="text-slate-800">{{ $compra->comprobante_numero }}</strong> | Fecha: {{ $compra->fecha->format('d/m/Y') }}</p>
    </div>

    <div class="flex items-center gap-2">
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 shadow-sm">
            <svg class="h-4 w-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect width="12" height="8" x="6" y="14"/>
            </svg>
            Imprimir Comprobante
        </button>
        <a href="{{ route('compras.create') }}" class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">
            + Nuevo Ingreso
        </a>
    </div>
</div>

<div class="max-w-3xl bg-white rounded-2xl border border-slate-200/80 shadow-sm p-8">
    <div class="flex items-center justify-between pb-6 border-b border-slate-100">
        <div>
            <h2 class="text-lg font-bold text-slate-900">ArStock - Recepción de Proveedor</h2>
            <p class="text-xs text-slate-400">Registrado por: {{ $compra->usuario->name ?? 'Administrador' }}</p>
        </div>
        <div class="text-right">
            <span class="text-xs font-mono font-bold text-slate-900 block">{{ $compra->comprobante_numero }}</span>
            <span class="text-[11px] text-slate-400">{{ $compra->fecha->format('d/m/Y') }}</span>
        </div>
    </div>

    <div class="py-6 border-b border-slate-100 text-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Proveedor</span>
        <p class="font-bold text-slate-900 text-base">{{ $compra->proveedor->empresa ?? 'Proveedor General' }}</p>
        @if ($compra->proveedor)
            <p class="text-xs text-slate-500">Contacto: {{ $compra->proveedor->contacto ?? '-' }} | Tel: {{ $compra->proveedor->telefono ?? '-' }} | CUIT: {{ $compra->proveedor->cuit ?? '-' }}</p>
        @endif
    </div>

    <div class="py-6">
        <table class="w-full text-left text-sm">
            <thead class="text-xs uppercase font-semibold text-slate-400 border-b border-slate-100 pb-2">
                <tr>
                    <th class="py-2">Producto</th>
                    <th class="py-2 text-center">Cant. Recibida</th>
                    <th class="py-2 text-right">Costo Unit.</th>
                    <th class="py-2 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($compra->detalles as $det)
                <tr>
                    <td class="py-3 font-semibold text-slate-900">
                        {{ $det->producto->nombre ?? 'Producto Eliminado' }}
                    </td>
                    <td class="py-3 text-center font-bold text-slate-800">
                        +{{ $det->cantidad }}
                    </td>
                    <td class="py-3 text-right text-slate-600">
                        ${{ number_format($det->costo_unitario, 2, ',', '.') }}
                    </td>
                    <td class="py-3 text-right font-black text-slate-900">
                        ${{ number_format($det->subtotal, 2, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pt-6 border-t-2 border-slate-900 flex items-baseline justify-between">
        <span class="text-base font-bold text-slate-900">TOTAL INVERTIDO:</span>
        <span class="text-3xl font-black text-slate-900">${{ number_format($compra->total, 2, ',', '.') }}</span>
    </div>

    @if ($compra->notas)
        <div class="mt-6 p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-500">
            <strong>Observaciones:</strong> {{ $compra->notas }}
        </div>
    @endif
</div>

@endsection
