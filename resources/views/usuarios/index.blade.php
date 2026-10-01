@extends('layouts.app')

@section('content')

{{-- Encabezado --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Usuarios y Empleados</h1>
        <p class="mt-1 text-sm text-slate-500">Administra las cuentas de acceso al sistema, roles y permisos.</p>
    </div>
    <a href="{{ route('usuarios.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo Usuario
    </a>
</div>

{{-- KPIs --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Total Cuentas</span>
        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ $totalUsuarios }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Administradores</span>
        <h3 class="text-3xl font-bold text-indigo-600 mt-2">{{ $totalAdmins }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Cajeros / Empleados</span>
        <h3 class="text-3xl font-bold text-emerald-600 mt-2">{{ $totalCajeros }}</h3>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <span class="text-sm font-medium text-slate-500">Activos</span>
        <h3 class="text-3xl font-bold text-slate-900 mt-2">{{ $totalActivos }}</h3>
    </div>
</div>

{{-- Buscador y Filtro --}}
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row gap-3 justify-between items-center">
        <form method="GET" class="relative w-full sm:max-w-md">
            <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
            </svg>
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre, email o teléfono..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none">
        </form>
        <div class="flex gap-2">
            <a href="{{ route('usuarios.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ !request('rol') ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Todos</a>
            <a href="{{ route('usuarios.index', ['rol' => 'admin']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('rol') === 'admin' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Admins</a>
            <a href="{{ route('usuarios.index', ['rol' => 'cajero']) }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ request('rol') === 'cajero' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Cajeros</a>
        </div>
    </div>

    {{-- Tabla --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-3.5">Usuario</th>
                    <th class="px-6 py-3.5">Rol</th>
                    <th class="px-6 py-3.5">Teléfono</th>
                    <th class="px-6 py-3.5 text-center">Estado</th>
                    <th class="px-6 py-3.5 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($usuarios as $u)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-full bg-slate-100 flex items-center justify-center font-bold text-xs text-slate-700">
                                {{ strtoupper(substr($u->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">{{ $u->name }}</p>
                                <p class="text-xs text-slate-400">{{ $u->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $u->rol === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                            {{ $u->rol === 'admin' ? '👑 Administrador' : '💼 Cajero / Empleado' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-600">
                        {{ $u->telefono ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $u->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            {{ $u->activo ? 'Activo' : 'Desactivado' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('usuarios.edit', $u) }}" class="p-1.5 text-slate-500 hover:text-slate-900 rounded-lg hover:bg-slate-100" title="Editar">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                </svg>
                            </a>
                            @if ($u->id !== auth()->id())
                                <form action="{{ route('usuarios.toggle', $u) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 rounded-lg {{ $u->activo ? 'text-amber-600 hover:bg-amber-50' : 'text-emerald-600 hover:bg-emerald-50' }}"
                                            title="{{ $u->activo ? 'Desactivar acceso' : 'Activar acceso' }}">
                                        @if ($u->activo)
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="20 6 9 17 4 12"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="inline"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar permanentemente a este usuario?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50" title="Eliminar">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">
                        No se encontraron usuarios registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($usuarios->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $usuarios->links() }}
    </div>
    @endif
</div>

@endsection
