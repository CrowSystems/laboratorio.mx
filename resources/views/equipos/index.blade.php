{{-- Catálogo de equipo, con búsqueda y filtro por estado. --}}
<x-layouts.app
    titulo="Catálogo de equipo"
    :subtitulo="$equipos->total().' equipos registrados en el laboratorio'">

    <x-slot:acciones>
        {{--
            @can consulta EquipoPolicy::create(). Ocultar el botón es cortesía
            con el auxiliar, para que no vea una puerta que no puede abrir; el
            candado de verdad está en la Policy y en el middleware 'rol:admin'
            de la ruta.
        --}}
        @can('create', App\Models\Equipo::class)
            <x-boton variante="acento" :href="route('equipos.create')" icono="mas">
                Registrar equipo
            </x-boton>
        @endcan
    </x-slot:acciones>

    {{-- Barra de filtros. Es un GET: así el filtro queda en la URL y se puede
         compartir o guardar en favoritos. --}}
    <form method="GET" action="{{ route('equipos.index') }}"
          class="mb-5 rounded-2xl bg-white p-4 shadow-tarjeta ring-1 ring-tinta-900/[0.06]">

        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="flex-1">
                <label for="q" class="block text-sm font-semibold text-tinta-800">Buscar</label>
                <input id="q" name="q" type="search" value="{{ $busqueda }}"
                       placeholder="Número de inventario, tipo, marca o modelo"
                       class="campo-base mt-1.5">
            </div>

            <div class="sm:w-56">
                <label for="estado" class="block text-sm font-semibold text-tinta-800">Estado</label>
                <select id="estado" name="estado" class="campo-base mt-1.5">
                    <option value="">Todos los estados</option>
                    @foreach (App\Enums\EstadoEquipo::paraSelect() as $valor => $texto)
                        <option value="{{ $valor }}" @selected($estadoActivo?->value === $valor)>{{ $texto }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <x-boton variante="primario" icono="buscar">Filtrar</x-boton>

                @if (filled($busqueda) || $estadoActivo)
                    <x-boton variante="secundario" :href="route('equipos.index')">Limpiar</x-boton>
                @endif
            </div>
        </div>
    </form>

    <x-tarjeta :relleno="false">
        @if ($equipos->isEmpty())
            <x-vacio titulo="No hay equipo que coincida">
                @if (filled($busqueda) || $estadoActivo)
                    Prueba con otro término de búsqueda o quita el filtro de estado.
                @else
                    El catálogo está vacío. Registra el primer equipo para empezar.
                @endif

                @can('create', App\Models\Equipo::class)
                    <x-slot:accion>
                        <x-boton variante="acento" :href="route('equipos.create')" icono="mas">Registrar equipo</x-boton>
                    </x-slot:accion>
                @endcan
            </x-vacio>
        @else
            {{-- En pantalla chica la tabla se desliza en horizontal, que es
                 mejor que apretar ocho columnas en 360 píxeles. --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[46rem] text-left text-sm">
                    <thead class="border-b border-tinta-200 bg-tinta-50/60 text-[11px] font-bold tracking-wider text-tinta-500 uppercase">
                        <tr>
                            <th scope="col" class="px-5 py-3">Inventario</th>
                            <th scope="col" class="px-5 py-3">Equipo</th>
                            <th scope="col" class="px-5 py-3">Estado</th>
                            <th scope="col" class="px-5 py-3">Restricción</th>
                            <th scope="col" class="px-5 py-3">Con quién está</th>
                            <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-tinta-100">
                        @foreach ($equipos as $equipo)
                            <tr class="transition hover:bg-tinta-50/70">
                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('equipos.show', $equipo) }}"
                                       class="font-mono text-sm font-bold text-tinta-800 underline decoration-tinta-200 underline-offset-2 hover:text-tinta-950">
                                        {{ $equipo->numero_inventario }}
                                    </a>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <p class="font-semibold text-tinta-950">{{ $equipo->tipo }}</p>
                                    <p class="text-xs text-tinta-500">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <x-insignia :clases="$equipo->estado->clasesInsignia()">
                                        {{ $equipo->estado->etiqueta() }}
                                    </x-insignia>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    @if ($equipo->estaRestringido())
                                        <x-insignia clases="bg-violet-50 text-violet-700 ring-violet-600/20">
                                            {{ $equipo->semestre_minimo }}.º semestre o más
                                        </x-insignia>
                                    @else
                                        <span class="text-xs text-tinta-400">Sin restricción</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    @if ($equipo->prestamoVigente)
                                        <a href="{{ route('prestamos.show', $equipo->prestamoVigente) }}"
                                           class="text-tinta-700 underline decoration-tinta-200 underline-offset-2 hover:text-tinta-950">
                                            {{ $equipo->prestamoVigente->alumno->nombre }}
                                        </a>
                                        <p class="text-xs text-tinta-500">
                                            entrega {{ $equipo->prestamoVigente->fecha_compromiso->translatedFormat('d M Y') }}
                                        </p>
                                    @else
                                        <span class="text-xs text-tinta-400">En el laboratorio</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-right align-middle whitespace-nowrap">
                                    @can('update', $equipo)
                                        <x-boton variante="fantasma" :href="route('equipos.edit', $equipo)" class="px-3 py-1.5 text-xs">
                                            Editar
                                        </x-boton>
                                    @endcan

                                    <x-boton variante="secundario" :href="route('equipos.show', $equipo)" class="px-3 py-1.5 text-xs">
                                        Ver
                                    </x-boton>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación. El objeto $equipos es un paginador, así que sabe
                 dibujar sus propios enlaces, ya traducidos por lang/es. --}}
            @if ($equipos->hasPages())
                <div class="border-t border-tinta-100 px-5 py-4">
                    {{ $equipos->links() }}
                </div>
            @endif
        @endif
    </x-tarjeta>

</x-layouts.app>
