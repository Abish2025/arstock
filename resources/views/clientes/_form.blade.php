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
               required minlength="2" maxlength="100"
               class="{{ $errors->has('nombre') ? $campoError : $campo }}">
        @error('nombre') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Teléfono / WhatsApp --}}
    <div>
        <label class="{{ $etiqueta }}">Teléfono / WhatsApp</label>
        <input type="text" name="telefono" value="{{ old('telefono', $cliente->telefono ?? '') }}" placeholder="Ej: 3764123456 o +5491112345678"
               maxlength="15"
               pattern="^\+?[0-9]{10,14}$"
               title="Debe ser un teléfono válido de Argentina (Mínimo 10 números. Ej: 3764123456 o +5493764123456). Solo números y el signo + inicial."
               oninput="this.value = this.value.replace(/[^0-9+]/g, '').replace(/(?!^)\+/g, '');"
               class="{{ $errors->has('telefono') ? $campoError : $campo }}">
        @error('telefono') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Dirección --}}
    <div>
        <label class="{{ $etiqueta }}">Dirección / Ubicación</label>
        <div class="flex gap-2">
            <input type="text" id="input_direccion" name="direccion" value="{{ old('direccion', $cliente->direccion ?? '') }}" placeholder="Ej: Av. Corrientes 1234, Posadas"
                   maxlength="255"
                   class="{{ $errors->has('direccion') ? $campoError : $campo }} flex-1">
            <button type="button" onclick="abrirGoogleMaps()"
                    class="shrink-0 flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-100 hover:text-emerald-700 transition-colors shadow-sm"
                    title="Ver en Google Maps">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                Mapa
            </button>
        </div>
        @error('direccion') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Límite de Crédito --}}
    <div>
        <label class="{{ $etiqueta }}">Límite Máximo de Fiado (Opcional)</label>
        <div class="relative">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">$</span>
            <input type="number" step="0.01" name="limite_credito" value="{{ old('limite_credito', $cliente->limite_credito ?? '') }}" placeholder="Ej: 50000"
                   min="0" max="99999999.99"
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
                       min="0" max="99999999.99"
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
                  maxlength="500"
                  class="{{ $errors->has('notas') ? $campoError : $campo }}">{{ old('notas', $cliente->notas ?? '') }}</textarea>
        @error('notas') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

</div>

<script>
    function abrirGoogleMaps() {
        const inputDir = document.getElementById('input_direccion');
        if (!inputDir) return;
        
        const direccion = inputDir.value.trim();
        if (!direccion) {
            alert('Por favor, ingresá primero una dirección para buscarla en el mapa.');
            return;
        }
        
        // Abre una nueva pestaña con la búsqueda de Google Maps
        window.open('https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(direccion), '_blank');
    }
</script>
