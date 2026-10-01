@extends('layouts.app')

@section('content')

<div class="mb-6">
    <a href="{{ route('compras.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        Volver a Ingresos
    </a>
    <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Registrar Recepción de Mercadería / Compra</h1>
    <p class="text-xs text-slate-500">Al registrar la compra se incrementará automáticamente el stock y se actualizarán los costos de compra.</p>
</div>

<form action="{{ route('compras.store') }}" method="POST" id="formCompra">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Cabecera de Compra (5 columnas) --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">Datos del Proveedor y Comprobante</h3>

                {{-- Proveedor --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Proveedor *</label>
                    <select name="id_proveedor" required
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                        <option value="">Selecciona el proveedor...</option>
                        @foreach ($proveedores as $prov)
                            <option value="{{ $prov->id_proveedor }}" {{ old('id_proveedor') == $prov->id_proveedor ? 'selected' : '' }}>
                                {{ $prov->empresa }} {{ $prov->contacto ? "({$prov->contacto})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- N° Comprobante / Remito --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">N° Comprobante / Remito (Opcional)</label>
                    <input type="text" name="comprobante_numero" value="{{ old('comprobante_numero') }}" placeholder="Ej: REM-004523 o Factura A"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                </div>

                {{-- Fecha --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Fecha de Recepción *</label>
                    <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                </div>

                {{-- Notas --}}
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Observaciones (Opcional)</label>
                    <textarea name="notas" rows="2" placeholder="Ej: Pago contado en entrega..."
                              class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3.5 py-2 text-xs text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">{{ old('notas') }}</textarea>
                </div>

                {{-- Total General --}}
                <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-xs font-bold uppercase text-slate-500">Total Inversión:</span>
                    <span class="text-2xl font-black text-slate-900" id="totalGeneral">$0</span>
                </div>

                <button type="submit" id="btnGuardarCompra" disabled
                        class="w-full rounded-xl bg-slate-900 py-3 text-sm font-bold text-white shadow-md hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    Confirmar e Ingresar Stock
                </button>
            </div>
        </div>

        {{-- Detalle de Productos Recibidos (8 columnas) --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Productos Ingresados</h3>
                        <p class="text-xs text-slate-400">Agrega los productos recibidos y su costo unitario.</p>
                    </div>
                    <button type="button" onclick="agregarFila()" class="rounded-xl bg-slate-100 hover:bg-slate-200 px-3 py-1.5 text-xs font-bold text-slate-700 transition-colors">
                        + Agregar Renglón
                    </button>
                </div>

                <div id="itemsContainer" class="space-y-3">
                    {{-- Filas dinámicas generadas con JS --}}
                </div>
            </div>
        </div>

    </div>
</form>

<script>
    const catalogoProductos = @json($productos);
    let filaIndex = 0;

    function agregarFila(idProducto = '', cantidad = 1, costo = '') {
        const container = document.getElementById('itemsContainer');
        const filaId = `fila_${filaIndex}`;

        let opcionesHtml = '<option value="">Selecciona producto...</option>';
        catalogoProductos.forEach(p => {
            const selected = (p.id_producto == idProducto) ? 'selected' : '';
            opcionesHtml += `<option value="${p.id_producto}" data-costo="${p.precio_compra}" ${selected}>${p.nombre} (Stock actual: ${p.stock})</option>`;
        });

        const filaHtml = `
            <div id="${filaId}" class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/30 flex flex-col sm:flex-row items-center gap-3">
                <div class="w-full sm:flex-1">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Producto</label>
                    <select name="items[${filaIndex}][id_producto]" onchange="alSeleccionarProducto(this, '${filaId}')" required
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-900 focus:border-slate-900 focus:outline-none">
                        ${opcionesHtml}
                    </select>
                </div>

                <div class="w-full sm:w-28">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Cant. Recibida</label>
                    <input type="number" min="1" name="items[${filaIndex}][cantidad]" value="${cantidad}" oninput="calcularTotales()" required
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-900 text-center focus:border-slate-900 focus:outline-none input-cant">
                </div>

                <div class="w-full sm:w-32">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Costo Unit. ($)</label>
                    <input type="number" step="0.01" min="0.01" name="items[${filaIndex}][costo]" value="${costo}" oninput="calcularTotales()" required placeholder="0.00"
                           class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-900 text-right focus:border-slate-900 focus:outline-none input-costo">
                </div>

                <div class="w-full sm:w-28 text-right">
                    <label class="block text-[11px] font-bold text-slate-500 mb-1">Subtotal</label>
                    <span class="text-xs font-black text-slate-900 block pt-2 subtotal-txt">$0</span>
                </div>

                <div class="pt-4 sm:pt-4">
                    <button type="button" onclick="eliminarFila('${filaId}')" class="text-rose-500 hover:text-rose-700 p-1" title="Quitar">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', filaHtml);
        filaIndex++;
        calcularTotales();
    }

    function alSeleccionarProducto(selectEl, filaId) {
        const opt = selectEl.options[selectEl.selectedIndex];
        const costo = opt.getAttribute('data-costo');
        const fila = document.getElementById(filaId);
        const costoInput = fila.querySelector('.input-costo');

        if (costo && (!costoInput.value || costoInput.value == '0')) {
            costoInput.value = costo;
        }
        calcularTotales();
    }

    function eliminarFila(filaId) {
        const fila = document.getElementById(filaId);
        if (fila) fila.remove();
        calcularTotales();
    }

    function calcularTotales() {
        const container = document.getElementById('itemsContainer');
        const filas = container.children;
        let totalGeneral = 0;
        let itemsValidos = 0;

        for (let fila of filas) {
            const cant = parseFloat(fila.querySelector('.input-cant')?.value) || 0;
            const costo = parseFloat(fila.querySelector('.input-costo')?.value) || 0;
            const subtotal = cant * costo;

            const subtotalTxt = fila.querySelector('.subtotal-txt');
            if (subtotalTxt) {
                subtotalTxt.innerText = '$' + subtotal.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            if (cant > 0 && costo > 0) {
                totalGeneral += subtotal;
                itemsValidos++;
            }
        }

        document.getElementById('totalGeneral').innerText = '$' + totalGeneral.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('btnGuardarCompra').disabled = (itemsValidos === 0);
    }

    // Agregar primera fila por defecto al cargar
    document.addEventListener('DOMContentLoaded', () => {
        agregarFila();
    });
</script>

@endsection
