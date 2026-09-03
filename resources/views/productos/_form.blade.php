@php
    $campo = 'w-full rounded-md border border-ink-950/15 bg-white px-3 py-2 text-sm focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500';
    $campoError = 'w-full rounded-md border border-crit-600/50 bg-white px-3 py-2 text-sm focus:border-crit-600 focus:outline-none focus:ring-1 focus:ring-crit-600';
    $etiqueta = 'mb-1.5 block text-sm font-medium text-ink-950/80';
@endphp

<div class="grid grid-cols-1 gap-5 sm:grid-cols-2">

    <div>
        <label class="{{ $etiqueta }}">Código</label>
        <input type="text" name="codigo" value="{{ old('codigo', $producto->codigo ?? '') }}"
               class="{{ $errors->has('codigo') ? $campoError : $campo }}">
        @error('codigo') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $producto->nombre ?? '') }}"
               class="{{ $errors->has('nombre') ? $campoError : $campo }}">
        @error('nombre') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label class="{{ $etiqueta }}">Descripción</label>
        <textarea name="descripcion" rows="2"
                  class="{{ $errors->has('descripcion') ? $campoError : $campo }}">{{ old('descripcion', $producto->descripcion ?? '') }}</textarea>
        @error('descripcion') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Categoría</label>
        <select name="id_categoria" class="{{ $errors->has('id_categoria') ? $campoError : $campo }}">
            <option value="">Sin categoría</option>
            @foreach ($categorias as $categoria)
                <option value="{{ $categoria->id_categoria }}"
                    @selected(old('id_categoria', $producto->id_categoria ?? '') == $categoria->id_categoria)>
                    {{ $categoria->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_categoria') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
    </div>

    <div></div>

    <div>
        <label class="{{ $etiqueta }}">Precio de compra</label>
        <div class="relative">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink-950/40">$</span>
            <input type="number" step="0.01" name="precio_compra" value="{{ old('precio_compra', $producto->precio_compra ?? '') }}"
                   class="{{ $errors->has('precio_compra') ? $campoError : $campo }} pl-6">
        </div>
        @error('precio_compra') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $etiqueta }}">Precio de venta</label>
        <div class="relative">
            <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-sm text-ink-950/40">$</span>
            <input type="number" step="0.01" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta ?? '') }}"
                   class="{{ $errors->has('precio_venta') ? $campoError : $campo }} pl-6">
        </div>
        @error('precio_venta') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2 mt-2 border-t border-ink-950/10 pt-5">
        <p class="mb-3 text-sm font-medium text-ink-950/80">Niveles de stock</p>
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div>
                <label class="{{ $etiqueta }}">Stock actual</label>
                <input type="number" name="stock" value="{{ old('stock', $producto->stock ?? 0) }}"
                       class="{{ $errors->has('stock') ? $campoError : $campo }}">
                @error('stock') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $etiqueta }}">Stock mínimo</label>
                <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $producto->stock_minimo ?? 0) }}"
                       class="{{ $errors->has('stock_minimo') ? $campoError : $campo }}">
                @error('stock_minimo') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="{{ $etiqueta }}">Stock crítico</label>
                <input type="number" name="stock_critico" value="{{ old('stock_critico', $producto->stock_critico ?? 0) }}"
                       class="{{ $errors->has('stock_critico') ? $campoError : $campo }}">
                @error('stock_critico') <p class="mt-1 text-xs text-crit-600">{{ $message }}</p> @enderror
            </div>
        </div>
        <p class="mt-2 text-xs text-ink-950/50">Cuando el stock baje del mínimo verás una alerta; por debajo del crítico se marca en rojo.</p>
    </div>

</div>
