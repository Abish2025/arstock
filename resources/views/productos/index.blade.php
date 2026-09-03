{{-- Hereda la estructura general definida en layouts/app.blade.php --}}
@extends('layouts.app')

@section('content')

<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-ink-950">Productos</h1>
        <p class="mt-1 text-sm text-ink-950/60">{{ $productos->total() }} producto{{ $productos->total() === 1 ? '' : 's' }} en tu inventario.</p>
    </div>
    <a href="{{ route('productos.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-md bg-ink-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-ink-800">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 4v12M4 10h12"/></svg>
        Nuevo producto
    </a>
</div>

{{-- Buscador --}}
<form method="GET" class="mt-6">
    <div class="relative max-w-sm">
        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-950/40" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="9" r="6"/><path d="m17 17-3.5-3.5"/></svg>
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por nombre o código..."
               class="w-full rounded-md border border-ink-950/15 bg-white py-2 pl-9 pr-3 text-sm placeholder:text-ink-950/40 focus:border-brand-500 focus:outline-none focus:ring-1 focus:ring-brand-500">
    </div>
</form>

<div class="mt-6 overflow-hidden rounded-lg border border-ink-950/10 bg-white">
    <table class="w-full text-left text-sm">
        <thead>
            <tr class="border-b border-ink-950/10 text-xs uppercase tracking-wide text-ink-950/50">
                <th class="px-5 py-3 font-medium">Código</th>
                <th class="px-5 py-3 font-medium">Nombre</th>
                <th class="px-5 py-3 font-medium">Categoría</th>
                <th class="px-5 py-3 font-medium text-right">P. Compra</th>
                <th class="px-5 py-3 font-medium text-right">P. Venta</th>
                <th class="px-5 py-3 font-medium">Stock</th>
                <th class="px-5 py-3 font-medium text-right">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-ink-950/8">
            @forelse ($productos as $producto)
                @php
                    $estado = $producto->estado_stock;
                    $puntos = [
                        'critico' => ['dot' => 'bg-crit-600', 'text' => 'text-crit-600', 'label' => 'Crítico'],
                        'bajo'    => ['dot' => 'bg-warn-600', 'text' => 'text-warn-600', 'label' => 'Bajo'],
                        'normal'  => ['dot' => 'bg-brand-500', 'text' => 'text-ink-950/60', 'label' => 'Normal'],
                    ][$estado];
                @endphp
                <tr class="hover:bg-ink-950/[0.02]">
                    <td class="px-5 py-3.5 font-mono text-xs text-ink-950/70">{{ $producto->codigo }}</td>
                    <td class="px-5 py-3.5 font-medium text-ink-950">{{ $producto->nombre }}</td>
                    <td class="px-5 py-3.5 text-ink-950/70">{{ $producto->categoria->nombre ?? '—' }}</td>
                    <td class="px-5 py-3.5 text-right text-ink-950/70">${{ number_format($producto->precio_compra, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5 text-right font-medium text-ink-950">${{ number_format($producto->precio_venta, 0, ',', '.') }}</td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center gap-1.5 {{ $puntos['text'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $puntos['dot'] }}"></span>
                            {{ $producto->stock }}
                            @if ($estado !== 'normal')
                                <span class="text-xs">({{ $puntos['label'] }})</span>
                            @endif
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('productos.edit', $producto) }}" class="text-ink-950/60 hover:text-ink-900" title="Editar">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M13.5 3.5a1.5 1.5 0 0 1 2 2L6.5 15 3 16l1-3.5 9.5-9Z"/></svg>
                            </a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar este producto?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-ink-950/40 hover:text-crit-600" title="Eliminar">
                                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 6h12M8 6V4h4v2m-6 0 .5 10h7L14 6"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-ink-950/50">
                        Todavía no cargaste ningún producto.
                        <a href="{{ route('productos.create') }}" class="font-medium text-brand-600 hover:underline">Cargá el primero</a>.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $productos->links() }}
</div>

@endsection
