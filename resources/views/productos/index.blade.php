@extends('layouts.app')

@section('content')

{{-- Encabezado de Productos --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Productos e Inventario</h1>
        <p class="mt-1 text-sm text-slate-500">Consulta y gestión de artículos, precios y niveles de stock.</p>
    </div>
    @if (auth()->user()->isAdmin())
    <div class="flex gap-2">
        <a href="{{ route('stock.ajuste') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 transition-all">
            Ajustar Stock
        </a>
        <a href="{{ route('productos.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Nuevo Producto
        </a>
    </div>
    @endif
</div>

{{-- Tarjetas de Resumen (KPIs) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 {{ auth()->user()->isAdmin() ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-5 mb-8">

    {{-- Total Productos --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Total Artículos</span>
        <h3 class="text-2xl font-black text-slate-900">{{ $totalProductos }}</h3>
        <p class="text-xs text-slate-500 mt-1">Registrados en catálogo</p>
    </div>

    {{-- Stock Bajo --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Stock Bajo</span>
        <h3 class="text-2xl font-black {{ $stockBajo > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $stockBajo }}</h3>
        <p class="text-xs text-slate-500 mt-1">&le; stock mínimo fijado</p>
    </div>

    {{-- Stock Crítico --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Stock Crítico</span>
        <h3 class="text-2xl font-black {{ $stockCritico > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $stockCritico }}</h3>
        <p class="text-xs text-slate-500 mt-1">Necesita reposición urgente</p>
    </div>

    {{-- Valor del Inventario (Solo Admin) --}}
    @if (auth()->user()->isAdmin())
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase text-slate-400 block mb-1">Valoración de Stock</span>
        <h3 class="text-2xl font-black text-slate-900">${{ number_format($valorInventario, 2, ',', '.') }}</h3>
        <p class="text-xs text-slate-500 mt-1">Costo total inmovilizado</p>
    </div>
    @endif

</div>

{{-- Contenedor Principal: Buscador, Filtros de Alerta y Tabla --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

    {{-- Filtros y Buscador --}}
    <div class="p-5 border-b border-slate-100 flex flex-col lg:flex-row gap-3 justify-between items-center">
        <form method="GET" class="relative w-full lg:max-w-md">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o código de barras..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none">
        </form>

        <div class="flex flex-wrap gap-2 w-full lg:w-auto">
            <a href="{{ route('productos.index') }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold {{ !request('alerta') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Todos
            </a>
            <a href="{{ route('productos.index', ['alerta' => 'critico']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request('alerta') === 'critico' ? 'bg-rose-600 text-white' : 'bg-rose-50 text-rose-700 hover:bg-rose-100' }}">
                ⚠️ Crítico ({{ $stockCritico }})
            </a>
            <a href="{{ route('productos.index', ['alerta' => 'bajo']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request('alerta') === 'bajo' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                Bajo ({{ $stockBajo }})
            </a>
            <a href="{{ route('productos.index', ['alerta' => 'sin_stock']) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-bold {{ request('alerta') === 'sin_stock' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Sin Stock (0)
            </a>
        </div>
    </div>

    {{-- Tabla de Productos --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Código</th>
                    <th class="px-6 py-3.5">Nombre</th>
                    <th class="px-6 py-3.5">Categoría</th>
                    @if (auth()->user()->isAdmin())
                        <th class="px-6 py-3.5 text-right">Costo Compra</th>
                    @endif
                    <th class="px-6 py-3.5 text-right">Precio Venta</th>
                    <th class="px-6 py-3.5 text-center">Stock</th>
                    <th class="px-6 py-3.5">Proveedor</th>
                    @if (auth()->user()->isAdmin())
                        <th class="px-6 py-3.5 text-right">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($productos as $producto)
                    @php
                        $estado = $producto->estado_stock;
                        $badge = match($estado) {
                            'critico' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Crítico'],
                            'bajo'    => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Bajo'],
                            default   => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Normal'],
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-6 py-4 font-mono text-xs font-bold text-slate-500">{{ $producto->codigo }}</td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 block">{{ $producto->nombre }}</span>
                            @if ($producto->descripcion)
                                <span class="text-[11px] text-slate-400 block">{{ Str::limit($producto->descripcion, 40) }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs">{{ $producto->categoria->nombre ?? 'General' }}</td>
                        @if (auth()->user()->isAdmin())
                            <td class="px-6 py-4 text-right text-xs text-slate-500">
                                ${{ number_format($producto->precio_compra, 2, ',', '.') }}
                            </td>
                        @endif
                        <td class="px-6 py-4 text-right font-black text-slate-900">
                            ${{ number_format($producto->precio_venta, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <span class="font-black text-slate-900">{{ $producto->stock }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-slate-600 text-xs">
                            {{ $producto->proveedor->empresa ?? '—' }}
                        </td>
                        @if (auth()->user()->isAdmin())
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('productos.edit', $producto) }}"
                                   class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Editar">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                    </svg>
                                </a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar permanentemente este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 8 : 6 }}" class="px-6 py-12 text-center text-slate-400">
                            <p class="text-sm">No se encontraron productos coincidentes.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($productos->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $productos->links() }}
        </div>
    @endif

</div>

@endsection
