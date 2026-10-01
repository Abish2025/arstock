@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('stock.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver al Historial de Stock
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Ajuste Manual de Inventario</h1>
        <p class="text-xs text-slate-500">Registra pérdidas, productos vencidos, roturas o correcciones de conteo físico.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('stock.ajuste.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Selector de Producto --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Producto a Ajustar *</label>
                <select name="id_producto" id="id_producto" required
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                    <option value="">Selecciona un producto...</option>
                    @foreach ($productos as $p)
                        <option value="{{ $p->id_producto }}" {{ (old('id_producto') == $p->id_producto || ($productoSeleccionado && $productoSeleccionado->id_producto == $p->id_producto)) ? 'selected' : '' }}>
                            {{ $p->nombre }} (Código: {{ $p->codigo }} | Stock Actual: {{ $p->stock }})
                        </option>
                    @endforeach
                </select>
                @error('id_producto')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipo de Ajuste --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Tipo de Movimiento *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="tipo" value="merma" {{ old('tipo', 'merma') === 'merma' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">⚠️ Merma / Vencimiento</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Resta unidades por descomposición o fecha vencida.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="tipo" value="rotura" {{ old('tipo') === 'rotura' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">🔨 Rotura / Daño</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Resta unidades rotas o dañadas en depósito o góndola.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="tipo" value="entrada" {{ old('tipo') === 'entrada' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">➕ Entrada Extra</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Suma unidades encontradas en auditoría de depósito.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="tipo" value="ajuste_manual" {{ old('tipo') === 'ajuste_manual' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">🎯 Fijar Conteo Real</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Establece el stock exacto al número ingresado.</span>
                        </div>
                    </label>
                </div>
                @error('tipo')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Cantidad --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Cantidad de Unidades *</label>
                <input type="number" min="1" name="cantidad" value="{{ old('cantidad', 1) }}" required
                       class="w-full rounded-xl border @error('cantidad') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 font-bold focus:border-slate-900 focus:bg-white focus:outline-none">
                @error('cantidad')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Motivo / Justificación --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Motivo / Justificación *</label>
                <textarea name="motivo" rows="2" required placeholder="Explica detalladamente la razón de este ajuste..."
                          class="w-full rounded-xl border @error('motivo') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2 text-xs text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">{{ old('motivo') }}</textarea>
                @error('motivo')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('stock.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900">Cancelar</a>
                <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
                    Registrar Ajuste
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
