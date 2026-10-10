@props([
    'etiqueta',
    'valor',
    'icono' => null,
    'tono' => 'neutro',
    'nota' => null,
    'href' => null,
])

{{--
    Una de las tarjetas de número grande del tablero.

    El `tono` solo cambia el color del acento, nunca el tamaño ni la posición,
    para que las seis tarjetas se lean como una sola fila y el ojo compare
    números, no colores. Si se le pasa `href`, la tarjeta completa se vuelve
    un enlace al listado correspondiente.
--}}
@php
    $tonos = [
        'neutro' => ['acento' => 'text-tinta-500', 'barra' => 'from-tinta-300 to-tinta-500', 'fondo' => 'bg-tinta-50'],
        'bien' => ['acento' => 'text-emerald-600', 'barra' => 'from-emerald-300 to-emerald-500', 'fondo' => 'bg-emerald-50'],
        'fuera' => ['acento' => 'text-sky-600', 'barra' => 'from-sky-300 to-sky-500', 'fondo' => 'bg-sky-50'],
        'taller' => ['acento' => 'text-amber-600', 'barra' => 'from-amber-300 to-amber-500', 'fondo' => 'bg-amber-50'],
        'alerta' => ['acento' => 'text-rose-600', 'barra' => 'from-rose-400 to-rose-600', 'fondo' => 'bg-rose-50'],
    ];

    $t = $tonos[$tono] ?? $tonos['neutro'];

    $clases = 'relative block overflow-hidden rounded-2xl bg-white p-4 shadow-tarjeta ring-1 ring-tinta-900/[0.06] transition';

    if ($href) {
        $clases .= ' hover:-translate-y-0.5 hover:shadow-lg outline-none focus-visible:ring-2 focus-visible:ring-tinta-500';
    }
@endphp

@if ($href)
    <a href="{{ $href }}" class="{{ $clases }}">
        @include('components.partes.indicador-cuerpo', ['t' => $t])
    </a>
@else
    <div class="{{ $clases }}">
        @include('components.partes.indicador-cuerpo', ['t' => $t])
    </div>
@endif
