<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — ARStock</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-[#0a0e1a] font-sans antialiased">

<div class="min-h-screen flex">

    {{-- Panel Izquierdo: Branding / Ilustración --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-[#0a0e1a] via-[#0f1629] to-[#111827] flex-col items-center justify-center p-12">

        {{-- Fondo decorativo con círculos --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
            <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-emerald-500/5 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-80 h-80 rounded-full bg-blue-500/5 blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-72 h-72 rounded-full bg-slate-500/5 blur-3xl"></div>
        </div>

        {{-- Contenido de branding --}}
        <div class="relative z-10 text-center max-w-md">
            {{-- Logo / Icono del sistema --}}
            <div class="inline-flex items-center justify-center h-20 w-20 rounded-3xl bg-white/5 border border-white/10 shadow-2xl mb-6">
                <svg class="h-10 w-10 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>

            <h1 class="text-4xl font-extrabold text-white tracking-tight mb-3">ARStock</h1>
            <p class="text-slate-400 text-base leading-relaxed">
                Sistema de gestión integral para tu negocio. Control de inventario, fiados, ventas y proveedores en un solo lugar.
            </p>

            {{-- Mini estadísticas decorativas --}}
            <div class="mt-10 grid grid-cols-3 gap-4">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                    <p class="text-2xl font-bold text-white">📦</p>
                    <p class="text-xs text-slate-400 mt-1">Inventario</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                    <p class="text-2xl font-bold text-white">📒</p>
                    <p class="text-xs text-slate-400 mt-1">Fiados</p>
                </div>
                <div class="bg-white/5 border border-white/10 rounded-2xl p-4">
                    <p class="text-2xl font-bold text-white">📊</p>
                    <p class="text-xs text-slate-400 mt-1">Reportes</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Panel Derecho: Formulario de Login --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-slate-50">
        <div class="w-full max-w-md">

            {{-- Header del formulario --}}
            <div class="mb-8">
                {{-- Logo visible en mobile --}}
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div class="h-10 w-10 rounded-xl bg-[#0a0e1a] flex items-center justify-center">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" x2="21" y1="6" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight">ARStock</span>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Bienvenido de vuelta</h2>
                <p class="mt-2 text-sm text-slate-500">Ingresá con tu cuenta para acceder al sistema.</p>
            </div>

            {{-- Alerta de error de credenciales --}}
            @if ($errors->isNotEmpty())
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4">
                    <svg class="mt-0.5 h-4 w-4 shrink-0 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>
                    </svg>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-sm font-medium text-rose-700">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Alerta de sesión cerrada exitosamente --}}
            @if (session('status'))
                <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                    <svg class="h-4 w-4 shrink-0 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    <p class="text-sm font-medium text-emerald-700">{{ session('status') }}</p>
                </div>
            @endif

            {{-- Formulario de Login --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                {{-- Campo Email --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Email
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            placeholder="tu@email.com"
                            class="w-full rounded-xl border pl-10 pr-4 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all
                                   {{ $errors->has('email') ? 'border-rose-400 bg-rose-50 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-200 bg-white focus:border-slate-900 focus:ring-slate-900' }}
                                   focus:outline-none focus:ring-1"
                        >
                    </div>
                </div>

                {{-- Campo Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                        Contraseña
                    </label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center">
                            <svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            placeholder="Tu contraseña"
                            class="w-full rounded-xl border pl-10 pr-12 py-3 text-sm text-slate-900 placeholder:text-slate-400 transition-all
                                   {{ $errors->has('email') ? 'border-rose-400 bg-rose-50 focus:border-rose-500 focus:ring-rose-500' : 'border-slate-200 bg-white focus:border-slate-900 focus:ring-slate-900' }}
                                   focus:outline-none focus:ring-1"
                        >
                        {{-- Botón mostrar/ocultar contraseña --}}
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-3.5 flex items-center text-slate-400 hover:text-slate-700 transition-colors" tabindex="-1">
                            <svg id="eye-icon" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Recordarme --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" id="remember"
                               class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900 cursor-pointer">
                        <span class="text-sm text-slate-600">Recordarme</span>
                    </label>
                </div>

                {{-- Botón Submit --}}
                <button type="submit"
                        class="mt-2 w-full inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-slate-800 active:scale-[0.98] transition-all duration-150">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/>
                    </svg>
                    Entrar al Sistema
                </button>
            </form>

            {{-- Footer del formulario --}}
            <p class="mt-8 text-center text-xs text-slate-400">
                ARStock — Sistema de Gestión &copy; {{ date('Y') }}
            </p>
        </div>
    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" x2="23" y1="1" y2="23"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
        }
    }
</script>

</body>
</html>
