@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('usuarios.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Usuarios
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Crear Nuevo Usuario / Empleado</h1>
        <p class="text-xs text-slate-500">Completa los datos para habilitar una nueva cuenta en el sistema.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Nombre completo --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nombre Completo *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ej: Juan Pérez"
                       class="w-full rounded-xl border @error('name') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                @error('name')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Correo Electrónico (Login) *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="Ej: juan@empresa.com"
                       class="w-full rounded-xl border @error('email') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                @error('email')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teléfono (Opcional)</label>
                <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: +54 9 376 4123456"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
            </div>

            {{-- Rol --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Rol y Nivel de Permisos *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="rol" value="cajero" {{ old('rol', 'cajero') === 'cajero' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">💼 Cajero / Empleado</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Acceso a Punto de Cobro, consulta de stock, catálogo de clientes y apertura/cierre de su caja.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="rol" value="admin" {{ old('rol') === 'admin' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">👑 Administrador</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Control total del negocio: reportes, costos de compra, creación de usuarios y anulación de ventas.</span>
                        </div>
                    </label>
                </div>
                @error('rol')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Contraseñas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Contraseña *</label>
                    <input type="password" name="password" required placeholder="Mínimo 6 caracteres"
                           class="w-full rounded-xl border @error('password') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Confirmar Contraseña *</label>
                    <input type="password" name="password_confirmation" required placeholder="Repite la contraseña"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                </div>
            </div>

            {{-- Estado Activo --}}
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                <label for="activo" class="text-sm font-medium text-slate-700">Cuenta activa (puede iniciar sesión inmediatamente)</label>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('usuarios.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900">Cancelar</a>
                <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
