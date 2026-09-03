@extends('layouts.app')

@section('content')

<a href="{{ route('productos.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-950/60 hover:text-ink-900">
    <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 4 6 10l6 6"/></svg>
    Productos
</a>
<h1 class="mt-3 text-2xl font-semibold tracking-tight text-ink-950">Editar producto</h1>

<form action="{{ route('productos.update', $producto) }}" method="POST" class="mt-6 max-w-2xl rounded-lg border border-ink-950/10 bg-white p-6">
    @csrf
    @method('PUT')

    @include('productos._form')

    <div class="mt-8 flex items-center gap-3 border-t border-ink-950/10 pt-5">
        <button type="submit" class="rounded-md bg-ink-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-ink-800">
            Guardar cambios
        </button>
        <a href="{{ route('productos.index') }}" class="text-sm font-medium text-ink-950/60 hover:text-ink-900">Cancelar</a>
    </div>
</form>

@endsection
