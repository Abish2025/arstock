@php
    $campo = 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all';
    $campoError = 'w-full rounded-xl border border-rose-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500 transition-all';
    $etiqueta = 'mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600';
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

    {{-- Nombre --}}
    <div class="sm:col-span-2">
        <label class="{{ $etiqueta }}">Nombre y Apellido del Cliente *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $cliente->nombre ?? '') }}" placeholder="Ej: Juan Pérez"
               class="{{ $errors->has('nombre') ? $campoError : $campo }}">
        @error('nombre') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Teléfono / WhatsApp --}}
    <div>
        <label class="{{ $etiqueta }}">Teléfono / WhatsApp</label>
        <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" placeholder="Ej: +54 9 11 1234-5678"
               class="{{ $errors->has('telefono') ? $campoError : $campo }}">
        @error('telefono') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Dirección --}}
    <div>
        <label class="{{ $etiqueta }}">Dirección / Ubicación</label>
        <input type="text" name="direccion" value="{{ old('direccion', $cliente->direccion ?? '') }}" placeholder="Ej: Av. San Martín 123"
               class="{{ $errors->has('direccion') ? $campoError : $campo }}">
        @error('direccion') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Límite de Crédito --}}
    <div>
        <label class="{{ $etiqueta }}">Límite Máximo de Fiado (Opcional)</label>
        <div class="relative">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">$</span>
            <input type="number" step="0.01" name="limite_credito" value="{{ old('limite_credito', $cliente->limite_credito ?? '') }}" placeholder="Ej: 50000"
                   class="{{ $errors->has('limite_credito') ? $campoError : $campo }} pl-8">
        </div>
        <p class="mt-1 text-[11px] text-slate-400">Tope máximo que permites que este cliente acumule en deuda.</p>
        @error('limite_credito') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Saldo Inicial (Solo al crear) --}}
    @if (!isset($cliente))
        <div>
            <label class="{{ $etiqueta }}">Deuda Inicial / Saldo Anterior</label>
            <div class="relative">
                <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">$</span>
                <input type="number" step="0.01" name="saldo" value="{{ old('saldo', 0) }}" placeholder="0.00"
                       class="{{ $errors->has('saldo') ? $campoError : $campo }} pl-8">
            </div>
            <p class="mt-1 text-[11px] text-slate-400">Si el cliente ya te debía dinero del cuaderno de papel, anótalo aquí.</p>
            @error('saldo') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
        </div>
    @endif

    {{-- Notas Adicionales --}}
    <div class="sm:col-span-2">
        <label class="{{ $etiqueta }}">Notas o Referencias (Opcional)</label>
        <textarea name="notas" rows="2" placeholder="Ej: Vecino de la esquina, suele pagar los sábados..."
                  class="{{ $errors->has('notas') ? $campoError : $campo }}">{{ old('notas', $cliente->notas ?? '') }}</textarea>
        @error('notas') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

</div>
