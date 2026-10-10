{{-- Ficha de un alumno con todo su historial. --}}
<x-layouts.app
    :titulo="$alumno->nombre"
    :subtitulo="$alumno->matricula.' · '.$alumno->carrera.' · '.$alumno->semestre.'.º semestre'">

    <x-slot:acciones>
        <x-boton variante="secundario" :href="route('alumnos.index')" icono="flecha-izq">Al padrón</x-boton>

        @can('update', $alumno)
            <x-boton variante="primario" :href="route('alumnos.edit', $alumno)">Editar datos</x-boton>
        @endcan
    </x-slot:acciones>

    <div class="grid items-start gap-5 lg:grid-cols-3">

        <x-tarjeta titulo="Datos del alumno">
            <dl class="space-y-4 text-sm">
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Matrícula</dt>
                    <dd class="mt-0.5 font-mono font-bold text-tinta-900">{{ $alumno->matricula }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Carrera</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">{{ $alumno->carrera }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Semestre</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">{{ $alumno->semestre }}.º</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Correo</dt>
                    <dd class="mt-0.5 break-all text-tinta-700">{{ $alumno->correo }}</dd>
                </div>
            </dl>

            {{--
                Aviso de adeudo. tieneAdeudoVencido() es el MISMO método que usa
                la regla de validación AlumnoSinAdeudoVencido, así que lo que se
                ve en pantalla y lo que el sistema permite nunca se contradicen.
            --}}
            @if ($alumno->tieneAdeudoVencido())
                <div class="mt-6 rounded-2xl bg-rose-50 p-4 ring-1 ring-inset ring-rose-600/20">
                    <p class="flex items-start gap-2 text-sm font-bold text-rose-900">
                        <x-icono nombre="alerta" class="mt-px h-4 w-4 shrink-0" />
                        Tiene adeudo vencido
                    </p>
                    <p class="mt-1 text-sm text-rose-800">
                        No se le puede prestar más equipo hasta que regrese lo que debe.
                    </p>
                </div>
            @else
                <div class="mt-6 border-t border-tinta-100 pt-5">
                    <x-boton variante="acento" :href="route('prestamos.create')" icono="prestamo" class="w-full">
                        Prestarle equipo
                    </x-boton>
                </div>
            @endif
        </x-tarjeta>

        <x-tarjeta
            class="lg:col-span-2"
            titulo="Historial de préstamos"
            :descripcion="$historial->total().' préstamos registrados'"
            :relleno="false">

            @if ($historial->isEmpty())
                <x-vacio titulo="Nunca ha recibido equipo" icono="prestamo">
                    Este alumno todavía no se ha llevado nada del laboratorio.
                </x-vacio>
            @else
                <ul class="divide-y divide-tinta-100">
                    @foreach ($historial as $prestamo)
                        <li class="px-5 py-4">
                            <div class="flex flex-wrap items-start justify-between gap-x-4 gap-y-2">
                                <div class="min-w-0">
                                    <a href="{{ route('prestamos.show', $prestamo) }}"
                                       class="text-sm font-bold text-tinta-950 hover:underline">
                                        {{ $prestamo->equipo->nombreCompleto() }}
                                    </a>
                                    <p class="font-mono text-xs text-tinta-500">{{ $prestamo->equipo->numero_inventario }}</p>
                                </div>

                                @if ($prestamo->esta_abierto)
                                    @if ($prestamo->esta_vencido)
                                        <x-atraso :dias="$prestamo->dias_atraso" />
                                    @else
                                        <x-insignia clases="bg-sky-50 text-sky-700 ring-sky-600/20">
                                            Entrega {{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}
                                        </x-insignia>
                                    @endif
                                @else
                                    <x-insignia :clases="$prestamo->condicion_devolucion->clasesInsignia()">
                                        {{ $prestamo->condicion_devolucion->etiqueta() }}
                                    </x-insignia>
                                @endif
                            </div>

                            <p class="mt-2 text-xs text-tinta-500">
                                Salió el {{ $prestamo->fecha_salida->translatedFormat('d M Y, H:i') }} ·
                                @if ($prestamo->fecha_devolucion)
                                    devuelto el {{ $prestamo->fecha_devolucion->translatedFormat('d M Y, H:i') }}
                                @else
                                    sin devolver
                                @endif
                                · autorizó {{ $prestamo->autorizadoPor->nombre }}
                            </p>
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
