{{--
    Préstamos vencidos: el reporte que el laboratorio no tenía cuando todo se
    firmaba en papel.

    Los días de atraso NO se calculan aquí. Vienen del atributo
    $prestamo->dias_atraso del modelo, y la consulta usa el scope
    Prestamo::vencidos(). La vista solo acomoda.
--}}
<x-layouts.app
    titulo="Préstamos vencidos"
    :subtitulo="$vencidos->total() > 0
        ? $vencidos->total().' préstamos sin devolver, de '.$alumnosConAdeudo.' alumnos distintos'
        : 'No hay equipo atrasado en este momento'">

    <x-slot:acciones>
        <x-boton variante="secundario" :href="route('prestamos.index')">Todos los préstamos</x-boton>
    </x-slot:acciones>

    @if ($vencidos->total() > 0)
        {{-- Aviso de contexto: explica la consecuencia de estar en esta lista. --}}
        <div class="mb-5 flex items-start gap-3 rounded-2xl bg-rose-50 p-4 ring-1 ring-inset ring-rose-600/20">
            <x-icono nombre="alerta" class="mt-px h-5 w-5 shrink-0 text-rose-600" />
            <p class="text-sm text-rose-900">
                <strong class="font-bold">Mientras un alumno aparezca en esta lista, el sistema no le deja llevarse más equipo.</strong>
                Es la regla que se valida al registrar un préstamo, así que no hace falta recordarla a mano.
            </p>
        </div>
    @endif

    <x-tarjeta :relleno="false">
        @if ($vencidos->isEmpty())
            <x-vacio titulo="Nada atrasado" icono="cheque">
                Todo el equipo prestado está dentro de su fecha comprometida.
                Esta lista se llena sola cuando alguien pasa su plazo.
            </x-vacio>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[52rem] text-left text-sm">
                    <thead class="border-b border-tinta-200 bg-tinta-50/60 text-[11px] font-bold tracking-wider text-tinta-500 uppercase">
                        <tr>
                            <th scope="col" class="px-5 py-3">Atraso</th>
                            <th scope="col" class="px-5 py-3">Alumno</th>
                            <th scope="col" class="px-5 py-3">Equipo</th>
                            <th scope="col" class="px-5 py-3">Debió entregar</th>
                            <th scope="col" class="px-5 py-3">Autorizó</th>
                            <th scope="col" class="px-5 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-tinta-100">
                        @foreach ($vencidos as $prestamo)
                            <tr class="transition hover:bg-rose-50/50">
                                <td class="px-5 py-3.5 align-middle">
                                    <x-atraso :dias="$prestamo->dias_atraso" />
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('alumnos.show', $prestamo->alumno) }}"
                                       class="font-semibold text-tinta-950 hover:underline">
                                        {{ $prestamo->alumno->nombre }}
                                    </a>
                                    <p class="font-mono text-xs text-tinta-500">
                                        {{ $prestamo->alumno->matricula }} · {{ $prestamo->alumno->semestre }}.º sem.
                                    </p>
                                </td>

                                <td class="px-5 py-3.5 align-middle">
                                    <a href="{{ route('equipos.show', $prestamo->equipo) }}"
                                       class="font-mono text-sm font-bold text-tinta-800 hover:underline">
                                        {{ $prestamo->equipo->numero_inventario }}
                                    </a>
                                    <p class="text-xs text-tinta-500">{{ $prestamo->equipo->nombreCompleto() }}</p>
                                </td>

                                <td class="px-5 py-3.5 align-middle whitespace-nowrap">
                                    <span class="font-semibold text-rose-700">
                                        {{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="block text-xs text-tinta-400">
                                        salió el {{ $prestamo->fecha_salida->translatedFormat('d M') }}
                                    </span>
                                </td>

                                <td class="px-5 py-3.5 align-middle text-xs text-tinta-600">
                                    {{ $prestamo->autorizadoPor->nombre }}
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

            @if ($vencidos->hasPages())
                <div class="border-t border-tinta-100 px-5 py-4">{{ $vencidos->links() }}</div>
            @endif
        @endif
    </x-tarjeta>

</x-layouts.app>
