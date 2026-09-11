@php
    $campo = 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all';
    $campoError = 'w-full rounded-xl border border-rose-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500 transition-all';
    $etiqueta = 'mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600';
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

    {{-- Empresa --}}
    <div>
        <label class="{{ $etiqueta }}">Nombre de la Empresa o Distribuidora *</label>
        <input type="text" name="empresa" value="{{ old('empresa', $proveedor->empresa ?? '') }}" placeholder="Ej: Distribuidora San Cayetano"
               class="{{ $errors->has('empresa') ? $campoError : $campo }}">
        @error('empresa') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Preventista / Contacto --}}
    <div>
        <label class="{{ $etiqueta }}">Preventista o Vendedor de Contacto</label>
        <input type="text" name="contacto" value="{{ old('contacto', $proveedor->contacto ?? '') }}" placeholder="Ej: Marcos Ramírez"
               class="{{ $errors->has('contacto') ? $campoError : $campo }}">
        @error('contacto') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Teléfono / WhatsApp --}}
    <div>
        <label class="{{ $etiqueta }}">Teléfono / WhatsApp para Pedidos</label>
        <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono ?? '') }}" placeholder="Ej: +54 9 11 3456-7890"
               class="{{ $errors->has('telefono') ? $campoError : $campo }}">
        @error('telefono') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Email --}}
    <div>
        <label class="{{ $etiqueta }}">Correo Electrónico (Opcional)</label>
        <input type="email" name="email" value="{{ old('email', $proveedor->email ?? '') }}" placeholder="pedidos@empresa.com"
               class="{{ $errors->has('email') ? $campoError : $campo }}">
        @error('email') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Días de Visita / Reparto --}}
    <div>
        <label class="{{ $etiqueta }}">Días de Visita o Reparto</label>
        <input type="text" name="dias_visita" value="{{ old('dias_visita', $proveedor->dias_visita ?? '') }}" placeholder="Ej: Martes y Viernes (mañana)"
               class="{{ $errors->has('dias_visita') ? $campoError : $campo }}">
        @error('dias_visita') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- CUIT --}}
    <div>
        <label class="{{ $etiqueta }}">CUIT / Identificación Fiscal (Opcional)</label>
        <input type="text" name="cuit" value="{{ old('cuit', $proveedor->cuit ?? '') }}" placeholder="Ej: 30-12345678-9"
               class="{{ $errors->has('cuit') ? $campoError : $campo }}">
        @error('cuit') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Dirección --}}
    <div class="sm:col-span-2">
        <label class="{{ $etiqueta }}">Dirección del Depósito / Oficina (Opcional)</label>
        <input type="text" name="direccion" value="{{ old('direccion', $proveedor->direccion ?? '') }}" placeholder="Ej: Parque Industrial Lote 14"
               class="{{ $errors->has('direccion') ? $campoError : $campo }}">
        @error('direccion') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    {{-- Notas --}}
    <div class="sm:col-span-2">
        <label class="{{ $etiqueta }}">Notas o Condiciones Comerciales (Opcional)</label>
        <textarea name="notas" rows="2" placeholder="Ej: Pedido mínimo $50.000, pago a 7 días contra entrega..."
                  class="{{ $errors->has('notas') ? $campoError : $campo }}">{{ old('notas', $proveedor->notas ?? '') }}</textarea>
        @error('notas') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

</div>
