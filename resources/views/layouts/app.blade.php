<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'ArStock' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-paper text-ink-950 antialiased">

    <div class="lg:flex lg:min-h-screen">

        {{-- Barra lateral --}}
        <aside class="bg-ink-900 text-white lg:w-64 lg:shrink-0 lg:min-h-screen">
            <div class="flex items-center justify-between px-5 py-5 lg:block">
                <a href="{{ route('productos.index') }}" class="flex items-center gap-2">
                    <span class="flex h-8 w-8 items-center justify-center rounded-md bg-brand-500 text-sm font-semibold">Ar</span>
                    <span class="text-lg font-semibold tracking-tight">ArStock</span>
                </a>
            </div>

            <nav class="px-3 pb-6 lg:pb-0">
                <p class="px-2 pt-2 pb-1 text-xs font-medium text-white/40">Inventario</p>
                <a href="{{ route('productos.index') }}"
                   class="flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('productos.*') ? 'bg-white/10 text-white' : 'text-white/70 hover:bg-white/5 hover:text-white' }}">
                    <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 6.5 10 3l7 3.5M3 6.5v7L10 17l7-3.5v-7M3 6.5 10 10m0 0 7-3.5M10 10v7"/></svg>
                    Productos
                </a>

                <p class="px-2 pt-5 pb-1 text-xs font-medium text-white/40">Ventas y clientes</p>
                <span class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm text-white/30 cursor-not-allowed">
                    <span class="flex items-center gap-3">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 4h2l1 9.5a1.5 1.5 0 0 0 1.5 1.5h6.4a1.5 1.5 0 0 0 1.48-1.24L17 7H6"/><circle cx="8" cy="17" r="1"/><circle cx="14" cy="17" r="1"/></svg>
                        Ventas del día
                    </span>
                    <span class="rounded-full border border-white/15 px-1.5 py-0.5 text-[10px]">pronto</span>
                </span>
                <span class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm text-white/30 cursor-not-allowed">
                    <span class="flex items-center gap-3">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="10" cy="7" r="3"/><path d="M4 17c0-3 2.7-5 6-5s6 2 6 5"/></svg>
                        Clientes / fiado
                    </span>
                    <span class="rounded-full border border-white/15 px-1.5 py-0.5 text-[10px]">pronto</span>
                </span>

                <p class="px-2 pt-5 pb-1 text-xs font-medium text-white/40">Negocio</p>
                <span class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm text-white/30 cursor-not-allowed">
                    <span class="flex items-center gap-3">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 4v12h12"/><path d="M7 13l2.5-3 2 2L15 8"/></svg>
                        Reportes
                    </span>
                    <span class="rounded-full border border-white/15 px-1.5 py-0.5 text-[10px]">pronto</span>
                </span>
                <span class="flex items-center justify-between gap-3 rounded-md px-3 py-2 text-sm text-white/30 cursor-not-allowed">
                    <span class="flex items-center gap-3">
                        <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 15l3-8 3 5 2-3 4 6"/></svg>
                        Proveedores
                    </span>
                    <span class="rounded-full border border-white/15 px-1.5 py-0.5 text-[10px]">pronto</span>
                </span>
            </nav>
        </aside>

        {{-- Contenido --}}
        <div class="flex-1 min-w-0">
            <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-10">

                @if (session('success'))
                    <div class="mb-6 flex items-start gap-3 rounded-md border border-brand-500/30 bg-brand-100 px-4 py-3 text-sm text-brand-600">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 10l4 4 8-9"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 flex items-start gap-3 rounded-md border border-crit-600/30 bg-crit-100 px-4 py-3 text-sm text-crit-600">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 6v5m0 3h.01M10 2l8 15H2z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')

            </main>
        </div>
    </div>

</body>
</html>
