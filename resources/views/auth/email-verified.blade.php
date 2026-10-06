@extends('layouts.app')

@section('content')

<div class="min-h-[70vh] flex items-center justify-center px-4">

    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">

        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-100">
            <svg class="h-7 w-7 text-emerald-600"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-900">
            ¡Correo verificado!
        </h1>

        <p class="mt-3 text-sm text-slate-500">
            Tu correo electrónico fue verificado correctamente.
        </p>

        <p class="mt-2 text-sm text-slate-500">
            Ya podés iniciar sesión en ARStock con tu cuenta.
        </p>

        <a href="{{ route('login') }}"
           class="mt-6 inline-block w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition">
            Iniciar sesión
        </a>

    </div>

</div>

@endsection