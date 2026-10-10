{{-- Edición de los datos de un alumno. --}}
<x-layouts.app
    :titulo="'Editar a '.$alumno->nombre"
    :subtitulo="$alumno->matricula.' · '.$alumno->carrera">

    <div class="max-w-3xl space-y-5">
        <x-tarjeta>
            <form method="POST" action="{{ route('alumnos.update', $alumno) }}" class="space-y-6">
                @csrf
                @method('PUT')

                @include('alumnos._formulario', ['alumno' => $alumno])

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-tinta-100 pt-5">
                    <x-boton variante="secundario" :href="route('alumnos.show', $alumno)">Cancelar</x-boton>
                    <x-boton variante="primario">Guardar cambios</x-boton>
                </div>
            </form>
        </x-tarjeta>

        {{--
            El botón de eliminar solo aparece si AlumnoPolicy::delete() lo
            permite, y esa Policy lo niega en cuanto el alumno tiene historial
            de préstamos.
        --}}
        @can('delete', $alumno)
            <x-tarjeta titulo="Eliminar del padrón" descripcion="Solo se puede eliminar a un alumno que nunca ha recibido equipo.">
                <form method="POST" action="{{ route('alumnos.destroy', $alumno) }}"
                      onsubmit="return confirm('¿Eliminar el registro de {{ $alumno->nombre }}?');">
                    @csrf
                    @method('DELETE')

                    <x-boton variante="peligro">Eliminar a {{ $alumno->nombre }}</x-boton>
                </form>
            </x-tarjeta>
        @endcan
    </div>

</x-layouts.app>
