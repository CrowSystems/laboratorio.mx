@props([
    'titulo' => null,
    'descripcion' => null,
    'relleno' => true,
])

{{--
    La caja blanca sobre la que se apoya casi todo el sistema.

    El slot opcional `acciones` permite poner un botón a la derecha del título:

        <x-tarjeta titulo="Historial">
            <x-slot:acciones>
                <x-boton variante="secundario" :href="..." >Ver todo</x-boton>
            </x-slot:acciones>
            ...
        </x-tarjeta>
--}}
<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl bg-white shadow-tarjeta ring-1 ring-tinta-900/[0.06]']) }}>
    @if ($titulo || isset($acciones))
        <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-2 border-b border-tinta-100 px-5 py-4">
            <div class="min-w-0">
                @if ($titulo)
                    <h2 class="text-sm font-bold tracking-wide text-tinta-950 uppercase">{{ $titulo }}</h2>
                @endif
                @if ($descripcion)
                    <p class="mt-0.5 text-sm text-tinta-500">{{ $descripcion }}</p>
                @endif
            </div>

            @isset($acciones)
                <div class="flex shrink-0 items-center gap-2">{{ $acciones }}</div>
            @endisset
        </header>
    @endif

    <div class="{{ $relleno ? 'p-5' : '' }}">
        {{ $slot }}
    </div>
</section>
