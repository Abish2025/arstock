@extends('layouts.app')

@section('content')

{{-- Alerta si no hay caja abierta --}}
@if (!$cajaActual)
    <div class="mb-6 flex items-center justify-between rounded-2xl border border-amber-300 bg-amber-50/80 p-4 text-sm text-amber-900 shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-amber-600 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div>
                <span class="font-bold">Turno de caja no iniciado:</span> No tienes una caja abierta. Puedes registrar ventas, pero se recomienda abrir turno para que el arqueo de efectivo sea exacto.
            </div>
        </div>
        <a href="{{ route('cajas.index') }}" class="rounded-xl bg-amber-600 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-700 transition-colors shrink-0">
            Abrir Caja Ahora
        </a>
    </div>
@endif

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('ventas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Ventas
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Punto de Cobro (POS)</h1>
        <p class="text-xs text-slate-500">Escanea códigos de barras, agrega productos y selecciona la modalidad de cobro.</p>
    </div>
</div>

<form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Columna Izquierda: Escáner y Catálogo de Productos (7 columnas) --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Escáner de Código de Barras / Búsqueda Rápida --}}
            <div class="bg-slate-900 text-white rounded-2xl p-5 shadow-lg border border-slate-800">
                <label for="lectorCodigo" class="block text-xs font-bold uppercase tracking-wider text-emerald-400 mb-2 flex items-center gap-2">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M3 5v14M21 5v14M7 5v14M17 5v14M11 5v14M14 5v14"/>
                    </svg>
                    Lector de Código de Barras / Ingreso Manual
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <input type="text" id="lectorCodigo" autofocus placeholder="Escanear código o escribirlo y presionar Enter..."
                               class="w-full rounded-xl border border-slate-700 bg-slate-800/90 py-3 pl-4 pr-10 text-sm text-white placeholder:text-slate-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 font-mono">
                        <span id="barcodeFlash" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-xs font-bold text-emerald-400">✓ Agregado</span>
                    </div>
                    <button type="button" onclick="procesarCodigoBarras()"
                            class="rounded-xl bg-emerald-500 px-5 py-3 text-xs font-bold text-white hover:bg-emerald-600 transition-colors shadow-sm">
                        + Agregar
                    </button>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Al pasar la pistola de código de barras o presionar Enter, el producto se añade automáticamente al ticket.
                </p>
            </div>

            {{-- Catálogo de Productos Disponibles --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Catálogo de Productos</h3>
                    <span class="text-xs text-slate-400">Haz clic para agregar</span>
                </div>

                {{-- Filtro visual --}}
                <div class="relative mb-4">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="text" id="buscarProducto" placeholder="Filtrar catálogo por nombre..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none">
                </div>

                {{-- Grilla de Productos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[380px] overflow-y-auto pr-1" id="listaProductos">
                    @forelse ($productos as $producto)
                        <button type="button"
                                onclick="agregarAlCarrito({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}', '{{ $producto->codigo }}', {{ $producto->precio_venta }}, {{ $producto->stock }})"
                                class="producto-card text-left p-3.5 rounded-xl border border-slate-200 hover:border-slate-900 hover:bg-slate-50 transition-all flex flex-col justify-between group {{ $producto->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $producto->stock <= 0 ? 'disabled' : '' }}
                                data-nombre="{{ strtolower($producto->nombre) }}"
                                data-codigo="{{ strtolower($producto->codigo) }}">
                            <div>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-mono font-bold text-slate-400">{{ $producto->codigo }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded font-semibold {{ $producto->stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $producto->stock }} disp.
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm mt-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $producto->nombre }}
                                </h4>
                                <p class="text-[11px] text-slate-500">{{ $producto->categoria->nombre ?? 'General' }}</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="font-black text-slate-900 text-base">${{ number_format($producto->precio_venta, 2, ',', '.') }}</span>
                                <span class="text-xs font-semibold text-emerald-600 group-hover:underline">+ Sumar</span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-2 py-8 text-center text-slate-400 text-sm">
                            No hay productos registrados en el inventario.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Columna Derecha: Ticket de Venta y Opciones de Cobro (5 columnas) --}}
        <div class="lg:col-span-5 space-y-6">

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Ticket de Venta</h3>
                    <button type="button" onclick="vaciarCarrito()" class="text-xs text-rose-600 hover:underline">Vaciar</button>
                </div>

                {{-- Lista de Ítems Agregados --}}
                <div id="itemsCarrito" class="divide-y divide-slate-100 my-4 max-h-64 overflow-y-auto pr-1">
                    <p class="py-8 text-center text-slate-400 text-xs" id="carritoVacio">
                        El ticket está vacío. Escanea un código o agrega productos del catálogo.
                    </p>
                </div>

                {{-- Total --}}
                <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between mb-6">
                    <span class="text-sm font-semibold text-slate-500">Total a Cobrar:</span>
                    <span class="text-3xl font-black text-slate-900" id="totalMonto">$0,00</span>
                </div>

                {{-- Forma de Pago --}}
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Forma de Pago *</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="metodo_pago" value="efectivo" checked onchange="cambiarMetodo(this.value)" class="text-slate-900 focus:ring-slate-900">
                                <span class="text-xs font-bold text-slate-800">💵 Efectivo</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="metodo_pago" value="transferencia" onchange="cambiarMetodo(this.value)" class="text-slate-900 focus:ring-slate-900">
                                <span class="text-xs font-bold text-slate-800">📲 Transf. / QR</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors">
                                <input type="radio" name="metodo_pago" value="tarjeta" onchange="cambiarMetodo(this.value)" class="text-slate-900 focus:ring-slate-900">
                                <span class="text-xs font-bold text-slate-800">💳 Débito/Tarj.</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-amber-300 bg-amber-50/50 cursor-pointer hover:bg-amber-100/50 transition-colors">
                                <input type="radio" name="metodo_pago" value="fiado" onchange="cambiarMetodo(this.value)" class="text-amber-600 focus:ring-amber-600">
                                <span class="text-xs font-bold text-amber-900">📒 Fiado</span>
                            </label>
                        </div>
                    </div>

                    {{-- Campos Específicos para EFECTIVO (Monto recibido y vuelto) --}}
                    <div id="seccionEfectivo" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <label class="block text-xs font-bold text-slate-700">Monto Recibido en Efectivo ($)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-bold">$</span>
                            <input type="number" step="0.01" name="monto_recibido" id="inputMontoRecibido" oninput="calcularVuelto()" placeholder="0.00"
                                   class="w-full rounded-xl border border-slate-300 bg-white py-2 pl-8 pr-3 text-sm font-bold text-slate-900 focus:border-slate-900 focus:outline-none">
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-semibold text-slate-500">Vuelto a Entregar:</span>
                            <span class="text-lg font-black text-emerald-600" id="vueltoTexto">$0,00</span>
                        </div>
                    </div>

                    {{-- Campos Específicos para TRANSFERENCIA o TARJETA (Referencia de Operación) --}}
                    <div id="seccionReferencia" class="hidden p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2">
                        <label class="block text-xs font-bold text-slate-700" id="labelReferencia">N° Comprobante / Cupón</label>
                        <input type="text" name="referencia_pago" id="inputReferencia" placeholder="Ej: TRF-12345 o Cupón 0042..."
                               class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    {{-- Selector de Cliente --}}
                    <div id="seccionCliente">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1" id="etiquetaCliente">
                            Cliente (Opcional si es contado)
                        </label>
                        <select name="id_cliente" id="selectCliente" onchange="alSeleccionarCliente()"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:outline-none">
                            <option value="" data-saldo="0" data-limite="0">Consumidor Final (Sin asignar)</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}" data-saldo="{{ $cliente->saldo }}" data-limite="{{ $cliente->limite_credito ?? 0 }}">
                                    {{ $cliente->nombre }} (Deuda: ${{ number_format($cliente->saldo, 2, ',', '.') }}{{ $cliente->limite_credito ? ' | Límite: $' . number_format($cliente->limite_credito, 2, ',', '.') : '' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1" id="infoClienteFiado"></p>
                    </div>

                    {{-- Notas Opcionales --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Notas del Ticket (Opcional)</label>
                        <input type="text" name="notas" placeholder="Ej: Retira familiar..."
                               class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 focus:border-slate-900 focus:outline-none">
                    </div>

                    {{-- Botón de Confirmación --}}
                    <button type="submit" id="btnConfirmar" disabled
                            class="w-full rounded-xl bg-slate-900 py-3.5 text-sm font-bold text-white shadow-md hover:bg-slate-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        Confirmar y Cobrar
                    </button>
                </div>
            </div>

        </div>

    </div>
</form>

{{-- Lógica Interactiva del POS --}}
<script>
    const catalogoProductos = @json($productos);
    let carrito = {};

    function agregarAlCarrito(id, nombre, codigo, precio, stockMaximo) {
        if (stockMaximo <= 0) return;

        if (carrito[id]) {
            if (carrito[id].cantidad < stockMaximo) {
                carrito[id].cantidad++;
            } else {
                alert(`No hay más stock disponible para '${nombre}'.`);
                return;
            }
        } else {
            carrito[id] = { id, nombre, codigo, precio, stockMaximo, cantidad: 1 };
        }
        renderCarrito();
    }

    function procesarCodigoBarras() {
        const input = document.getElementById('lectorCodigo');
        const codigoBuscado = input.value.trim().toLowerCase();
        if (!codigoBuscado) return;

        // Buscar producto por código exacto o código SKU
        const prod = catalogoProductos.find(p => p.codigo.toLowerCase() === codigoBuscado || p.nombre.toLowerCase() === codigoBuscado);

        if (prod) {
            if (prod.stock <= 0) {
                alert(`El producto '${prod.nombre}' está agotado (stock 0).`);
            } else {
                agregarAlCarrito(prod.id_producto, prod.nombre, prod.codigo, prod.precio_venta, prod.stock);

                // Feedback visual de escaneo exitoso
                const flash = document.getElementById('barcodeFlash');
                flash.classList.remove('hidden');
                setTimeout(() => flash.classList.add('hidden'), 1200);
            }
        } else {
            alert(`No se encontró ningún producto con el código: "${input.value}"`);
        }

        input.value = '';
        input.focus();
    }

    // Permitir escaneo al presionar Enter en el lector de código
    document.getElementById('lectorCodigo').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            procesarCodigoBarras();
        }
    });

    function cambiarCantidad(id, cambio) {
        if (!carrito[id]) return;
        const nuevaCant = carrito[id].cantidad + cambio;

        if (nuevaCant <= 0) {
            delete carrito[id];
        } else if (nuevaCant > carrito[id].stockMaximo) {
            alert('Has alcanzado el stock disponible de este producto.');
        } else {
            carrito[id].cantidad = nuevaCant;
        }
        renderCarrito();
    }

    function eliminarItem(id) {
        delete carrito[id];
        renderCarrito();
    }

    function vaciarCarrito() {
        carrito = {};
        renderCarrito();
    }

    function renderCarrito() {
        const contenedor = document.getElementById('itemsCarrito');
        const ids = Object.keys(carrito);
        const btn = document.getElementById('btnConfirmar');
        const totalEl = document.getElementById('totalMonto');

        if (ids.length === 0) {
            contenedor.innerHTML = '<p class="py-8 text-center text-slate-400 text-xs" id="carritoVacio">El ticket está vacío. Escanea un código o agrega productos del catálogo.</p>';
            totalEl.innerText = '$0,00';
            btn.disabled = true;
            calcularVuelto();
            return;
        }

        btn.disabled = false;
        let html = '';
        let total = 0;

        ids.forEach((id, index) => {
            const item = carrito[id];
            const subtotal = item.precio * item.cantidad;
            total += subtotal;

            html += `
                <div class="py-3 flex items-center justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold text-slate-800 truncate">${item.nombre}</p>
                        <p class="text-[11px] text-slate-400 font-mono">${item.codigo} &bull; $${item.precio.toLocaleString('es-AR', {minimumFractionDigits: 2})} c/u</p>
                        <input type="hidden" name="items[${index}][id_producto]" value="${item.id}">
                        <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                    </div>
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="cambiarCantidad(${item.id}, -1)"
                                class="h-6 w-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">-</button>
                        <span class="text-xs font-bold text-slate-800 w-6 text-center">${item.cantidad}</span>
                        <button type="button" onclick="cambiarCantidad(${item.id}, 1)"
                                class="h-6 w-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">+</button>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black text-slate-900 block">$${subtotal.toLocaleString('es-AR', {minimumFractionDigits: 2})}</span>
                        <button type="button" onclick="eliminarItem(${item.id})" class="text-[10px] text-rose-500 hover:underline">Quitar</button>
                    </div>
                </div>
            `;
        });

        contenedor.innerHTML = html;
        totalEl.innerText = '$' + total.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        calcularVuelto();
        alSeleccionarCliente();
    }

    function cambiarMetodo(metodo) {
        const seccionEfectivo = document.getElementById('seccionEfectivo');
        const seccionReferencia = document.getElementById('seccionReferencia');
        const labelReferencia = document.getElementById('labelReferencia');
        const selectCliente = document.getElementById('selectCliente');
        const etiquetaCliente = document.getElementById('etiquetaCliente');

        if (metodo === 'efectivo') {
            seccionEfectivo.classList.remove('hidden');
            seccionReferencia.classList.add('hidden');
            etiquetaCliente.innerText = 'Cliente (Opcional si es contado)';
            selectCliente.required = false;
        } else if (metodo === 'transferencia' || metodo === 'tarjeta') {
            seccionEfectivo.classList.add('hidden');
            seccionReferencia.classList.remove('hidden');
            labelReferencia.innerText = (metodo === 'transferencia') ? 'N° Comprobante de Transferencia / QR' : 'N° Cupón / Lote / Últimos 4 dígitos';
            etiquetaCliente.innerText = 'Cliente (Opcional)';
            selectCliente.required = false;
        } else if (metodo === 'fiado') {
            seccionEfectivo.classList.add('hidden');
            seccionReferencia.classList.add('hidden');
            etiquetaCliente.innerText = 'Cliente Obligatorio (Cuaderno de Fiados) *';
            selectCliente.required = true;
        }
        alSeleccionarCliente();
    }

    function calcularVuelto() {
        let total = 0;
        Object.keys(carrito).forEach(id => {
            total += carrito[id].precio * carrito[id].cantidad;
        });

        const montoRecibido = parseFloat(document.getElementById('inputMontoRecibido').value) || 0;
        const vueltoTexto = document.getElementById('vueltoTexto');

        if (montoRecibido >= total && total > 0) {
            const vuelto = montoRecibido - total;
            vueltoTexto.innerText = '$' + vuelto.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            vueltoTexto.className = 'text-lg font-black text-emerald-600';
        } else if (montoRecibido > 0 && montoRecibido < total) {
            vueltoTexto.innerText = 'Faltan $' + (total - montoRecibido).toLocaleString('es-AR', { minimumFractionDigits: 2 });
            vueltoTexto.className = 'text-sm font-bold text-rose-500';
        } else {
            vueltoTexto.innerText = '$0,00';
            vueltoTexto.className = 'text-lg font-black text-slate-400';
        }
    }

    function alSeleccionarCliente() {
        const select = document.getElementById('selectCliente');
        const opt = select.options[select.selectedIndex];
        const info = document.getElementById('infoClienteFiado');
        const metodo = document.querySelector('input[name="metodo_pago"]:checked')?.value;

        if (!opt || !opt.value) {
            info.innerText = (metodo === 'fiado') ? '⚠️ Debes seleccionar un cliente registrado para vender a fiado.' : '';
            info.className = 'text-[11px] text-amber-600 font-bold mt-1';
            return;
        }

        const saldo = parseFloat(opt.getAttribute('data-saldo')) || 0;
        const limite = parseFloat(opt.getAttribute('data-limite')) || 0;

        let totalTicket = 0;
        Object.keys(carrito).forEach(id => {
            totalTicket += carrito[id].precio * carrito[id].cantidad;
        });

        if (limite > 0) {
            const proyectado = saldo + totalTicket;
            const disponible = Math.max(0, limite - saldo);
            if (metodo === 'fiado' && proyectado > limite) {
                info.innerText = `⛔ ¡Límite de crédito superado! Deuda actual: $${saldo.toLocaleString('es-AR')} + Ticket: $${totalTicket.toLocaleString('es-AR')} = $${proyectado.toLocaleString('es-AR')}. Límite: $${limite.toLocaleString('es-AR')}.`;
                info.className = 'text-[11px] text-rose-600 font-bold mt-1';
            } else {
                info.innerText = `Límite: $${limite.toLocaleString('es-AR')} | Disponible para fiar: $${disponible.toLocaleString('es-AR')}`;
                info.className = 'text-[11px] text-slate-500 mt-1';
            }
        } else {
            info.innerText = `Deuda acumulada actual: $${saldo.toLocaleString('es-AR', {minimumFractionDigits: 2})} (Sin límite fijado)`;
            info.className = 'text-[11px] text-slate-500 mt-1';
        }
    }

    // Buscador del catálogo en tiempo real
    document.getElementById('buscarProducto').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const tarjetas = document.querySelectorAll('.producto-card');

        tarjetas.forEach(card => {
            const nombre = card.getAttribute('data-nombre');
            const codigo = card.getAttribute('data-codigo');
            if (nombre.includes(query) || codigo.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

@endsection
