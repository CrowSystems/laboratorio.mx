@props([
    'titulo' => null,
])

{{--
    Layout de las pantallas públicas: la consulta del alumno y el acceso del
    personal.

    Está pensado primero para el celular: una sola columna, controles grandes y
    el contenido centrado verticalmente. En escritorio simplemente se queda
    centrado, con más aire. Ese es el orden correcto: diseñar para la pantalla
    chica y luego agregar, no diseñar para la grande y luego recortar.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#161c33">
    <meta name="description" content="Consulta en línea del equipo que tienes prestado en el laboratorio de cómputo del CESBA.">
    <title>{{ $titulo ? $titulo.' · ' : '' }}{{ config('app.name') }}</title>
    <x-tailwind />
</head>
<body class="flex min-h-full flex-col bg-tinta-950 font-sans text-tinta-900 antialiased">

{{-- Fondo: dos degradados radiales y una rejilla. Ni un archivo de imagen. --}}
<div class="pointer-events-none fixed inset-0 fondo-rejilla opacity-40" aria-hidden="true"></div>
<div class="pointer-events-none fixed inset-0 opacity-70" aria-hidden="true"
     style="background:
        radial-gradient(60rem 40rem at 15% -10%, rgba(70,100,174,.55), transparent 60%),
        radial-gradient(45rem 35rem at 100% 10%, rgba(251,191,36,.18), transparent 55%),
        radial-gradient(50rem 40rem at 50% 110%, rgba(22,28,51,.9), transparent 60%);"></div>

<div class="relative flex flex-1 flex-col">
    <header class="px-5 pt-6 sm:px-8">
        <div class="mx-auto flex max-w-2xl items-center justify-between gap-4">
            <a href="{{ route('consulta.index') }}" class="min-w-0 rounded-xl outline-none focus-visible:ring-2 focus-visible:ring-amber-300">
                <x-marca />
            </a>

            @auth
                <a href="{{ route('tablero') }}"
                   class="shrink-0 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                    <span class="sm:hidden">Tablero</span>
                    <span class="hidden sm:inline">Ir al tablero</span>
                </a>
            @else
                <a href="{{ route('sesion.create') }}"
                   class="shrink-0 rounded-xl bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                    {{-- En el celular no cabe la frase completa junto al logotipo. --}}
                    <span class="sm:hidden">Entrar</span>
                    <span class="hidden sm:inline">Soy del laboratorio</span>
                </a>
            @endauth
        </div>
    </header>

    <main class="flex flex-1 items-center px-5 py-8 sm:px-8 sm:py-12">
        <div class="mx-auto w-full max-w-2xl">
            {{ $slot }}
        </div>
    </main>

    <footer class="px-5 pb-7 text-center text-xs text-tinta-400 sm:px-8">
        CESBA · Centro de Estudios Superiores del Bajío — Querétaro<br class="sm:hidden">
        <span class="hidden sm:inline"> · </span>Laboratorio de Cómputo
    </footer>
</div>
</body>
</html>
