{{--
    Formulario de devolución.

    La decisión importante de esta pantalla: si el equipo regresó con daño, NO
    vuelve al catálogo como disponible, se va a «en reparación». Esa regla la
    resuelve el enum CondicionDevolucion::estadoResultante(), y el texto de
    abajo se la explica al auxiliar antes de que elija.
--}}
<x-layouts.app
    titulo="Recibir equipo"
    :subtitulo="'Préstamo #'.$prestamo->id.' · '.$prestamo->equipo->numero_inventario.' de '.$prestamo->alumno->nombre">

    <div class="grid max-w-5xl items-start gap-5 lg:grid-cols-3">

        <div class="lg:col-span-2">
            <x-tarjeta>
                <form method="POST" action="{{ route('devoluciones.store', $prestamo) }}" class="space-y-6">
                    @csrf

                    <x-campo
                        nombre="condicion_devolucion"
                        etiqueta="¿Cómo regresó el equipo?"
                        tipo="select"
                        :opciones="$condiciones"
                        placeholder="— Revísalo antes de contestar —"
                        requerido />

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-emerald-50 p-4 ring-1 ring-inset ring-emerald-600/20">
                            <p class="flex items-center gap-2 text-sm font-bold text-emerald-900">
                                <x-icono nombre="cheque" class="h-4 w-4" />
                                Regresó bien
                            </p>
                            <p class="mt-1 text-sm text-emerald-800">
                                El equipo vuelve al catálogo como <strong>disponible</strong> y se puede prestar de inmediato.
                            </p>
                        </div>

                        <div class="rounded-2xl bg-amber-50 p-4 ring-1 ring-inset ring-amber-600/25">
                            <p class="flex items-center gap-2 text-sm font-bold text-amber-900">
                                <x-icono nombre="alerta" class="h-4 w-4" />
                                Regresó con daño
                            </p>
                            <p class="mt-1 text-sm text-amber-800">
                                El equipo pasa a <strong>en reparación</strong> y deja de prestarse hasta que el
                                encargado lo revise. En este caso la nota es obligatoria.
                            </p>
                        </div>
                    </div>

                    <x-campo
                        nombre="notas"
                        etiqueta="Observaciones"
                        tipo="textarea"
                        rows="4"
                        placeholder="Qué le pasó, qué accesorio falta, qué hay que revisar…"
                        ayuda="Obligatoria si el equipo regresó con daño." />

                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-tinta-100 pt-5">
                        <x-boton variante="secundario" :href="route('prestamos.show', $prestamo)">Cancelar</x-boton>
                        <x-boton variante="primario" icono="regresar">Registrar devolución</x-boton>
                    </div>
                </form>
            </x-tarjeta>
        </div>

        {{-- Resumen de lo que se está recibiendo. --}}
        <x-tarjeta titulo="Lo que estás recibiendo">
            <p class="font-mono text-xs font-bold tracking-wider text-tinta-500">
                {{ $prestamo->equipo->numero_inventario }}
            </p>
            <p class="mt-1 text-base font-extrabold tracking-tight text-tinta-950">
                {{ $prestamo->equipo->nombreCompleto() }}
            </p>

            @if (filled($prestamo->equipo->descripcion))
                <p class="mt-2 rounded-xl bg-tinta-50 p-3 text-xs leading-relaxed text-tinta-600">
                    <strong class="block text-tinta-800">Revisa que venga completo:</strong>
                    {{ $prestamo->equipo->descripcion }}
                </p>
            @endif

            <dl class="mt-5 space-y-3.5 border-t border-tinta-100 pt-5 text-sm">
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Lo tenía</dt>
                    <dd class="mt-0.5 font-semibold text-tinta-900">{{ $prestamo->alumno->nombre }}</dd>
                    <dd class="font-mono text-xs text-tinta-500">{{ $prestamo->alumno->matricula }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Salió</dt>
                    <dd class="mt-0.5 text-tinta-700">{{ $prestamo->fecha_salida->translatedFormat('d M Y, H:i') }} h</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Debía entregarlo</dt>
                    <dd class="mt-0.5 text-tinta-700">{{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}</dd>
                </div>
            </dl>

            @if ($prestamo->esta_vencido)
                <div class="mt-4 rounded-2xl bg-rose-50 p-4 ring-1 ring-inset ring-rose-600/20">
                    <p class="text-sm font-bold text-rose-900">Entrega atrasada</p>
                    <div class="mt-2"><x-atraso :dias="$prestamo->dias_atraso" /></div>
                </div>
            @endif

            @if (filled($prestamo->notas))
                <div class="mt-4 border-t border-tinta-100 pt-4">
                    <p class="text-[11px] font-bold tracking-wider text-tinta-400 uppercase">Nota de la entrega</p>
                    <p class="mt-1 text-sm leading-relaxed whitespace-pre-line text-tinta-700">{{ $prestamo->notas }}</p>
                </div>
            @endif
        </x-tarjeta>
    </div>

</x-layouts.app>
