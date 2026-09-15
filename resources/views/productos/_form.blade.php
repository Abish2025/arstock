@php
    $campo = 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all';
    $campoError = 'w-full rounded-xl border border-rose-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500 transition-all';
    $etiqueta = 'mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-600';
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

    <div>
        <label class="{{ $etiqueta }}">Código / SKU *</label>
        <input type="text" name="codigo" value="{{ old('codigo', $producto->codigo ?? '') }}" placeholder="Ej: PRD-001"
               required minlength="1" maxlength="25"
               class="{{ $errors->has('codigo') ? $campoError : $campo }}">
        @error('codigo') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Nombre del producto *</label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}" placeholder="Ej: Arroz 1kg"
               required minlength="2" maxlength="100"
               class="{{ $errors->has('nombre') ? $campoError : $campo }}">
        @error('nombre') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $etiqueta }}">Descripción (Opcional)</label>
        <textarea name="descripcion" rows="2" placeholder="Detalles adicionales del producto..."
                  maxlength="255"
                  class="{{ $errors->has('descripcion') ? $campoError : $campo }}">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
        @error('descripcion') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Categoría</label>
        <select name="id_categoria" class="{{ $errors->has('id_categoria') ? $campoError : $campo }}">
            <option value="">Sin categoría asignada</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id_categoria }}"
                    @selected(old('id_categoria', $producto->id_categoria ?? '') == $categoria->id_categoria)>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_categoria') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Proveedor (Opcional)</label>
        <select name="id_proveedor" class="{{ $errors->has('id_proveedor') ? $campoError : $campo }}">
            <option value="">Sin proveedor asignado</option>
            @foreach ($proveedores as $proveedor)
                <option value="{{ $proveedor->id_proveedor }}"
                    @selected(old('id_proveedor', $producto->id_proveedor ?? '') == $proveedor->id_proveedor)>
                    {{ $proveedor->empresa }}
                </option>
            @endforeach
        </select>
        @error('id_proveedor') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Precio de compra *</label>
        <div class="relative">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">$</span>
            <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '') }}" placeholder="0.00"
                   required min="0" max="99999999.99"
                   class="{{ $errors->has('precio_compra') ? $campoError : $campo }} pl-8">
        </div>
        @error('precio_compra') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Precio de venta *</label>
        <div class="relative">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-semibold text-slate-400">$</span>
            <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta ?? '') }}" placeholder="0.00"
                   required min="0" max="99999999.99"
                   class="{{ $errors->has('precio_venta') ? $campoError : $campo }} pl-8">
        </div>
        <p class="mt-1 text-[11px] text-slate-400">Debe ser igual o mayor al precio de compra.</p>
        @error('precio_venta') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2 mt-2 rounded-xl bg-slate-50/70 p-5 border border-slate-100">
        <p class="mb-4 text-xs font-bold uppercase tracking-wider text-slate-700">Control de Niveles de Stock</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="{{ $etiqueta }}">Stock actual *</label>
                <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}"
                       required min="0" max="999999" step="1"
                       class="{{ $errors->has('stock') ? $campoError : $campo }}">
                @error('stock') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $etiqueta }}">Stock mínimo (Alerta) *</label>
                <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}"
                       required min="0" max="999999" step="1"
                       class="{{ $errors->has('stock_minimo') ? $campoError : $campo }}">
                @error('stock_minimo') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $etiqueta }}">Stock crítico (Urgente) *</label>
                <input type="number" name="stock_critico" value="{{ old('stock_critico', $producto->stock_critico ?? 0) }}"
                       required min="0" max="999999" step="1"
                       class="{{ $errors->has('stock_critico') ? $campoError : $campo }}">
                <p class="mt-1 text-[11px] text-slate-400">No puede ser mayor al stock mínimo.</p>
                @error('stock_critico') <p class="mt-1.5 text-xs font-medium text-rose-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500">El sistema marcará en naranja cuando el stock sea menor al mínimo, y en rojo cuando esté por debajo del crítico.</p>
    </div>

</div>
