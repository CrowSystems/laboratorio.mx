@props([
    'nombre',
])

{{--
    Catálogo de iconos del sistema, en SVG escrito a mano.

    Un solo componente con un @switch en lugar de nueve archivos SVG. Así, si
    hay que cambiar el grosor de la línea de todos los iconos, se cambia una vez.
    Los iconos heredan el color (currentColor) y el tamaño se pasa por clase:
    <x-icono nombre="equipo" class="h-5 w-5" />
--}}
<svg {{ $attributes->merge(['class' => 'h-5 w-5']) }}
     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($nombre)
        @case('tablero')
            <path d="M3.5 3.5h7v7h-7zM13.5 3.5h7v4h-7zM13.5 10.5h7v10h-7zM3.5 13.5h7v7h-7z" />
            @break

        @case('prestamo')
            <path d="M4 7h11m0 0-3-3m3 3-3 3M20 17H9m0 0 3-3m-3 3 3 3" />
            @break

        @case('alerta')
            <path d="M12 3.5 21 19.5H3zM12 9.5v4.5M12 17h.01" />
            @break

        @case('equipo')
            <rect x="2.5" y="4.5" width="19" height="12" rx="2" />
            <path d="M9 20.5h6M12 16.5v4" />
            @break

        @case('alumno')
            <path d="M12 3.5 22 8l-10 4.5L2 8z" />
            <path d="M6 10.2V15c0 1.7 2.7 3 6 3s6-1.3 6-3v-4.8M19.5 9.2v5.3" />
            @break

        @case('celular')
            <rect x="6.5" y="2.5" width="11" height="19" rx="2.5" />
            <path d="M10.5 18.5h3" />
            @break

        @case('buscar')
            <circle cx="11" cy="11" r="6.5" />
            <path d="m16 16 4.5 4.5" />
            @break

        @case('mas')
            <path d="M12 5v14M5 12h14" />
            @break

        @case('regresar')
            <path d="M20 11a8 8 0 1 0-2.3 5.6M20 5v6h-6" />
            @break

        @case('reloj')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M12 7.5V12l3 2" />
            @break

        @case('cheque')
            <path d="m4.5 12.5 5 5 10-11" />
            @break

        @case('candado')
            <rect x="4.5" y="10.5" width="15" height="10" rx="2" />
            <path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" />
            @break

        @case('flecha-izq')
            <path d="M19 12H5m0 0 6-6m-6 6 6 6" />
            @break

        @default
            <circle cx="12" cy="12" r="8.5" />
    @endswitch
</svg>
