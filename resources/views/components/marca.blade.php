{{--
    Logotipo del sistema, dibujado con SVG en línea.

    No hay un solo archivo de imagen en el proyecto: el símbolo es un SVG
    escrito aquí mismo, así que escala sin pixelarse y hereda el color del
    texto que lo rodea. Es también la forma más rápida de cargar: no hay una
    petición HTTP extra al servidor.
--}}
<div {{ $attributes->merge(['class' => 'flex items-center gap-3']) }}>
    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-tinta-400 via-tinta-600 to-tinta-900 shadow-lg shadow-tinta-900/30 ring-1 ring-white/20">
        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            {{-- Monitor: el laboratorio de cómputo --}}
            <rect x="2.5" y="4" width="19" height="12" rx="2" class="text-white/90" stroke="currentColor" />
            <path d="M9 20h6M12 16v4" class="text-white/70" stroke="currentColor" />
            {{-- Flecha de préstamo saliendo de la pantalla --}}
            <path d="M8 10h7m0 0-2.5-2.5M15 10l-2.5 2.5" class="text-amber-300" stroke="currentColor" stroke-width="2" />
        </svg>
    </span>

    {{-- Dos líneas cortas: el texto tiene que caber también en un celular
         de 360 píxeles sin cortarse con puntos suspensivos. --}}
    <span class="min-w-0">
        <span class="block truncate text-[15px] leading-tight font-bold text-white">Laboratorio CESBA</span>
        <span class="block truncate text-[10px] font-semibold tracking-[0.16em] text-tinta-300 uppercase">Préstamo de equipo</span>
    </span>
</div>
