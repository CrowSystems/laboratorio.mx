@props([
    'href',
    'activo' => false,
    'icono' => null,
    'insignia' => null,
])

{{--
    Un enlace de la barra lateral.

    Resuelve de un solo golpe tres cosas que de otro modo se repetirían seis
    veces: el estado activo, el icono y la insignia con el contador.
--}}
<a href="{{ $href }}"
   @if ($activo) aria-current="page" @endif
   class="group flex shrink-0 items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold whitespace-nowrap transition outline-none focus-visible:ring-2 focus-visible:ring-amber-300 lg:gap-3
          {{ $activo
              ? 'bg-white text-tinta-950 shadow-sm'
              : 'text-tinta-300 hover:bg-white/10 hover:text-white' }}">

    @if ($icono)
        <span class="shrink-0 {{ $activo ? 'text-tinta-600' : 'text-tinta-400 group-hover:text-amber-300' }}">
            <x-icono :nombre="$icono" class="h-[18px] w-[18px]" />
        </span>
    @endif

    <span class="flex-1">{{ $slot }}</span>

    @if (filled($insignia) && $insignia > 0)
        <span class="shrink-0 rounded-full bg-rose-500 px-2 py-0.5 text-[11px] font-bold text-white tabular-nums">
            {{ $insignia }}
        </span>
    @endif
</a>
