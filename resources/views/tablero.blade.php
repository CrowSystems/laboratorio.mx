{{--
    Tablero del laboratorio.

    Toda la información llega lista desde App\Services\ResumenDelTablero a
    través de TableroController. Esta vista no hace una sola consulta: solo
    acomoda y pinta. Esa es la razón de que se lea de corrido.
--}}
<x-layouts.app
    titulo="Tablero"
    :subtitulo="'Estado del laboratorio al '.now()->translatedFormat('d \d\e F \d\e Y, H:i').' h'">

    <x-slot:acciones>
        <x-boton variante="secundario" :href="route('vencidos.index')" icono="alerta">
            Ver vencidos
        </x-boton>
        <x-boton variante="acento" :href="route('prestamos.create')" icono="mas">
            Registrar préstamo
        </x-boton>
    </x-slot:acciones>

    {{-- ---------------------------------------------------------------- --}}
    {{-- Fila de indicadores                                              --}}
    {{-- ---------------------------------------------------------------- --}}
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-6 xl:gap-4">
        <x-indicador
            etiqueta="Equipo total"
            :valor="$conteos['total']"
            icono="equipo"
            tono="neutro"
            nota="En el catálogo"
            :href="route('equipos.index')" />

        <x-indicador
            etiqueta="Disponible"
            :valor="$conteos['disponible']"
            icono="cheque"
            tono="bien"
            nota="Listo para prestar"
            :href="route('equipos.index', ['estado' => 'disponible'])" />

        <x-indicador
            etiqueta="Prestado"
            :valor="$conteos['prestado']"
            icono="prestamo"
            tono="fuera"
            nota="Fuera del laboratorio"
            :href="route('equipos.index', ['estado' => 'prestado'])" />

        <x-indicador
            etiqueta="En reparación"
            :valor="$conteos['en_reparacion']"
            icono="reloj"
            tono="taller"
            nota="Regresó con daño"
            :href="route('equipos.index', ['estado' => 'en_reparacion'])" />

        <x-indicador
            etiqueta="Dado de baja"
            :valor="$conteos['baja']"
            icono="candado"
            tono="neutro"
            nota="Ya no se presta"
            :href="route('equipos.index', ['estado' => 'baja'])" />

        <x-indicador
            etiqueta="Vencidos"
            :valor="$vencidos"
            icono="alerta"
            :tono="$vencidos > 0 ? 'alerta' : 'bien'"
            :nota="$vencidos > 0 ? 'Requieren seguimiento' : 'Nada atrasado'"
            :href="route('vencidos.index')" />
    </div>

    {{-- ---------------------------------------------------------------- --}}
    {{-- Dos columnas: vencidos y ranking                                 --}}
    {{-- ---------------------------------------------------------------- --}}
    <div class="mt-5 grid items-start gap-5 xl:grid-cols-3">

        {{-- Atención inmediata --}}
        <x-tarjeta
            class="xl:col-span-2"
            titulo="Atención inmediata"
            :descripcion="$vencidos > 0
                ? 'Los '.$vencidos.' préstamos vencidos, del más atrasado al más reciente.'
                : 'No hay equipo atrasado en este momento.'"
            :relleno="false">

            <x-slot:acciones>
                @if ($vencidos > 0)
                    <x-boton variante="fantasma" :href="route('vencidos.index')">Ver los {{ $vencidos }}</x-boton>
                @endif
            </x-slot:acciones>

            @forelse ($vencidosRecientes as $prestamo)
                {{-- flex-wrap + un ancho mínimo en el bloque del nombre: en el
                     celular el botón baja a su propio renglón en vez de aplastar
                     el nombre del alumno hasta dejarlo en puntos suspensivos. --}}
                <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-b border-tinta-100 px-5 py-3.5 last:border-0">
                    <x-atraso :dias="$prestamo->dias_atraso" />

                    <div class="min-w-[11rem] flex-1">
                        <p class="truncate text-sm font-bold text-tinta-950">{{ $prestamo->alumno->nombre }}</p>
                        <p class="truncate text-xs text-tinta-500">
                            <span class="font-mono font-semibold">{{ $prestamo->equipo->numero_inventario }}</span>
                            · {{ $prestamo->equipo->nombreCompleto() }}
                        </p>
                    </div>

                    <x-boton variante="secundario" :href="route('devoluciones.create', $prestamo)" icono="regresar"
                             class="ml-auto px-3 py-2 text-xs">
                        Recibir
                    </x-boton>
                </div>
            @empty
                <x-vacio titulo="Todo en orden" icono="cheque">
                    Ningún alumno trae equipo atrasado. Cuando alguien pase su fecha
                    comprometida, aparecerá aquí automáticamente.
                </x-vacio>
            @endforelse
        </x-tarjeta>

        {{-- Equipos más solicitados --}}
        <x-tarjeta
            titulo="Los 5 más solicitados"
            descripcion="Préstamos acumulados desde que arrancó el sistema."
            :relleno="false">

            @forelse ($masSolicitados as $indice => $equipo)
                @php
                    // El ancho de la barra es relativo al primer lugar, que es
                    // el máximo. Se calcula aquí porque es presentación pura.
                    $maximo = max(1, $masSolicitados->first()->prestamos_count);
                    $porcentaje = (int) round($equipo->prestamos_count / $maximo * 100);
                @endphp

                <a href="{{ route('equipos.show', $equipo) }}"
                   class="block border-b border-tinta-100 px-5 py-3.5 transition last:border-0 hover:bg-tinta-50">
                    <div class="flex items-baseline gap-3">
                        <span class="w-5 shrink-0 text-sm font-extrabold text-tinta-300 tabular-nums">{{ $indice + 1 }}</span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-bold text-tinta-950">{{ $equipo->tipo }} {{ $equipo->marca }}</p>
                            <p class="truncate font-mono text-xs text-tinta-500">{{ $equipo->numero_inventario }}</p>
                        </div>
                        <span class="shrink-0 text-sm font-extrabold text-tinta-800 tabular-nums">{{ $equipo->prestamos_count }}</span>
                    </div>

                    <div class="mt-2 ml-8 h-1.5 overflow-hidden rounded-full bg-tinta-100">
                        <div class="h-full rounded-full bg-gradient-to-r from-tinta-400 to-tinta-700"
                             style="width: {{ $porcentaje }}%"></div>
                    </div>
                </a>
            @empty
                <x-vacio titulo="Sin préstamos todavía" icono="prestamo">
                    El ranking se llena solo en cuanto empieces a registrar salidas de equipo.
                </x-vacio>
            @endforelse
        </x-tarjeta>
    </div>

    {{-- ---------------------------------------------------------------- --}}
    {{-- Últimos movimientos                                              --}}
    {{-- ---------------------------------------------------------------- --}}
    <x-tarjeta
        class="mt-5"
        titulo="Últimos movimientos"
        descripcion="Las salidas más recientes, con el estado en el que está cada préstamo."
        :relleno="false">

        <x-slot:acciones>
            <x-boton variante="fantasma" :href="route('prestamos.index', ['filtro' => 'todos'])">Ver todos</x-boton>
        </x-slot:acciones>

        <div class="divide-y divide-tinta-100">
            @forelse ($movimientos as $prestamo)
                <a href="{{ route('prestamos.show', $prestamo) }}"
                   class="flex flex-wrap items-center gap-x-4 gap-y-2 px-5 py-3.5 transition hover:bg-tinta-50">
                    <div class="min-w-[14rem] flex-1">
                        <p class="truncate text-sm font-bold text-tinta-950">
                            {{ $prestamo->equipo->numero_inventario }} · {{ $prestamo->equipo->nombreCompleto() }}
                        </p>
                        <p class="truncate text-xs text-tinta-500">
                            {{ $prestamo->alumno->nombre }} ·
                            salió el {{ $prestamo->fecha_salida->translatedFormat('d M, H:i') }} ·
                            autorizó {{ $prestamo->autorizadoPor->nombre }}
                        </p>
                    </div>

                    <span class="ml-auto">
                        @if (! $prestamo->esta_abierto)
                            <x-insignia :clases="$prestamo->condicion_devolucion->clasesInsignia()">
                                Devuelto · {{ $prestamo->condicion_devolucion->etiqueta() }}
                            </x-insignia>
                        @elseif ($prestamo->esta_vencido)
                            <x-atraso :dias="$prestamo->dias_atraso" />
                        @else
                            <x-insignia clases="bg-sky-50 text-sky-700 ring-sky-600/20">
                                Fuera · entrega {{ $prestamo->fecha_compromiso->translatedFormat('d M') }}
                            </x-insignia>
                        @endif
                    </span>
                </a>
            @empty
                <x-vacio titulo="Todavía no hay movimientos" icono="prestamo">
                    En cuanto registres el primer préstamo lo verás aquí.
                </x-vacio>
            @endforelse
        </div>
    </x-tarjeta>

</x-layouts.app>
