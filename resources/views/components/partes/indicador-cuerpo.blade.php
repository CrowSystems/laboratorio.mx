{{--
    Contenido interior de <x-indicador>.

    Está en un archivo aparte porque la tarjeta a veces es un <a> y a veces un
    <div>, y así el contenido no se escribe dos veces. No es un componente, es
    un @include: solo se usa desde components/indicador.blade.php.

    Variables que recibe: $t (paleta del tono), $etiqueta, $valor, $icono, $nota.
--}}
{{-- Franja de color en el borde superior: degradado CSS, sin imágenes. --}}
<span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $t['barra'] }}"></span>

<div class="flex items-start justify-between gap-3">
    <p class="text-[11px] font-bold tracking-[0.12em] text-tinta-500 uppercase">{{ $etiqueta }}</p>

    @if ($icono)
        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg {{ $t['fondo'] }} {{ $t['acento'] }}">
            <x-icono :nombre="$icono" class="h-[17px] w-[17px]" />
        </span>
    @endif
</div>

<p class="mt-2 text-3xl font-extrabold tracking-tight text-tinta-950 tabular-nums">{{ $valor }}</p>

@if ($nota)
    <p class="mt-1 text-xs {{ $t['acento'] }}">{{ $nota }}</p>
@endif
