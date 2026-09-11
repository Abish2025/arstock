@extends('layouts.app')

@section('content')

{{-- Encabezado de Proveedores --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Proveedores</h1>
        <p class="mt-1 text-sm text-slate-500">Gestiona tus distribuidores, contactos de preventistas y días de reparto.</p>
    </div>
    <a href="{{ route('proveedores.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all duration-150">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo Proveedor
    </a>
</div>

{{-- 3 Tarjetas de Métricas (KPIs de Proveedores) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

    {{-- Total Proveedores --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Total Proveedores</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalProveedores }}</h3>
        </div>
    </div>

    {{-- Proveedores Activos --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Con Productos Asignados</span>
            <div class="h-10 w-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-emerald-600">{{ $proveedoresActivos }}</h3>
        </div>
    </div>

    {{-- Total Productos Provistos --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Productos Provistos</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalProductosAsignados }}</h3>
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
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por empresa, contacto o día..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
        </form>
    </div>

    {{-- Tabla de Proveedores --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Empresa / Distribuidora</th>
                    <th class="px-6 py-4">Preventista / Contacto</th>
                    <th class="px-6 py-4">Teléfono / WhatsApp</th>
                    <th class="px-6 py-4">Días de Reparto</th>
                    <th class="px-6 py-4 text-center">Productos</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($proveedores as $proveedor)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        {{-- Empresa con Avatar --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($proveedor->empresa, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('proveedores.show', $proveedor) }}" class="font-semibold text-slate-900 hover:text-emerald-600 transition-colors block leading-tight">
                                        {{ $proveedor->empresa }}
                                    </a>
                                    @if ($proveedor->cuit)
                                        <span class="text-xs text-slate-400 font-mono">CUIT: {{ $proveedor->cuit }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Contacto --}}
                        <td class="px-6 py-4 text-slate-700 font-medium text-xs">
                            {{ $proveedor->contacto ?? '—' }}
                        </td>

                        {{-- Teléfono / WhatsApp --}}
                        <td class="px-6 py-4">
                            @if ($proveedor->telefono)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $proveedor->telefono) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-emerald-600 transition-colors" title="Hacer pedido por WhatsApp">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    {{ $proveedor->telefono }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Días de Visita --}}
                        <td class="px-6 py-4 text-xs font-medium text-slate-600">
                            @if ($proveedor->dias_visita)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px]">
                                    {{ $proveedor->dias_visita }}
                                </span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Productos Asociados --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $proveedor->productos_count > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-500' }}">
                                {{ $proveedor->productos_count }} producto{{ $proveedor->productos_count === 1 ? '' : 's' }}
                            </span>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('proveedores.show', $proveedor) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Ver Catálogo">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </a>
                                <a href="{{ route('proveedores.edit', $proveedor) }}" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Editar">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                    </svg>
                                </a>
                                <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este proveedor?')">
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
                            <p class="text-sm">No tienes proveedores registrados todavía.</p>
                            <a href="{{ route('proveedores.create') }}" class="mt-2 inline-block font-semibold text-emerald-600 hover:text-emerald-700">
                                + Registrar el primer proveedor
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($proveedores->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $proveedores->links() }}
        </div>
    @endif

</div>

@endsection
