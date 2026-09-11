@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('ventas.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Ventas
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Nueva Venta (Punto de Cobro)</h1>
        <p class="text-xs text-slate-500">Selecciona los productos, indica las cantidades y elige el método de cobro.</p>
    </div>
</div>

<form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Columna Izquierda: Catálogo y Selección de Productos (7 columnas) --}}
        <div class="lg:col-span-7 space-y-6">

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-900">Productos Disponibles</h3>
                    <span class="text-xs text-slate-400">Haz clic para agregar al ticket</span>
                </div>

                {{-- Buscador rápido de productos en el cliente --}}
                <div class="relative mb-4">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input type="text" id="buscarProducto" placeholder="Filtrar por nombre o código..."
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
                </div>

                {{-- Grilla de Productos con Stock --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-[480px] overflow-y-auto pr-1" id="listaProductos">
                    @forelse ($productos as $producto)
                        <button type="button"
                                onclick="agregarAlCarrito({{ $producto->id_producto }}, '{{ addslashes($producto->nombre) }}', {{ $producto->precio_venta }}, {{ $producto->stock }})"
                                class="producto-card text-left p-3.5 rounded-xl border border-slate-200 hover:border-slate-900 hover:bg-slate-50 transition-all flex flex-col justify-between group {{ $producto->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                {{ $producto->stock <= 0 ? 'disabled' : '' }}
                                data-nombre="{{ strtolower($producto->nombre) }}"
                                data-codigo="{{ strtolower($producto->codigo) }}">
                            <div>
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-mono text-slate-400">{{ $producto->codigo }}</span>
                                    <span class="text-[11px] px-2 py-0.5 rounded font-semibold {{ $producto->stock > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $producto->stock }} en stock
                                    </span>
                                </div>
                                <h4 class="font-bold text-slate-900 text-sm mt-1 group-hover:text-emerald-600 transition-colors">
                                    {{ $producto->nombre }}
                                </h4>
                                <p class="text-[11px] text-slate-500">{{ $producto->categoria->nombre ?? 'General' }}</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-slate-100 flex items-center justify-between">
                                <span class="font-extrabold text-slate-900 text-base">${{ number_format($producto->precio_venta, 0, ',', '.') }}</span>
                                <span class="text-xs font-semibold text-emerald-600 group-hover:underline">+ Agregar</span>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-2 py-8 text-center text-slate-400 text-sm">
                            No hay productos cargados en el inventario.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Columna Derecha: Ticket de Venta y Cobro (5 columnas) --}}
        <div class="lg:col-span-5 space-y-6">

            <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Ticket de Venta</h3>
                    <button type="button" onclick="vaciarCarrito()" class="text-xs text-rose-600 hover:underline">Vaciar</button>
                </div>

                {{-- Lista de Ítems Agregados --}}
                <div id="itemsCarrito" class="divide-y divide-slate-100 my-4 max-h-72 overflow-y-auto pr-1">
                    <p class="py-8 text-center text-slate-400 text-xs" id="carritoVacio">
                        El ticket está vacío. Haz clic en los productos para agregarlos.
                    </p>
                </div>

                {{-- Total --}}
                <div class="pt-4 border-t border-slate-100 flex items-baseline justify-between mb-6">
                    <span class="text-sm font-semibold text-slate-500">Total a Cobrar:</span>
                    <span class="text-3xl font-black text-slate-900" id="totalMonto">$0</span>
                </div>

                {{-- Método de Pago --}}
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Forma de Pago</label>
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

                    {{-- Selector de Cliente (Obligatorio si es fiado, opcional si es contado) --}}
                    <div id="seccionCliente">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1" id="etiquetaCliente">
                            Cliente (Opcional)
                        </label>
                        <select name="id_cliente" id="selectCliente"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
                            <option value="">Consumidor Final (Sin asignar)</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id_cliente }}">
                                    {{ $cliente->nombre }} (Saldo: ${{ number_format($cliente->saldo, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1" id="ayudaCliente">
                            Si es fiado, es obligatorio elegir a un cliente para anotarlo en su cuaderno.
                        </p>
                    </div>

                    {{-- Notas Opcionales --}}
                    <div>
                        <label class="block text-xs font-semibold uppercase text-slate-600 mb-1">Notas del Ticket (Opcional)</label>
                        <input type="text" name="notas" placeholder="Ej: Entregar por la tarde..."
                               class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-900 focus:border-slate-900 focus:ring-1 focus:ring-slate-900">
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

{{-- Lógica Interactiva del Carrito y Buscador --}}
<script>
    let carrito = {};

    function agregarAlCarrito(id, nombre, precio, stockMaximo) {
        if (stockMaximo <= 0) return;

        if (carrito[id]) {
            if (carrito[id].cantidad < stockMaximo) {
                carrito[id].cantidad++;
            } else {
                alert('No hay más stock disponible para este producto.');
                return;
            }
        } else {
            carrito[id] = { id, nombre, precio, stockMaximo, cantidad: 1 };
        }
        renderCarrito();
    }

    function cambiarCantidad(id, cambio) {
        if (!carrito[id]) return;
        const nuevaCant = carrito[id].cantidad + cambio;

        if (nuevaCant <= 0) {
            delete carrito[id];
        } else if (nuevaCant > carrito[id].stockMaximo) {
            alert('Has alcanzado el límite de stock de este producto.');
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
            contenedor.innerHTML = '<p class="py-8 text-center text-slate-400 text-xs" id="carritoVacio">El ticket está vacío. Haz clic en los productos para agregarlos.</p>';
            totalEl.innerText = '$0';
            btn.disabled = true;
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
                        <p class="text-[11px] text-slate-400">$${item.precio.toLocaleString()} c/u</p>
                        <input type="hidden" name="items[${index}][id_producto]" value="${item.id}">
                        <input type="hidden" name="items[${index}][cantidad]" value="${item.cantidad}">
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="cambiarCantidad(${item.id}, -1)"
                                class="h-6 w-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">-</button>
                        <span class="text-xs font-bold text-slate-800 w-5 text-center">${item.cantidad}</span>
                        <button type="button" onclick="cambiarCantidad(${item.id}, 1)"
                                class="h-6 w-6 rounded-md bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">+</button>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-extrabold text-slate-900 block">$${subtotal.toLocaleString()}</span>
                        <button type="button" onclick="eliminarItem(${item.id})" class="text-[10px] text-rose-500 hover:underline">Quitar</button>
                    </div>
                </div>
            `;
        });

        contenedor.innerHTML = html;
        totalEl.innerText = '$' + total.toLocaleString();
    }

    function cambiarMetodo(metodo) {
        const select = document.getElementById('selectCliente');
        const etiqueta = document.getElementById('etiquetaCliente');
        const ayuda = document.getElementById('ayudaCliente');

        if (metodo === 'fiado') {
            etiqueta.innerHTML = 'Cliente (Obligatorio para Fiado) <span class="text-rose-600">*</span>';
            select.required = true;
            select.classList.add('border-amber-400', 'bg-amber-50/20');
            ayuda.innerHTML = '⚠️ <strong>Obligatorio:</strong> Selecciona el cliente a quien se le anotará la deuda.';
            ayuda.classList.add('text-amber-700');
        } else {
            etiqueta.innerHTML = 'Cliente (Opcional)';
            select.required = false;
            select.classList.remove('border-amber-400', 'bg-amber-50/20');
            ayuda.innerText = 'Si es fiado, es obligatorio elegir a un cliente para anotarlo en su cuaderno.';
            ayuda.classList.remove('text-amber-700');
        }
    }

    // Buscador instantáneo en el catálogo
    document.getElementById('buscarProducto').addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.producto-card');

        cards.forEach(card => {
            const nombre = card.getAttribute('data-nombre');
            const codigo = card.getAttribute('data-codigo');

            if (nombre.includes(term) || codigo.includes(term)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });
</script>

@endsection
