<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ArStock - Sistema de Gestión' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Chart.js para gráficos interactivos del Dashboard --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="h-full bg-slate-50 text-slate-800 font-sans antialiased">

    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- Barra lateral (Sidebar) --}}
        <aside class="w-full lg:w-64 bg-[#0a0e1a] text-white flex flex-col justify-between shrink-0 border-r border-slate-800/60 shadow-xl">
            <div>
                {{-- Encabezado del Sistema --}}
                <div class="px-6 py-6 border-b border-slate-800/80 flex items-center justify-between">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-lg bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 font-bold text-base tracking-wide">
                            AR
                        </div>
                        <div>
                            <span class="text-base font-bold tracking-tight text-white block leading-tight">ArStock</span>
                            <span class="text-[11px] font-medium text-slate-400">Sistema de Gestión</span>
                        </div>
                    </a>
                </div>

                {{-- Navegación --}}
                <nav class="p-4 space-y-1.5">
                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('dashboard') ? 'bg-slate-800/90 text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                        </svg>
                        Dashboard
                    </a>

                    {{-- Productos --}}
                    <a href="{{ route('productos.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('productos.*') ? 'bg-slate-800/90 text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>
                        </svg>
                        Productos
                    </a>

                    {{-- Clientes --}}
                    <a href="{{ route('clientes.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('clientes.*') ? 'bg-slate-800/90 text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Clientes
                    </a>

                    {{-- Ventas --}}
                    <a href="{{ route('ventas.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('ventas.*') ? 'bg-slate-800/90 text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>
                        </svg>
                        Ventas
                    </a>

                    {{-- Proveedores --}}
                    <a href="{{ route('proveedores.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('proveedores.*') ? 'bg-slate-800/90 text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>
                        </svg>
                        Proveedores
                    </a>

                    {{-- Reportes --}}
                    <a href="{{ route('reportes.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('reportes.*') ? 'bg-slate-800/90 text-white shadow-sm ring-1 ring-white/10' : 'text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                        Reportes
                    </a>
                </nav>
            </div>

            {{-- Pie del Sidebar / Usuario y Cerrar Sesión --}}
            <div class="p-4 border-t border-slate-800/80 space-y-2">

                {{-- Info del usuario logueado --}}
                @auth
                <div class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg bg-slate-800/40">
                    <div class="h-8 w-8 rounded-full bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                @endauth

                {{-- Botón Cerrar Sesión --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-lg text-sm font-medium text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all duration-150">
                        <svg class="h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>
                        </svg>
                        Cerrar Sesión
                    </button>
                </form>
            </div>
        </aside>

        {{-- Área Principal de Contenido --}}
        <main class="flex-1 min-w-0 overflow-y-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                {{-- Notificaciones / Alertas --}}
                @if (session('success'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl border border-emerald-500/20 bg-emerald-50/80 p-4 text-sm text-emerald-800 shadow-sm animate-in fade-in duration-200">
                        <svg class="h-5 w-5 text-emerald-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-center gap-3 rounded-xl border border-rose-500/20 bg-rose-50/80 p-4 text-sm text-rose-800 shadow-sm animate-in fade-in duration-200">
                        <svg class="h-5 w-5 text-rose-600 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')

            </div>
        </main>

    </div>

</body>
</html>
