@props([
    'variante' => 'primario',
    'href' => null,
    'icono' => null,
    'tipo' => 'submit',
])

{{--
    Botón del sistema, en sus cuatro variantes.

    Si recibe `href` dibuja un <a>; si no, un <button>. Es el mismo componente
    porque visualmente son lo mismo, y así nunca hay un enlace que se vea como
    botón primario y otro que se vea distinto.
--}}
@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60';

    $variantes = [
        'primario' => 'bg-tinta-800 text-white shadow-sm hover:bg-tinta-900 focus-visible:ring-tinta-500',
        'secundario' => 'bg-white text-tinta-800 ring-1 ring-inset ring-tinta-200 hover:bg-tinta-50 focus-visible:ring-tinta-400',
        'acento' => 'bg-gradient-to-br from-amber-400 to-amber-500 text-tinta-950 shadow-sm shadow-amber-500/30 hover:from-amber-300 hover:to-amber-400 focus-visible:ring-amber-500',
        'peligro' => 'bg-rose-600 text-white shadow-sm hover:bg-rose-700 focus-visible:ring-rose-500',
        'fantasma' => 'text-tinta-600 hover:bg-tinta-100 hover:text-tinta-900 focus-visible:ring-tinta-400',
    ];

    $clases = $base.' '.($variantes[$variante] ?? $variantes['primario']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $clases]) }}>
        @if ($icono)<x-icono :nombre="$icono" class="h-[18px] w-[18px]" />@endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $tipo }}" {{ $attributes->merge(['class' => $clases]) }}>
        @if ($icono)<x-icono :nombre="$icono" class="h-[18px] w-[18px]" />@endif
        {{ $slot }}
    </button>
@endif
