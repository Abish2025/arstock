@extends('layouts.app')

@section('content')

<div class="min-h-[70vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-2xl border border-slate-200 shadow-sm p-8 text-center">

        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
            <svg class="h-7 w-7 text-slate-700"
                 fill="none"
                 stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 008.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-slate-900">
            Verificá tu correo
        </h1>

        <p class="mt-3 text-sm text-slate-500">
            Te enviamos un enlace de verificación a:
        </p>

        <p class="mt-2 font-semibold text-slate-900">
            {{ auth()->user()->email }}
        </p>

        <p class="mt-3 text-sm text-slate-500">
            Revisá tu bandeja de entrada y hacé clic en
            <strong>“Verificar correo”</strong> para activar tu cuenta.
        </p>

        @if (session('success'))
            <div class="mt-5 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST"
              action="{{ route('verification.send') }}"
              class="mt-6">

            @csrf

            <button type="submit"
                    class="w-full rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white hover:bg-slate-800 transition">
                Reenviar correo de verificación
            </button>

        </form>

        <form method="POST"
              action="{{ route('logout') }}"
              class="mt-3">

            @csrf

            <button type="submit"
                    class="text-sm text-slate-500 hover:text-slate-900">
                Cerrar sesión
            </button>

        </form>

    </div>
</div>

@endsection