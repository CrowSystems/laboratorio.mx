{{-- Detalle de un préstamo: el comprobante de la operación. --}}
<x-layouts.app
    :titulo="'Préstamo #'.$prestamo->id"
    :subtitulo="$prestamo->equipo->numero_inventario.' · '.$prestamo->alumno->nombre">

    <x-slot:acciones>
        <x-boton variante="secundario" :href="route('prestamos.index')" icono="flecha-izq">A préstamos</x-boton>

        @can('devolver', $prestamo)
            <x-boton variante="acento" :href="route('devoluciones.create', $prestamo)" icono="regresar">
                Recibir el equipo
            </x-boton>
        @endcan
    </x-slot:acciones>

    <div class="grid max-w-5xl items-start gap-5 lg:grid-cols-3">

        {{-- Situación actual --}}
        <x-tarjeta titulo="Situación">
            @if (! $prestamo->esta_abierto)
                <div class="rounded-2xl bg-emerald-50 p-4 ring-1 ring-inset ring-emerald-600/20">
                    <p class="text-sm font-bold text-emerald-900">Préstamo cerrado</p>
                    <p class="mt-1 text-sm text-emerald-800">
                        El equipo regresó el {{ $prestamo->fecha_devolucion->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }} h.
                    </p>
                    <div class="mt-3">
                        <x-insignia :clases="$prestamo->condicion_devolucion->clasesInsignia()">
                            {{ $prestamo->condicion_devolucion->etiqueta() }}
                        </x-insignia>
                    </div>

                    @if ($prestamo->dias_atraso_al_devolver > 0)
                        <p class="mt-3 text-xs font-semibold text-emerald-800">
                            Se entregó con {{ $prestamo->dias_atraso_al_devolver }} día(s) de retraso.
                        </p>
                    @endif
                </div>
            @elseif ($prestamo->esta_vencido)
                <div class="rounded-2xl bg-rose-50 p-4 ring-1 ring-inset ring-rose-600/20">
                    <p class="text-sm font-bold text-rose-900">Vencido</p>
                    <p class="mt-1 text-sm text-rose-800">
                        Debió entregarse el {{ $prestamo->fecha_compromiso->translatedFormat('d \d\e F') }}.
                    </p>
                    <div class="mt-3">
                        <x-atraso :dias="$prestamo->dias_atraso" />
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-sky-50 p-4 ring-1 ring-inset ring-sky-600/20">
                    <p class="text-sm font-bold text-sky-900">Fuera del laboratorio</p>
                    <p class="mt-1 text-sm text-sky-800">
                        Se entrega el {{ $prestamo->fecha_compromiso->translatedFormat('d \d\e F \d\e Y') }}.
                    </p>
                </div>
            @endif

            <dl class="mt-5 space-y-4 border-t border-tinta-100 pt-5 text-sm">
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Salida</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">
                        {{ $prestamo->fecha_salida->translatedFormat('d M Y, H:i') }} h
                    </dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Compromiso</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">
                        {{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}
                    </dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Devolución real</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">
                        @if ($prestamo->fecha_devolucion)
                            {{ $prestamo->fecha_devolucion->translatedFormat('d M Y, H:i') }} h
                        @else
                            Pendiente
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Autorizó la salida</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">{{ $prestamo->autorizadoPor->nombre }}</dd>
                    <dd class="text-xs text-tinta-500">{{ $prestamo->autorizadoPor->rol->etiqueta() }}</dd>
                </div>
            </dl>
        </x-tarjeta>

        {{-- Equipo y alumno --}}
        <div class="space-y-5 lg:col-span-2">
            <x-tarjeta titulo="Equipo">
                <x-slot:acciones>
                    <x-boton variante="fantasma" :href="route('equipos.show', $prestamo->equipo)">Ver ficha</x-boton>
                </x-slot:acciones>

                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="font-mono text-sm font-bold tracking-wider text-tinta-500">
                            {{ $prestamo->equipo->numero_inventario }}
                        </p>
                        <p class="mt-1 text-lg font-extrabold tracking-tight text-tinta-950">
                            {{ $prestamo->equipo->nombreCompleto() }}
                        </p>
                        @if (filled($prestamo->equipo->descripcion))
                            <p class="mt-1.5 max-w-xl text-sm leading-relaxed text-tinta-600">
                                {{ $prestamo->equipo->descripcion }}
                            </p>
                        @endif
                    </div>

                    <x-insignia :clases="$prestamo->equipo->estado->clasesInsignia()">
                        {{ $prestamo->equipo->estado->etiqueta() }}
                    </x-insignia>
                </div>
            </x-tarjeta>

            <x-tarjeta titulo="Alumno">
                <x-slot:acciones>
                    <x-boton variante="fantasma" :href="route('alumnos.show', $prestamo->alumno)">Ver ficha</x-boton>
                </x-slot:acciones>

                <div class="flex items-start gap-4">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-tinta-500 to-tinta-800 text-base font-extrabold text-white">
                        {{ Str::of($prestamo->alumno->nombre)->substr(0, 1)->upper() }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-mono text-xs font-bold tracking-wider text-tinta-500">{{ $prestamo->alumno->matricula }}</p>
                        <p class="mt-0.5 text-lg font-extrabold tracking-tight text-tinta-950">{{ $prestamo->alumno->nombre }}</p>
                        <p class="mt-0.5 text-sm text-tinta-500">
                            {{ $prestamo->alumno->carrera }} · {{ $prestamo->alumno->semestre }}.º semestre
                        </p>
                    </div>
                </div>
            </x-tarjeta>

            @if (filled($prestamo->notas))
                <x-tarjeta titulo="Notas">
                    {{-- whitespace-pre-line respeta los saltos de línea de la nota,
                         y {{ }} escapa el HTML para que nadie pueda inyectar
                         etiquetas en el texto que capturó el auxiliar. --}}
                    <p class="text-sm leading-relaxed whitespace-pre-line text-tinta-700">{{ $prestamo->notas }}</p>
                </x-tarjeta>
            @endif
        </div>
    </div>

</x-layouts.app>
