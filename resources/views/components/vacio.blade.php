@props([
    'titulo' => 'No hay nada que mostrar',
    'icono' => 'buscar',
])

{{--
    Estado vacío de un listado.

    Una tabla sin filas no debe verse como un error: debe decirle al usuario
    qué pasó y qué puede hacer. El slot lleva la explicación y el slot
    `accion` un botón opcional.
--}}
<div {{ $attributes->merge(['class' => 'flex flex-col items-center px-6 py-14 text-center']) }}>
    <span class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-tinta-100 to-tinta-200 text-tinta-500">
        <x-icono :nombre="$icono" class="h-7 w-7" />
    </span>

    <p class="mt-4 text-base font-bold text-tinta-900">{{ $titulo }}</p>

    @if (trim($slot) !== '')
        <p class="mt-1 max-w-sm text-sm text-tinta-500">{{ $slot }}</p>
    @endif

    @isset($accion)
        <div class="mt-5">{{ $accion }}</div>
    @endisset
</div>
