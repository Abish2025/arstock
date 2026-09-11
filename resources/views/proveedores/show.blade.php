@extends('layouts.app')

@section('content')

{{-- Encabezado con datos del proveedor --}}
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('proveedores.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Proveedores
        </a>
        <div class="mt-2 flex items-center gap-3">
            <div class="h-11 w-11 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-base shadow-sm">
                {{ strtoupper(substr($proveedor->empresa, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ $proveedor->empresa }}</h1>
                <p class="text-xs text-slate-400">
                    @if ($proveedor->contacto)
                        Preventista: <span class="text-slate-700 font-semibold">{{ $proveedor->contacto }}</span> •
                    @endif
                    Días de visita: <span class="text-emerald-700 font-semibold">{{ $proveedor->dias_visita ?? 'A convenir' }}</span>
                </p>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2">
        @if ($proveedor->telefono)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proveedor->telefono) }}" target="_blank"
               class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 transition-colors">
                <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                Hacer Pedido (WhatsApp)
            </a>
        @endif
        <a href="{{ route('proveedores.edit', $proveedor) }}"
           class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
            </svg>
            Editar
        </a>
    </div>
</div>

{{-- Información del Proveedor --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Contacto</span>
        <p class="mt-2 text-sm font-semibold text-slate-800">{{ $proveedor->contacto ?? 'Sin preventista asignado' }}</p>
        <p class="text-xs text-slate-500">{{ $proveedor->telefono ?? 'Sin teléfono' }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Días de Reparto</span>
        <p class="mt-2 text-sm font-bold text-slate-800">{{ $proveedor->dias_visita ?? 'No especificado' }}</p>
        <p class="text-xs text-slate-500">{{ $proveedor->direccion ?? 'Sin dirección' }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Productos en Catálogo</span>
        <p class="mt-2 text-2xl font-black text-slate-900">{{ $proveedor->productos->count() }}</p>
        <p class="text-xs text-slate-500">artículos asociados a este proveedor</p>
    </div>
</div>

{{-- Catálogo de Productos Provistos --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-900">Productos Provistos por {{ $proveedor->empresa }}</h3>
            <p class="text-xs text-slate-500">Artículos del inventario que le compramos a este distribuidor</p>
        </div>
        <a href="{{ route('productos.create') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800 transition-colors">
            + Asociar Nuevo Producto
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Código</th>
                    <th class="px-6 py-3.5">Producto</th>
                    <th class="px-6 py-3.5">Categoría</th>
                    <th class="px-6 py-3.5 text-right">P. Compra (Costo)</th>
                    <th class="px-6 py-3.5 text-right">P. Venta</th>
                    <th class="px-6 py-3.5">Stock Actual</th>
                    <th class="px-6 py-3.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($proveedor->productos as $producto)
                    @php
                        $estado = $producto->estado_stock;
                        $badge = [
                            'critico' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200/60', 'label' => 'Bajo (Pedir reposición)'],
                            'bajo'    => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200/60', 'label' => 'Medio'],
                            'normal'  => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60', 'label' => 'Alto'],
                        ][$estado];
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        <td class="px-6 py-4 font-mono text-xs text-slate-500 font-semibold">{{ $producto->codigo }}</td>
                        <td class="px-6 py-4 font-semibold text-slate-900">{{ $producto->nombre }}</td>
                        <td class="px-6 py-4 text-slate-600 text-xs">{{ $producto->categoria->nombre ?? 'Sin categoría' }}</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-700">${{ number_format($producto->precio_compra, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-900">${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-800">{{ $producto->stock }}</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold border {{ $badge['bg'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('productos.edit', $producto) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Editar Producto">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            <p class="text-sm">No hay productos asociados a este proveedor todavía.</p>
                            <p class="text-xs text-slate-400 mt-1">Al crear o editar productos en el inventario puedes asignarlos a {{ $proveedor->empresa }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
