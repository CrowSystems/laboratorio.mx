{{-- Alta de alumno. --}}
<x-layouts.app
    titulo="Registrar alumno"
    subtitulo="Agrega un alumno al padrón para poder prestarle equipo.">

    <div class="max-w-3xl">
        <x-tarjeta>
            <form method="POST" action="{{ route('alumnos.store') }}" class="space-y-6">
                @csrf

                @include('alumnos._formulario')

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-tinta-100 pt-5">
                    <x-boton variante="secundario" :href="route('alumnos.index')">Cancelar</x-boton>
                    <x-boton variante="primario">Guardar alumno</x-boton>
                </div>
            </form>
        </x-tarjeta>
    </div>

</x-layouts.app>
