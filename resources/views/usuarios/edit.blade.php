@extends('layouts.app')

@section('content')

<div class="max-w-2xl mx-auto space-y-8">
    <div>
        <a href="{{ route('usuarios.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Volver a Usuarios
        </a>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Editar Usuario: {{ $user->name }}</h1>
        <p class="text-xs text-slate-500">Modifica los permisos o datos de contacto del usuario.</p>
    </div>

    {{-- Formulario Principal de Datos --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <form action="{{ route('usuarios.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nombre completo --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nombre Completo *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border @error('name') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                @error('name')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Correo Electrónico (Login) *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full rounded-xl border @error('email') border-rose-500 @else border-slate-200 @enderror bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                @error('email')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Teléfono (Opcional)</label>
                <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}"
                       class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
            </div>

            {{-- Rol --}}
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Rol y Nivel de Permisos *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="rol" value="cajero" {{ old('rol', $user->rol) === 'cajero' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">💼 Cajero / Empleado</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Punto de Cobro, stock, cuaderno de fiados y su propia caja.</span>
                        </div>
                    </label>

                    <label class="p-3.5 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all flex items-start gap-3">
                        <input type="radio" name="rol" value="admin" {{ old('rol', $user->rol) === 'admin' ? 'checked' : '' }} class="mt-0.5 text-slate-900 focus:ring-slate-900">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">👑 Administrador</span>
                            <span class="block text-xs text-slate-500 mt-0.5">Control total, márgenes de ganancia, usuarios e inventario.</span>
                        </div>
                    </label>
                </div>
                @error('rol')
                    <p class="mt-1 text-xs text-rose-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Estado Activo --}}
            @if ($user->id !== auth()->id())
            <div class="flex items-center gap-3 pt-2">
                <input type="checkbox" name="activo" id="activo" value="1" {{ old('activo', $user->activo) ? 'checked' : '' }}
                       class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                <label for="activo" class="text-sm font-medium text-slate-700">Cuenta activa (permitir acceso al sistema)</label>
            </div>
            @endif

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('usuarios.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-900">Cancelar</a>
                <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Tarjeta de Restablecimiento de Contraseña --}}
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8">
        <h3 class="text-base font-bold text-slate-900 mb-1">Restablecer Contraseña</h3>
        <p class="text-xs text-slate-500 mb-6">Asigna una nueva clave de acceso directamente para este usuario.</p>

        <form action="{{ route('usuarios.reset-password', $user) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Nueva Contraseña</label>
                    <input type="password" name="nueva_password" required placeholder="Mínimo 6 caracteres"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Repetir Nueva Contraseña</label>
                    <input type="password" name="nueva_password_confirmation" required placeholder="Confirma la clave"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm text-slate-900 focus:border-slate-900 focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-all">
                    Actualizar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
