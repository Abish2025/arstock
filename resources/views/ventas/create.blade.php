@extends('layouts.app')

@section('content')

{{-- Alerta si no hay caja abierta --}}
@if (!$cajaActual)
    <div class="mb-6 flex items-center justify-between rounded-2xl border border-amber-300 bg-amber-50/80 p-4 text-sm text-amber-900 shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <span><span class="font-bold">Turno de caja no iniciado:</span> No tienes una caja abierta. Puedes registrar ventas, pero se recomienda abrir turno para que el arqueo de efectivo sea exacto.</span>
        </div>
        <a href="{{ route('cajas.index') }}" class="rounded-xl bg-amber-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-700 transition-colors shrink-0">
            Abrir Caja Ahora
        </a>
    </div>
@endif

<form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
    @csrf

    {{-- ══════════════════════════════════════════════════════════
         PASO 1 — ESCANEAR PRODUCTOS
    ══════════════════════════════════════════════════════════ --}}
    <div id="pasoVenta">

        {{-- Encabezado --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <a href="{{ route('ventas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Volver a Ventas
                </a>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Nueva Venta</h1>
                <p class="text-xs text-slate-500">Escaneá los productos para agregarlos al ticket.</p>
            </div>
        </div>

        {{-- Lector de código de barras --}}
        <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-lg border border-slate-800 mb-6">
            <label for="lectorCodigo" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-3 flex items-center gap-2">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M3 5v14M21 5v14M7 5v14M17 5v14M11 5v14M14 5v14"/>
                </svg>
                Lector de Código de Barras
            </label>
            <div class="relative">
                <input type="text" id="lectorCodigo" autofocus
                       placeholder="Escaneá o escribí el código y presioná Enter..."
                       class="w-full rounded-xl border border-slate-700 bg-slate-800/90 py-4 pl-5 pr-32 text-sm text-white placeholder:text-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono text-base">
                <span id="barcodeFlash" class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-emerald-400 bg-emerald-900/50 px-2 py-1 rounded-lg">✓ Agregado</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">El producto se agrega automáticamente al presionar Enter. No necesitás tocar ningún botón.</p>
        </div>

        {{-- Ticket --}}
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">

            {{-- Header del ticket --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base font-bold text-slate-900">Ticket</h3>
                <button type="button" onclick="vaciarCarrito()"
                        class="text-xs text-rose-500 hover:text-rose-700 hover:underline font-medium">
                    Vaciar todo
                </button>
            </div>

            {{-- Cabecera de columnas --}}
            <div class="hidden sm:grid grid-cols-12 px-6 py-2 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                <div class="col-span-5">Producto</div>
                <div class="col-span-2 text-right">Precio</div>
                <div class="col-span-3 text-center">Cantidad</div>
                <div class="col-span-2 text-right">Subtotal</div>
            </div>

            {{-- Items --}}
            <div id="itemsCarrito" class="divide-y divide-slate-100 min-h-[120px]">
                <div id="carritoVacio" class="flex flex-col items-center justify-center py-14 text-slate-400">
                    <svg class="h-10 w-10 mb-3 text-slate-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                        <path d="M16 10a4 4 0 0 1-8 0"/>
                    </svg>
                    <p class="text-sm font-medium">El ticket está vacío</p>
                    <p class="text-xs mt-1">Escaneá un código para empezar</p>
                </div>
            </div>

            {{-- Total --}}
            <div class="px-6 py-5 border-t border-slate-200 bg-slate-50/50 flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-600">Total</span>
                <span class="text-4xl font-black text-slate-900" id="totalMonto">$0,00</span>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-between gap-4">
            <a href="{{ route('ventas.index') }}"
               class="px-5 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
            <button type="button" id="btnContinuar" onclick="mostrarCobro()" disabled
                    class="flex items-center gap-2 px-8 py-3 rounded-xl bg-emerald-600 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed transition-all">
                Continuar al cobro
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>

    </div>

    {{-- ══════════════════════════════════════════════════════════
         PASO 2 — COBRO
    ══════════════════════════════════════════════════════════ --}}
    <div id="pasoCobro" class="hidden">

        {{-- Encabezado --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <button type="button" onclick="volverProductos()" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Volver a productos
                </button>
                <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-900">Confirmar Venta</h1>
                <p class="text-xs text-slate-500">Revisá el resumen y seleccioná la forma de pago.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- Columna izquierda: resumen --}}
            <div class="lg:col-span-5">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Resumen de la venta</h3>
                    </div>
                    <div id="resumenCobro" class="divide-y divide-slate-100 px-6"></div>
                    <div class="px-6 py-4 bg-slate-900 flex items-center justify-between">
                        <span class="text-sm font-semibold text-slate-300">Total a cobrar</span>
                        <span class="text-3xl font-black text-white" id="totalCobro">$0,00</span>
                    </div>
                </div>
            </div>

            {{-- Columna derecha: forma de pago --}}
            <div class="lg:col-span-7">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6">

                    {{-- Botones de forma de pago --}}
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Forma de pago</label>
                        <div class="grid grid-cols-2 gap-3">

                            <button type="button" onclick="seleccionarMetodo('efectivo')"
                                    data-metodo="efectivo"
                                    class="metodo-btn flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 border-slate-200 hover:border-slate-900 hover:bg-slate-50 transition-all">
                                <span class="text-3xl">💵</span>
                                <span class="text-sm font-bold text-slate-800">Efectivo</span>
                            </button>

                            <button type="button" onclick="seleccionarMetodo('transferencia')"
                                    data-metodo="transferencia"
                                    class="metodo-btn flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 border-slate-200 hover:border-slate-900 hover:bg-slate-50 transition-all">
                                <span class="text-3xl">📲</span>
                                <span class="text-sm font-bold text-slate-800">Transferencia</span>
                            </button>

                            <button type="button" onclick="seleccionarMetodo('tarjeta')"
                                    data-metodo="tarjeta"
                                    class="metodo-btn flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 border-slate-200 hover:border-slate-900 hover:bg-slate-50 transition-all">
                                <span class="text-3xl">💳</span>
                                <span class="text-sm font-bold text-slate-800">Tarjeta</span>
                            </button>

                            <button type="button" onclick="seleccionarMetodo('fiado')"
                                    data-metodo="fiado"
                                    class="metodo-btn flex flex-col items-center justify-center gap-2 p-4 rounded-2xl border-2 border-amber-200 bg-amber-50/50 hover:border-amber-500 hover:bg-amber-50 transition-all">
                                <span class="text-3xl">📒</span>
                                <span class="text-sm font-bold text-amber-900">Cuenta corriente</span>
                            </button>

                        </div>
                        {{-- Input hidden para el método seleccionado --}}
                        <input type="hidden" name="metodo_pago" id="metodoSeleccionado" value="">
                    </div>

                    {{-- Panel efectivo --}}
                    <div id="panelEfectivo" class="hidden space-y-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Monto recibido</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold text-sm">$</span>
                                <input type="number" step="0.01" name="monto_recibido" id="inputMontoRecibido"
                                       oninput="calcularVuelto()"
                                       placeholder="0,00"
                                       class="w-full rounded-xl border border-slate-300 bg-white py-3 pl-8 pr-4 text-base font-bold text-slate-900 focus:border-slate-900 focus:outline-none">
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-1">
                            <span class="text-sm font-semibold text-slate-600">Vuelto a entregar:</span>
                            <span class="text-2xl font-black text-emerald-600" id="vueltoTexto">$0,00</span>
                        </div>
                    </div>

                    {{-- Panel transferencia / tarjeta --}}
                    <div id="panelReferencia" class="hidden space-y-2 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="block text-xs font-bold text-slate-700" id="labelReferencia">N° Comprobante</label>
                        <input type="text" name="referencia_pago" id="inputReferencia"
                               placeholder="Ej: TRF-12345"
                               class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    {{-- Panel cliente --}}
                    <div id="panelCliente" class="hidden space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500" id="etiquetaCliente">
                            Cliente
                        </label>
                        <select name="id_cliente" id="selectCliente" onchange="actualizarInfoCliente()"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                            <option value="" data-saldo="0" data-limite="0">Consumidor Final</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}"
                                        data-saldo="{{ $cliente->saldo }}"
                                        data-limite="{{ $cliente->limite_credito ?? 0 }}">
                                    {{ $cliente->nombre }} — Deuda: ${{ number_format($cliente->saldo, 0, ',', '.') }}
                                    {{ $cliente->limite_credito ? '| Límite: $' . number_format($cliente->limite_credito, 0, ',', '.') : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs font-semibold" id="infoClienteFiado"></p>
                    </div>

                    {{-- Notas --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Notas (opcional)</label>
                        <input type="text" name="notas" placeholder="Ej: Retira familiar..."
                               class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    {{-- Botón cobrar --}}
                    <button type="submit" id="btnCobrar" disabled
                            class="w-full rounded-2xl py-4 text-base font-black text-white shadow-md disabled:opacity-40 disabled:cursor-not-allowed transition-all"
                            style="background: #1e293b;">
                        Seleccioná una forma de pago
                    </button>

                </div>
            </div>

        </div>

    </div>

</form>

<script>
const catalogoProductos = @json($productos);
let carrito = {};
let metodoActual = null;

// ─── LECTOR ─────────────────────────────────────────────────────────────────

let ultimoTiempo = 0;
let bufferCodigo = '';
let timerLector = null;

document.getElementById('lectorCodigo').addEventListener('keydown', function(e) {
    const ahora = Date.now();
    const diferencia = ahora - ultimoTiempo;
    ultimoTiempo = ahora;

    if (e.key === 'Enter') {
        e.preventDefault();
        procesarCodigoBarras();
        return;
    }

    // Si los caracteres llegan muy rápido (menos de 50ms entre cada uno)
    // es el lector de código de barras, no el teclado humano
    if (diferencia < 50) {
        // Es el lector → acumular caracteres y procesar automáticamente
        clearTimeout(timerLector);
        bufferCodigo = this.value + (e.key.length === 1 ? e.key : '');

        timerLector = setTimeout(() => {
            if (bufferCodigo.trim()) {
                procesarCodigoBarras();
            }
            bufferCodigo = '';
        }, 80);
    }
    // Si tarda más de 50ms entre teclas es escritura manual → esperar Enter
});

function procesarCodigoBarras() {
    const input = document.getElementById('lectorCodigo');
    const codigo = input.value.trim().toLowerCase();
    if (!codigo) return;

    const prod = catalogoProductos.find(p =>
        p.codigo && p.codigo.toLowerCase() === codigo ||
        p.nombre && p.nombre.toLowerCase() === codigo
    );

    if (prod) {
        if (prod.stock <= 0) {
            mostrarAlerta(`"${prod.nombre}" está agotado.`, 'error');
        } else {
            agregarAlCarrito(prod.id_producto, prod.nombre, prod.codigo, prod.precio_venta, prod.stock);
            const flash = document.getElementById('barcodeFlash');
            flash.classList.remove('hidden');
            setTimeout(() => flash.classList.add('hidden'), 1000);
        }
    } else {
        mostrarAlerta(`No se encontró producto con código: "${input.value}"`, 'error');
    }

    input.value = '';
    input.focus();
}

// ─── CARRITO ─────────────────────────────────────────────────────────────────

function agregarAlCarrito(id, nombre, codigo, precio, stockMaximo) {
    if (stockMaximo <= 0) return;

    if (carrito[id]) {
        if (carrito[id].cantidad < stockMaximo) {
            carrito[id].cantidad++;
        } else {
            mostrarAlerta(`Stock máximo alcanzado para "${nombre}".`, 'error');
            return;
        }
    } else {
        carrito[id] = { id, nombre, codigo, precio, stockMaximo, cantidad: 1 };
    }
    renderCarrito();
}

function cambiarCantidad(id, cambio) {
    if (!carrito[id]) return;
    const nueva = carrito[id].cantidad + cambio;
    if (nueva <= 0) {
        delete carrito[id];
    } else if (nueva > carrito[id].stockMaximo) {
        mostrarAlerta('Stock máximo alcanzado.', 'error');
    } else {
        carrito[id].cantidad = nueva;
    }
    renderCarrito();
}

function eliminarItem(id) {
    delete carrito[id];
    renderCarrito();
}

function vaciarCarrito() {
    if (Object.keys(carrito).length === 0) return;
    if (!confirm('¿Vaciar el ticket?')) return;
    carrito = {};
    renderCarrito();
}

function calcularTotal() {
    return Object.values(carrito).reduce((sum, item) => sum + item.precio * item.cantidad, 0);
}

function renderCarrito() {
    const contenedor = document.getElementById('itemsCarrito');
    const vacio = document.getElementById('carritoVacio');
    const btnContinuar = document.getElementById('btnContinuar');
    const totalEl = document.getElementById('totalMonto');
    const ids = Object.keys(carrito);

    if (ids.length === 0) {
        contenedor.innerHTML = '';
        contenedor.appendChild(document.getElementById('carritoVacio') || crearVacio());
        document.getElementById('carritoVacio').classList.remove('hidden');
        totalEl.innerText = '$0,00';
        btnContinuar.disabled = true;
        return;
    }

    let html = '';
    let total = 0;
    let index = 0;

    ids.forEach(id => {
        const item = carrito[id];
        const subtotal = item.precio * item.cantidad;
        total += subtotal;

        html += `
            <div class="grid grid-cols-12 items-center px-6 py-4 gap-2">
                <div class="col-span-5">
                    <p class="text-sm font-bold text-slate-800 leading-tight">${item.nombre}</p>
                    <p class="text-[11px] text-slate-400 font-mono mt-0.5">$${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})} c/u</p>
                    <input type="hidden" name="items[${index}][id_producto]" value="${item.id}">
                    <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                </div>
                <div class="col-span-2 text-right">
                    <span class="text-sm font-semibold text-slate-600">$${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                </div>
                <div class="col-span-3 flex items-center justify-center gap-2">
                    <button type="button" onclick="cambiarCantidad(${item.id}, -1)"
                            class="h-7 w-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center transition-colors">−</button>
                    <span class="text-sm font-black text-slate-900 w-6 text-center">${item.cantidad}</span>
                    <button type="button" onclick="cambiarCantidad(${item.id}, 1)"
                            class="h-7 w-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm flex items-center justify-center transition-colors">+</button>
                </div>
                <div class="col-span-2 text-right">
                    <span class="text-sm font-black text-slate-900 block">$${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                    <button type="button" onclick="eliminarItem(${item.id})" class="text-[10px] text-rose-500 hover:underline">Quitar</button>
                </div>
            </div>
        `;
        index++;
    });

    contenedor.innerHTML = html;
    totalEl.innerText = '$' + total.toLocaleString('es-AR', { minimumFractionDigits: 2 });
    btnContinuar.disabled = false;
}

// ─── PASOS ────────────────────────────────────────────────────────────────────

function mostrarCobro() {
    const total = calcularTotal();
    if (total <= 0) return;

    // Actualizar resumen
    let html = '';
    Object.values(carrito).forEach(item => {
        const subtotal = item.precio * item.cantidad;
        html += `
            <div class="py-3 flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-slate-800">${item.nombre}</p>
                    ${item.cantidad > 1 ? `<p class="text-xs text-slate-400">× ${item.cantidad} unidades</p>` : ''}
                </div>
                <span class="text-sm font-bold text-slate-900 shrink-0">$${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
            </div>
        `;
    });
    document.getElementById('resumenCobro').innerHTML = html;
    document.getElementById('totalCobro').innerText = '$' + total.toLocaleString('es-AR', { minimumFractionDigits: 2 });

    document.getElementById('pasoVenta').classList.add('hidden');
    document.getElementById('pasoCobro').classList.remove('hidden');
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function volverProductos() {
    document.getElementById('pasoCobro').classList.add('hidden');
    document.getElementById('pasoVenta').classList.remove('hidden');
    setTimeout(() => document.getElementById('lectorCodigo').focus(), 100);
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ─── FORMA DE PAGO ────────────────────────────────────────────────────────────

function seleccionarMetodo(metodo) {
    metodoActual = metodo;
    document.getElementById('metodoSeleccionado').value = metodo;

    // Resaltar botón seleccionado
    document.querySelectorAll('.metodo-btn').forEach(btn => {
        btn.classList.remove('border-slate-900', 'bg-slate-900', 'text-white', 'border-amber-500', 'bg-amber-50');
        btn.classList.add('border-slate-200');
        btn.querySelectorAll('span').forEach(s => s.classList.remove('text-white'));
    });

    const btnActivo = document.querySelector(`[data-metodo="${metodo}"]`);
    if (metodo === 'fiado') {
        btnActivo.classList.add('border-amber-500', 'bg-amber-50');
    } else {
        btnActivo.classList.remove('border-slate-200');
        btnActivo.classList.add('border-slate-900', 'bg-slate-900');
        btnActivo.querySelectorAll('span.text-sm').forEach(s => {
            s.classList.add('text-white');
        });
    }

    // Mostrar paneles
    document.getElementById('panelEfectivo').classList.add('hidden');
    document.getElementById('panelReferencia').classList.add('hidden');
    document.getElementById('panelCliente').classList.remove('hidden');

    const etiqueta = document.getElementById('etiquetaCliente');
    const select = document.getElementById('selectCliente');

    if (metodo === 'efectivo') {
        document.getElementById('panelEfectivo').classList.remove('hidden');
        etiqueta.innerText = 'Cliente (opcional)';
        select.required = false;
    } else if (metodo === 'transferencia') {
        document.getElementById('panelReferencia').classList.remove('hidden');
        document.getElementById('labelReferencia').innerText = 'N° Comprobante de transferencia / QR';
        etiqueta.innerText = 'Cliente (opcional)';
        select.required = false;
    } else if (metodo === 'tarjeta') {
        document.getElementById('panelReferencia').classList.remove('hidden');
        document.getElementById('labelReferencia').innerText = 'N° Cupón / Últimos 4 dígitos';
        etiqueta.innerText = 'Cliente (opcional)';
        select.required = false;
    } else if (metodo === 'fiado') {
        etiqueta.innerText = 'Cliente obligatorio *';
        select.required = true;
    }

    // Actualizar botón cobrar
    const labels = {
        efectivo:      '💵 Cobrar',
        transferencia: '📲 Confirmar transferencia',
        tarjeta:       '💳 Confirmar pago con tarjeta',
        fiado:         '📒 Registrar en cuenta corriente',
    };
    const btn = document.getElementById('btnCobrar');
    const total = calcularTotal();
    btn.disabled = false;
    btn.innerText = `${labels[metodo]} — $${total.toLocaleString('es-AR', { minimumFractionDigits: 2 })}`;

    if (metodo === 'fiado') {
        btn.style.background = '#92400e';
    } else if (metodo === 'efectivo') {
        btn.style.background = '#166534';
    } else {
        btn.style.background = '#1e293b';
    }

    actualizarInfoCliente();
}

// ─── VUELTO ───────────────────────────────────────────────────────────────────

function calcularVuelto() {
    const total = calcularTotal();
    const recibido = parseFloat(document.getElementById('inputMontoRecibido').value) || 0;
    const el = document.getElementById('vueltoTexto');

    if (recibido >= total && total > 0) {
        const vuelto = recibido - total;
        el.innerText = '$' + vuelto.toLocaleString('es-AR', { minimumFractionDigits: 2 });
        el.className = 'text-2xl font-black text-emerald-600';
    } else if (recibido > 0 && recibido < total) {
        el.innerText = 'Faltan $' + (total - recibido).toLocaleString('es-AR', { minimumFractionDigits: 2 });
        el.className = 'text-lg font-bold text-rose-500';
    } else {
        el.innerText = '$0,00';
        el.className = 'text-2xl font-black text-slate-400';
    }
}

// ─── CLIENTE ──────────────────────────────────────────────────────────────────

function actualizarInfoCliente() {
    const select = document.getElementById('selectCliente');
    const opt = select.options[select.selectedIndex];
    const info = document.getElementById('infoClienteFiado');

    if (!opt || !opt.value || metodoActual !== 'fiado') {
        info.innerText = metodoActual === 'fiado' ? '⚠️ Seleccioná un cliente para registrar la venta a fiado.' : '';
        info.className = 'text-xs text-amber-600 font-semibold mt-1';
        return;
    }

    const saldo = parseFloat(opt.getAttribute('data-saldo')) || 0;
    const limite = parseFloat(opt.getAttribute('data-limite')) || 0;
    const total = calcularTotal();

    if (limite > 0) {
        const proyectado = saldo + total;
        const disponible = Math.max(0, limite - saldo);
        if (proyectado > limite) {
            info.innerText = `⛔ Límite superado. Deuda: $${saldo.toLocaleString('es-AR')} + Ticket: $${total.toLocaleString('es-AR')} = $${proyectado.toLocaleString('es-AR')} (Límite: $${limite.toLocaleString('es-AR')})`;
            info.className = 'text-xs text-rose-600 font-bold mt-1';
        } else {
            info.innerText = `✅ Disponible para fiar: $${disponible.toLocaleString('es-AR')} (Límite: $${limite.toLocaleString('es-AR')})`;
            info.className = 'text-xs text-emerald-600 font-semibold mt-1';
        }
    } else {
        info.innerText = `Deuda actual: $${saldo.toLocaleString('es-AR', {minimumFractionDigits: 2})} (Sin límite)`;
        info.className = 'text-xs text-slate-500 mt-1';
    }
}

// ─── ALERTAS ──────────────────────────────────────────────────────────────────

function mostrarAlerta(mensaje, tipo) {
    const colores = {
        error: 'bg-rose-100 border-rose-300 text-rose-800',
        ok:    'bg-emerald-100 border-emerald-300 text-emerald-800',
    };
    const div = document.createElement('div');
    div.className = `fixed top-6 right-6 z-50 px-5 py-3 rounded-xl border text-sm font-semibold shadow-lg ${colores[tipo] || colores.error}`;
    div.innerText = mensaje;
    document.body.appendChild(div);
    setTimeout(() => div.remove(), 2500);
}
</script>

@endsection