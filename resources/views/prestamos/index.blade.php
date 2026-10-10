{{-- Listado de préstamos con tres filtros. --}}
<x-layouts.app
    titulo="Préstamos"
    :subtitulo="$prestamos->total().' préstamos en este filtro'">

    <x-slot:acciones>
        <x-boton variante="acento" :href="route('prestamos.create')" icono="mas">Registrar préstamo</x-boton>
    </x-slot:acciones>

    {{-- Pestañas de filtro. Son enlaces GET, no JavaScript: cada pestaña es
         una URL propia que se puede compartir. --}}
    <nav class="mb-5 flex gap-1 overflow-x-auto rounded-2xl bg-white p-1.5 shadow-tarjeta ring-1 ring-tinta-900/[0.06]"
         aria-label="Filtro de préstamos">
        @foreach (['abiertos' => 'Sin devolver', 'devueltos' => 'Ya devueltos', 'todos' => 'Todos'] as $clave => $texto)
            <a href="{{ route('prestamos.index', ['filtro' => $clave]) }}"
               @if ($filtro === $clave) aria-current="page" @endif
               class="shrink-0 rounded-xl px-4 py-2 text-sm font-semibold whitespace-nowrap transition
                      {{ $filtro === $clave
                          ? 'bg-tinta-800 text-white shadow-sm'
                          : 'text-tinta-600 hover:bg-tinta-50' }}">
                {{ $texto }}
            </a>
        @endforeach
    </nav>

    <x-tarjeta :relleno="false">
        @if ($prestamos->isEmpty())
            <x-vacio titulo="No hay préstamos en este filtro" icono="prestamo">
                @if ($filtro === 'abiertos')
                    Todo el equipo está en el laboratorio. Nadie trae nada fuera.
                @else
                    Prueba con otro filtro o registra el primer préstamo.
                @endif

                <x-slot:accion>
                    <x-boton variante="acento" :href="route('prestamos.create')" icono="mas">Registrar préstamo</x-boton>
                </x-slot:accion>
            </x-vacio>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[52rem] text-left text-sm">
                    <thead class="border-b border-tinta-200 bg-tinta-50/60 text-[11px] font-bold tracking-wider text-tinta-500 uppercase">
                        <tr>
                            <th scope="col" class="px-5 py-3">Equipo</th>
                            <th scope="col" class="px-5 py-3">Alumno</th>
                            <th scope="col" class="px-5 py-3">Salida</th>
                            <th scope="col" class="px-5 py-3">Compromiso</th>
                            <th scope="col" class="px-5 py-3">Situación</th>
                            <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-tinta-100">
                        @foreach ($prestamos as $prestamo)
                            <tr class="transition hover:bg-tinta-50/70 {{ $prestamo->esta_vencido ? 'bg-rose-50/40' : '' }}">
                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('equipos.show', $prestamo->equipo) }}"
                                       class="font-mono text-sm font-bold text-tinta-800 hover:underline">
                                        {{ $prestamo->equipo->numero_inventario }}
                                    </a>
                                    <p class="text-xs text-tinta-500">{{ $prestamo->equipo->nombreCompleto() }}</p>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('alumnos.show', $prestamo->alumno) }}"
                                       class="font-semibold text-tinta-950 hover:underline">
                                        {{ $prestamo->alumno->nombre }}
                                    </a>
                                    <p class="font-mono text-xs text-tinta-500">{{ $prestamo->alumno->matricula }}</p>
                                </td>

                                <td class="px-5 py-3.5 align-middle whitespace-nowrap text-tinta-700">
                                    {{ $prestamo->fecha_salida->translatedFormat('d M Y') }}
                                    <span class="block text-xs text-tinta-400">{{ $prestamo->fecha_salida->format('H:i') }} h</span>
                                </td>

                                <td class="px-5 py-3.5 align-middle whitespace-nowrap text-tinta-700">
                                    {{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    @if (! $prestamo->esta_abierto)
                                        <x-insignia :clases="$prestamo->condicion_devolucion->clasesInsignia()">
                                            {{ $prestamo->condicion_devolucion->etiqueta() }}
                                        </x-insignia>
                                    @elseif ($prestamo->esta_vencido)
                                        <x-atraso :dias="$prestamo->dias_atraso" />
                                    @else
                                        <x-insignia clases="bg-sky-50 text-sky-700 ring-sky-600/20">Fuera, a tiempo</x-insignia>
                                    @endif
                                </td>

                                <td class="px-5 py-3.5 text-right align-middle whitespace-nowrap">
                                    @can('devolver', $prestamo)
                                        <x-boton variante="primario" :href="route('devoluciones.create', $prestamo)"
                                                 icono="regresar" class="px-3 py-1.5 text-xs">
                                            Recibir
                                        </x-boton>
                                    @endcan

                                    <x-boton variante="secundario" :href="route('prestamos.show', $prestamo)" class="px-3 py-1.5 text-xs">
                                        Ver
                                    </x-boton>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($prestamos->hasPages())
                <div class="border-t border-tinta-100 px-5 py-4">{{ $prestamos->links() }}</div>
            @endif
        @endif
    </x-tarjeta>

</x-layouts.app>
