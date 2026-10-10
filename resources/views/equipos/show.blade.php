{{-- Ficha de un equipo con su historial de préstamos. --}}
<x-layouts.app
    :titulo="$equipo->numero_inventario"
    :subtitulo="$equipo->nombreCompleto()">

    <x-slot:acciones>
        <x-boton variante="secundario" :href="route('equipos.index')" icono="flecha-izq">Al catálogo</x-boton>

        @can('update', $equipo)
            <x-boton variante="primario" :href="route('equipos.edit', $equipo)">Editar ficha</x-boton>
        @endcan
    </x-slot:acciones>

    <div class="grid items-start gap-5 lg:grid-cols-3">

        {{-- Datos del equipo --}}
        <x-tarjeta titulo="Ficha del equipo" class="lg:col-span-1">
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Estado</dt>
                    <dd class="mt-1">
                        <x-insignia :clases="$equipo->estado->clasesInsignia()">
                            {{ $equipo->estado->etiqueta() }}
                        </x-insignia>
                    </dd>
                </div>

                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Tipo</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">{{ $equipo->tipo }}</dd>
                </div>

                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Marca y modelo</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">{{ $equipo->marca }} {{ $equipo->modelo }}</dd>
                </div>

                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Restricción por semestre</dt>
                    <dd class="mt-1">
                        @if ($equipo->estaRestringido())
                            <x-insignia clases="bg-violet-50 text-violet-700 ring-violet-600/20">
                                Solo de {{ $equipo->semestre_minimo }}.º semestre en adelante
                            </x-insignia>
                        @else
                            <span class="text-tinta-600">Lo puede llevar cualquier alumno.</span>
                        @endif
                    </dd>
                </div>

                @if (filled($equipo->descripcion))
                    <div>
                        <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Descripción</dt>
                        <dd class="mt-0.5 leading-relaxed text-tinta-700">{{ $equipo->descripcion }}</dd>
                    </div>
                @endif

                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Dado de alta</dt>
                    <dd class="mt-0.5 text-tinta-700">{{ $equipo->created_at->translatedFormat('d \d\e F \d\e Y') }}</dd>
                </div>
            </dl>

            @if ($equipo->estado->permitePrestamo())
                <div class="mt-6 border-t border-tinta-100 pt-5">
                    <x-boton variante="acento" :href="route('prestamos.create')" icono="prestamo" class="w-full">
                        Prestar este equipo
                    </x-boton>
                </div>
            @endif
        </x-tarjeta>

        {{-- Historial --}}
        <x-tarjeta
            class="lg:col-span-2"
            titulo="Historial de préstamos"
            :descripcion="$historial->total().' movimientos registrados'"
            :relleno="false">

            @if ($historial->isEmpty())
                <x-vacio titulo="Este equipo no se ha prestado" icono="prestamo">
                    Cuando salga por primera vez aparecerá aquí con el nombre del alumno
                    y las fechas.
                </x-vacio>
            @else
                <ul class="divide-y divide-tinta-100">
                    @foreach ($historial as $prestamo)
                        <li class="px-5 py-4">
                            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                                <div class="min-w-0">
                                    <a href="{{ route('alumnos.show', $prestamo->alumno) }}"
                                       class="text-sm font-bold text-tinta-950 hover:underline">
                                        {{ $prestamo->alumno->nombre }}
                                    </a>
                                    <p class="font-mono text-xs text-tinta-500">{{ $prestamo->alumno->matricula }}</p>
                                </div>

                                @if ($prestamo->esta_abierto)
                                    @if ($prestamo->esta_vencido)
                                        <x-atraso :dias="$prestamo->dias_atraso" />
                                    @else
                                        <x-insignia clases="bg-sky-50 text-sky-700 ring-sky-600/20">Fuera del laboratorio</x-insignia>
                                    @endif
                                @else
                                    <x-insignia :clases="$prestamo->condicion_devolucion->clasesInsignia()">
                                        {{ $prestamo->condicion_devolucion->etiqueta() }}
                                    </x-insignia>
                                @endif
                            </div>

                            <dl class="mt-3 grid grid-cols-2 gap-x-4 gap-y-2 text-xs sm:grid-cols-4">
                                <div>
                                    <dt class="font-bold tracking-wide text-tinta-400 uppercase">Salida</dt>
                                    <dd class="mt-0.5 text-tinta-700">{{ $prestamo->fecha_salida->translatedFormat('d M Y, H:i') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-bold tracking-wide text-tinta-400 uppercase">Compromiso</dt>
                                    <dd class="mt-0.5 text-tinta-700">{{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}</dd>
                                </div>
                                <div>
                                    <dt class="font-bold tracking-wide text-tinta-400 uppercase">Devolución</dt>
                                    <dd class="mt-0.5 text-tinta-700">
                                        {{ $prestamo->fecha_devolucion?->translatedFormat('d M Y, H:i') ?? '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="font-bold tracking-wide text-tinta-400 uppercase">Autorizó</dt>
                                    <dd class="mt-0.5 text-tinta-700">{{ $prestamo->autorizadoPor->nombre }}</dd>
                                </div>
                            </dl>
                        </li>
                    @endforeach
                </ul>

                @if ($historial->hasPages())
                    <div class="border-t border-tinta-100 px-5 py-4">{{ $historial->links() }}</div>
                @endif
            @endif
        </x-tarjeta>
    </div>

</x-layouts.app>
