@extends('layouts.app')

@section('content')

<div class="mb-6">
    <a href="{{ route('clientes.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-slate-900 transition-colors">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        Volver al listado de clientes
    </a>
    <h1 class="mt-3 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Nuevo Cliente</h1>
    <p class="mt-1 text-sm text-slate-500">Registra un nuevo cliente para abrirle cuenta corriente o habilitar fiados.</p>
</div>

<form action="{{ route('clientes.store') }}" method="POST" class="max-w-3xl rounded-2xl border border-slate-200/80 bg-white p-6 sm:p-8 shadow-sm">
    @csrf

    @include('clientes._form')

    <div class="mt-8 flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
        <a href="{{ route('clientes.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-colors">
            Cancelar
        </a>
        <button type="submit" class="rounded-xl bg-slate-900 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-slate-800 transition-all">
            Registrar Cliente
        </button>
    </div>
</form>

@endsection
