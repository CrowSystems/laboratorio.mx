@props([
    'titulo' => null,
    'subtitulo' => null,
])

{{--
    Layout de la zona autenticada.

    Es un componente de Blade, no una plantilla con @extends. Las vistas lo usan
    así:

        <x-layouts.app titulo="Catálogo de equipo">
            ... contenido ...
        </x-layouts.app>

    Todo lo que la vista escriba adentro llega aquí en la variable {{ $slot }}.
    Las props declaradas arriba (titulo, subtitulo) son los datos que el layout
    necesita y que la vista le pasa como atributos.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $titulo ? $titulo.' · ' : '' }}{{ config('app.name') }}</title>
    <x-tailwind />
</head>
<body class="h-full bg-tinta-50 font-sans text-tinta-900 antialiased">
<div class="min-h-full lg:flex">

    {{-- ---------------------------------------------------------------- --}}
    {{-- Navegación. En pantalla grande es una barra lateral fija; en el  --}}
    {{-- celular se convierte en un encabezado con los enlaces en una     --}}
    {{-- fila que se desliza. Sin JavaScript: solo clases de Tailwind.    --}}
    {{-- ---------------------------------------------------------------- --}}
    <aside class="bg-tinta-950 lg:sticky lg:top-0 lg:flex lg:h-screen lg:w-72 lg:shrink-0 lg:flex-col">
        <div class="flex items-center justify-between gap-4 px-5 py-4 lg:px-6 lg:py-7">
            <a href="{{ route('tablero') }}" class="min-w-0 rounded-xl outline-none focus-visible:ring-2 focus-visible:ring-amber-300">
                <x-marca />
            </a>

            {{-- Datos del usuario y salida, visibles solo en el celular. --}}
            <form method="POST" action="{{ route('sesion.destroy') }}" class="lg:hidden">
                @csrf
                <button type="submit"
                        class="rounded-lg bg-white/10 px-3 py-2 text-xs font-semibold text-white transition hover:bg-white/20">
                    Salir
                </button>
            </form>
        </div>

        <nav class="flex gap-1.5 overflow-x-auto px-4 pb-4 lg:flex-col lg:overflow-visible lg:px-4 lg:pb-0"
             aria-label="Navegación principal">
            <x-nav-enlace :href="route('tablero')" :activo="request()->routeIs('tablero')" icono="tablero">
                Tablero
            </x-nav-enlace>

            <x-nav-enlace :href="route('prestamos.index')" :activo="request()->routeIs('prestamos.*', 'devoluciones.*')" icono="prestamo">
                Préstamos
            </x-nav-enlace>

            <x-nav-enlace :href="route('vencidos.index')" :activo="request()->routeIs('vencidos.*')" icono="alerta"
                          :insignia="$vencidosPendientes ?? null">
                Vencidos
            </x-nav-enlace>

            <x-nav-enlace :href="route('equipos.index')" :activo="request()->routeIs('equipos.*')" icono="equipo">
                Equipo
            </x-nav-enlace>

            <x-nav-enlace :href="route('alumnos.index')" :activo="request()->routeIs('alumnos.*')" icono="alumno">
                Alumnos
            </x-nav-enlace>

            <x-nav-enlace :href="route('consulta.index')" :activo="false" icono="celular">
                Consulta pública
            </x-nav-enlace>
        </nav>

        {{-- Pie de la barra lateral, solo en escritorio. --}}
        <div class="mt-auto hidden border-t border-white/10 p-4 lg:block">
            <div class="flex items-center gap-3 rounded-2xl bg-white/5 p-3">
                <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-amber-300 to-amber-500 text-sm font-bold text-tinta-950">
                    {{ Str::of(auth()->user()->nombre)->substr(0, 1)->upper() }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-sm font-semibold text-white">{{ auth()->user()->nombre }}</span>
                    <span class="block truncate text-[11px] text-tinta-300">{{ auth()->user()->rol->etiqueta() }}</span>
                </span>
            </div>

            <form method="POST" action="{{ route('sesion.destroy') }}" class="mt-2">
                @csrf
                <button type="submit"
                        class="w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-tinta-300 transition hover:bg-white/10 hover:text-white">
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ---------------------------------------------------------------- --}}
    {{-- Contenido                                                        --}}
    {{-- ---------------------------------------------------------------- --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="border-b border-tinta-200/70 bg-white/80 px-5 py-5 backdrop-blur sm:px-8 sm:py-6">
            <div class="flex flex-wrap items-end justify-between gap-x-6 gap-y-3">
                <div class="min-w-0">
                    <h1 class="text-xl font-extrabold tracking-tight text-tinta-950 sm:text-2xl">
                        {{ $titulo ?? 'Laboratorio de Cómputo' }}
                    </h1>
                    @if ($subtitulo)
                        <p class="mt-1 max-w-2xl text-sm text-tinta-500">{{ $subtitulo }}</p>
                    @endif
                </div>

                @isset($acciones)
                    <div class="flex flex-wrap items-center gap-2">
                        {{ $acciones }}
                    </div>
                @endisset
            </div>
        </header>

        <main class="flex-1 px-5 py-6 sm:px-8 sm:py-8">
            <x-alertas class="mb-6" />

            {{ $slot }}
        </main>

        <footer class="border-t border-tinta-200/70 px-5 py-5 text-xs text-tinta-400 sm:px-8">
            Laboratorio de Cómputo · CESBA — Centro de Estudios Superiores del Bajío ·
            Proyecto de clase de Licenciatura en Sistemas Computacionales
        </footer>
    </div>
</div>
</body>
</html>
