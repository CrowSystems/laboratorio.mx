{{-- Alta de equipo. Solo el encargado llega aquí (middleware 'rol:admin'). --}}
<x-layouts.app
    titulo="Registrar equipo"
    subtitulo="Da de alta una pieza nueva en el catálogo del laboratorio.">

    <div class="max-w-3xl">
        <x-tarjeta>
            <form method="POST" action="{{ route('equipos.store') }}" class="space-y-6">
                @csrf

                @include('equipos._formulario', ['estados' => $estados])

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-tinta-100 pt-5">
                    <x-boton variante="secundario" :href="route('equipos.index')">Cancelar</x-boton>
                    <x-boton variante="primario">Guardar equipo</x-boton>
                </div>
            </form>
        </x-tarjeta>
    </div>

</x-layouts.app>
