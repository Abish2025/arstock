@extends('layouts.app')

@section('content')

{{-- Encabezado de Clientes --}}
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Clientes y Fiados</h1>
        <p class="mt-1 text-sm text-slate-500">Administra tu base de clientes, compras fiadas y cuentas corrientes.</p>
    </div>
    <a href="{{ route('clientes.create') }}"
       class="inline-flex items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all duration-150">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Nuevo Cliente
    </a>
</div>

{{-- 3 Tarjetas de Resumen (KPIs de Clientes y Fiados) --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

    {{-- Total Clientes --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Total Clientes</span>
            <div class="h-10 w-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold text-slate-900">{{ $totalClientes }}</h3>
        </div>
    </div>

    {{-- Clientes con Deuda --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Con Deuda Pendiente</span>
            <div class="h-10 w-10 rounded-xl {{ $clientesConDeuda > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold {{ $clientesConDeuda > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                {{ $clientesConDeuda }}
            </h3>
        </div>
    </div>

    {{-- Total Fiado en la Calle --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between">
            <span class="text-sm font-medium text-slate-500">Total por Cobrar (Fiado)</span>
            <div class="h-10 w-10 rounded-xl {{ $totalDeuda > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-100 text-slate-700' }} flex items-center justify-center">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
        </div>
        <div class="mt-3">
            <h3 class="text-3xl font-bold {{ $totalDeuda > 0 ? 'text-rose-600' : 'text-slate-900' }}">
                ${{ number_format($totalDeuda, 0, ',', '.') }}
            </h3>
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
            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar cliente por nombre o teléfono..."
                   class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-2.5 pl-10 pr-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-none focus:ring-1 focus:ring-slate-900 transition-all">
        </form>
    </div>

    {{-- Tabla de Clientes --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50/80 text-xs font-semibold uppercase text-slate-400 border-b border-slate-100">
                <tr>
                    <th class="px-6 py-4">Cliente</th>
                    <th class="px-6 py-4">Contacto</th>
                    <th class="px-6 py-4">Dirección / Notas</th>
                    <th class="px-6 py-4 text-right">Saldo Deudor</th>
                    <th class="px-6 py-4 text-center">Estado</th>
                    <th class="px-6 py-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($clientes as $cliente)
                    <tr class="hover:bg-slate-50/60 transition-colors duration-150">
                        {{-- Cliente con Avatar --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($cliente->nombre, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('clientes.show', $cliente) }}" class="font-semibold text-slate-900 hover:text-emerald-600 transition-colors block leading-tight">
                                        {{ $cliente->nombre }}
                                    </a>
                                    <span class="text-xs text-slate-400 font-mono">CLI-{{ str_pad($cliente->id_cliente, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- Teléfono / WhatsApp --}}
                        <td class="px-6 py-4">
                            @if ($cliente->telefono)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $cliente->telefono) }}" target="_blank"
                                   class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-600 hover:text-emerald-600 transition-colors" title="Contactar por WhatsApp">
                                    <svg class="h-3.5 w-3.5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                    </svg>
                                    {{ $cliente->telefono }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>

                        {{-- Dirección y Notas --}}
                        <td class="px-6 py-4 text-slate-600 text-xs max-w-xs truncate">
                            {{ $cliente->direccion ?? $cliente->notas ?? '—' }}
                        </td>

                        {{-- Saldo --}}
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-sm {{ $cliente->saldo > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                                ${{ number_format($cliente->saldo, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Estado con Badge --}}
                        <td class="px-6 py-4 text-center">
                            @if ($cliente->saldo <= 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    Al día
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    Debe ${{ number_format($cliente->saldo, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                {{-- Ver Cuaderno / Cuenta Corriente --}}
                                <a href="{{ route('clientes.show', $cliente) }}"
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
                                   title="Abrir cuaderno de fiados">
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/>
                                    </svg>
                                    Cuaderno
                                </a>

                                {{-- Editar --}}
                                <a href="{{ route('clientes.edit', $cliente) }}"
                                   class="p-1.5 rounded-lg text-slate-400 hover:text-slate-900 hover:bg-slate-100 transition-colors" title="Editar datos">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/>
                                    </svg>
                                </a>

                                {{-- Eliminar --}}
                                <form action="{{ route('clientes.destroy', $cliente) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar este cliente y todo su historial de fiados?')">
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
                            <p class="text-sm">Todavía no tienes clientes registrados en el sistema.</p>
                            <a href="{{ route('clientes.create') }}" class="mt-2 inline-block font-semibold text-emerald-600 hover:text-emerald-700">
                                + Agregar el primer cliente
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if ($clientes->hasPages())
        <div class="px-6 py-4 border-t border-slate-100">
            {{ $clientes->links() }}
        </div>
    @endif

</div>

@endsection
