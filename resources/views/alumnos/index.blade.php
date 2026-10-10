{{-- Padrón de alumnos. --}}
<x-layouts.app
    titulo="Alumnos"
    :subtitulo="$alumnos->total().' alumnos en el padrón del laboratorio'">

    <x-slot:acciones>
        @can('create', App\Models\Alumno::class)
            <x-boton variante="acento" :href="route('alumnos.create')" icono="mas">Registrar alumno</x-boton>
        @endcan
    </x-slot:acciones>

    <form method="GET" action="{{ route('alumnos.index') }}"
          class="mb-5 rounded-2xl bg-white p-4 shadow-tarjeta ring-1 ring-tinta-900/[0.06]">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="q" class="block text-sm font-semibold text-tinta-800">Buscar</label>
                <input id="q" name="q" type="search" value="{{ $busqueda }}"
                       placeholder="Matrícula o nombre del alumno"
                       class="campo-base mt-1.5">
            </div>

            <label class="flex items-center gap-2.5 py-2.5 text-sm font-medium text-tinta-700 sm:pb-3">
                <input type="checkbox" name="adeudos" value="1" @checked($soloAdeudos)
                       class="h-4 w-4 rounded border-tinta-300 text-tinta-700 focus:ring-tinta-500">
                Solo con adeudo vencido
            </label>

            <div class="flex gap-2">
                <x-boton variante="primario" icono="buscar">Filtrar</x-boton>

                @if (filled($busqueda) || $soloAdeudos)
                    <x-boton variante="secundario" :href="route('alumnos.index')">Limpiar</x-boton>
                @endif
            </div>
        </div>
    </form>

    <x-tarjeta :relleno="false">
        @if ($alumnos->isEmpty())
            <x-vacio titulo="Ningún alumno coincide">
                @if ($soloAdeudos)
                    Buena noticia: nadie trae equipo atrasado.
                @else
                    Revisa la matrícula o el nombre que escribiste.
                @endif
            </x-vacio>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[42rem] text-left text-sm">
                    <thead class="border-b border-tinta-200 bg-tinta-50/60 text-[11px] font-bold tracking-wider text-tinta-500 uppercase">
                        <tr>
                            <th scope="col" class="px-5 py-3">Matrícula</th>
                            <th scope="col" class="px-5 py-3">Alumno</th>
                            <th scope="col" class="px-5 py-3">Semestre</th>
                            <th scope="col" class="px-5 py-3">Equipo en su poder</th>
                            <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-tinta-100">
                        @foreach ($alumnos as $alumno)
                            <tr class="transition hover:bg-tinta-50/70">
                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('alumnos.show', $alumno) }}"
                                       class="font-mono text-sm font-bold text-tinta-800 underline decoration-tinta-200 underline-offset-2 hover:text-tinta-950">
                                        {{ $alumno->matricula }}
                                    </a>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <p class="font-semibold text-tinta-950">{{ $alumno->nombre }}</p>
                                    <p class="text-xs text-tinta-500">{{ $alumno->carrera }}</p>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <span class="font-semibold text-tinta-700 tabular-nums">{{ $alumno->semestre }}.º</span>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    {{--
                                        Los dos contadores vienen de withCount() en el
                                        controlador. La vista no consulta la base de datos.
                                    --}}
                                    @if ($alumno->vencidos_count > 0)
                                        <x-insignia clases="bg-rose-50 text-rose-700 ring-rose-600/20">
                                            {{ $alumno->vencidos_count }} vencido(s)
                                        </x-insignia>
                                    @elseif ($alumno->abiertos_count > 0)
                                        <x-insignia clases="bg-sky-50 text-sky-700 ring-sky-600/20">
                                            {{ $alumno->abiertos_count }} prestado(s)
                                        </x-insignia>
                                    @else
                                        <span class="text-xs text-tinta-400">Nada pendiente</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-right align-middle whitespace-nowrap">
                                    @can('update', $alumno)
                                        <x-boton variante="fantasma" :href="route('alumnos.edit', $alumno)" class="px-3 py-1.5 text-xs">
                                            Editar
                                        </x-boton>
                                    @endcan

                                    <x-boton variante="secundario" :href="route('alumnos.show', $alumno)" class="px-3 py-1.5 text-xs">
                                        Ver
                                    </x-boton>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($alumnos->hasPages())
                <div class="border-t border-tinta-100 px-5 py-4">{{ $alumnos->links() }}</div>
            @endif
        @endif
    </x-tarjeta>

</x-layouts.app>
