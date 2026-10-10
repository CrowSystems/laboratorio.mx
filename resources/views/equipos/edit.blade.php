{{-- Edición de la ficha de un equipo. --}}
<x-layouts.app
    :titulo="'Editar '.$equipo->numero_inventario"
    :subtitulo="$equipo->nombreCompleto()">

    <div class="max-w-3xl space-y-5">
        <x-tarjeta>
            <form method="POST" action="{{ route('equipos.update', $equipo) }}" class="space-y-6">
                @csrf
                {{--
                    Los formularios HTML solo saben enviar GET y POST. @method('PUT')
                    agrega un campo oculto _method=PUT que Laravel lee para saber
                    que, en realidad, esta petición es un PUT.
                --}}
                @method('PUT')

                @include('equipos._formulario', ['equipo' => $equipo, 'estados' => $estados])

                <div class="flex flex-wrap items-center justify-end gap-3 border-t border-tinta-100 pt-5">
                    <x-boton variante="secundario" :href="route('equipos.show', $equipo)">Cancelar</x-boton>
                    <x-boton variante="primario">Guardar cambios</x-boton>
                </div>
            </form>
        </x-tarjeta>

        {{-- Zona de baja, separada a propósito del formulario principal. --}}
        @can('delete', $equipo)
            <x-tarjeta titulo="Dar de baja" descripcion="Un equipo con historial de préstamos no se borra: se marca como baja para conservar el registro.">
                <form method="POST" action="{{ route('equipos.destroy', $equipo) }}"
                      onsubmit="return confirm('¿Seguro que quieres dar de baja el equipo {{ $equipo->numero_inventario }}?');">
                    @csrf
                    @method('DELETE')

                    <x-boton variante="peligro">Dar de baja {{ $equipo->numero_inventario }}</x-boton>
                </form>
            </x-tarjeta>
        @endcan
    </div>

</x-layouts.app>
