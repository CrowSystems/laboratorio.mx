@props([
    'clases' => 'bg-tinta-100 text-tinta-700 ring-tinta-500/20',
])

{{--
    Insignia de estado (la "pastilla" de color).

    Las clases de color NO se deciden aquí: las entrega el enum
    (EstadoEquipo::clasesInsignia(), CondicionDevolucion::clasesInsignia()).
    De ese modo el color de "prestado" es el mismo en el tablero, en el
    catálogo y en el detalle, porque sale del mismo método de PHP.

    Uso:  <x-insignia :clases="$equipo->estado->clasesInsignia()">
              {{ $equipo->estado->etiqueta() }}
          </x-insignia>
--}}
<span {{ $attributes->merge([
    'class' => 'inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset whitespace-nowrap '.$clases,
]) }}>
    {{ $slot }}
</span>
