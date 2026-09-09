@extends('layouts.app')

@section('content')

{{-- Encabezado de Productos --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Productos</h1>
        <p class="mt-1 text-sm text-slate-500">Gestiona tu inventario de productos y niveles de stock.</p>
    </div>
    <a href="{{ route('productos.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all duration-150">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo Producto
    </a>
</div>

{{-- 3 Tarjetas de Resumen (KPIs de Inventario) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

    {{-- Total Productos --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Total Productos</span>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalProductos }}</h3>
        </div>
    </div>

    {{-- Stock Bajo --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Stock Bajo (< 10)</span>
        <div class="mt-3">
            <h3 class="text-3xl font-bold {{ $stockBajo > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                {{ $stockBajo }}
            </h3>
        </div>
    </div>

    {{-- Valor del Inventario --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Valor del Inventario</span>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">${{ number_format($valorInventario, 0, ',', '.') }}</h3>
        </div>
    </div>

</div>

{{-- Contenedor Principal: Buscador y Tabla --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">

    {{-- Barra de búsqueda --}}
    <div class="p-5 border-b border-slate-100">
        <form method="GET" class="relative max-w-md">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar productos..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
        </form>
    </div>

    {{-- Tabla de Productos --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Nombre</th>
                    <th class="px-6 py-4">Categoría</th>
                    <th class="px-6 py-4 text-right">Precio Venta</th>
                    <th class="px-6 py-4">Stock</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($productos as $producto)
                    @php
                        $estado = $producto->estado_stock;
                        $badge = [
                            'critico' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200/60', 'label' => 'Bajo'],
                            'bajo'    => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/60', 'label' => 'Medio'],
                            'normal'  => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'label' => 'Alto'],
                        ][$estado];
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">{{ $producto->codigo }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $producto->nombre }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-slate-700">{{ $producto->stock }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('productos.edit', $producto) }}" 
                                   class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Editar">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                    </svg>
                                </a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition-colors" title="Eliminar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <p class="text-sm">No se encontraron productos en el inventario.</p>
                            <a href="{{ route('productos.create') }}" class="mt-2 inline-block font-semibold text-emerald-600 hover:text-emerald-700">
                                + Agregar el primer producto
                            </a>
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
