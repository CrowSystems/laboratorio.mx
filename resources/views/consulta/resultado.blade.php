{{--
    Resultado de la consulta pública.

    Tres casos posibles, y los tres se atienden:
      1. La matrícula no existe → se dice con claridad y sin regañar.
      2. Existe y no debe nada → mensaje tranquilizador.
      3. Existe y trae equipo → lista de pendientes, los vencidos arriba y en rojo.

    Qué datos se muestran: nombre, carrera y los préstamos del propio alumno.
    NO se muestra su correo ni nada de otros alumnos, porque esta pantalla es
    pública y basta con adivinar una matrícula para verla.
--}}
<x-layouts.publico titulo="Tus préstamos">

    @php
        // El controlador ya ordenó la colección; aquí solo se separa en dos
        // grupos para pintarlos distinto. partition() no consulta la base de
        // datos: parte en dos una colección que ya está en memoria.
        [$vencidos, $alCorriente] = $pendientes->partition(fn ($prestamo) => $prestamo->esta_vencido);
    @endphp

    <div class="space-y-4">

        {{-- ---------------------------------------------------------------- --}}
        {{-- Caso 1: la matrícula no existe                                   --}}
        {{-- ---------------------------------------------------------------- --}}
        @if ($alumno === null)
            <div class="rounded-3xl bg-white p-6 text-center shadow-2xl shadow-tinta-950/40 sm:p-9">
                <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-amber-50 text-amber-600">
                    <x-icono nombre="buscar" class="h-7 w-7" />
                </span>
                <h2 class="mt-4 text-xl font-extrabold text-tinta-950">No encontramos la matrícula {{ $matricula }}</h2>
                <p class="mt-2 text-[15px] text-tinta-600">
                    Revisa que la hayas escrito completa. Si está bien y aun así no aparece,
                    pasa al mostrador del laboratorio para que te registren.
                </p>
                <x-boton variante="secundario" :href="route('consulta.index')" icono="flecha-izq" class="mt-6">
                    Intentar con otra matrícula
                </x-boton>
            </div>
        @else

            {{-- ------------------------------------------------------------ --}}
            {{-- Encabezado con los datos del alumno                          --}}
            {{-- ------------------------------------------------------------ --}}
            <div class="rounded-3xl bg-white p-6 shadow-2xl shadow-tinta-950/40 sm:p-7">
                <div class="flex items-start gap-4">
                    <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-tinta-500 to-tinta-800 text-lg font-extrabold text-white">
                        {{ Str::of($alumno->nombre)->substr(0, 1)->upper() }}
                    </span>
                    <div class="min-w-0">
                        <p class="font-mono text-xs font-bold tracking-widest text-tinta-500">{{ $alumno->matricula }}</p>
                        <h2 class="mt-0.5 text-xl font-extrabold tracking-tight text-tinta-950">{{ $alumno->nombre }}</h2>
                        <p class="mt-0.5 text-sm text-tinta-500">{{ $alumno->carrera }} · {{ $alumno->semestre }}.º semestre</p>
                    </div>
                </div>

                {{-- Resumen en una línea: lo primero que el alumno quiere saber. --}}
                <div class="mt-5 rounded-2xl p-4 text-sm font-semibold ring-1 ring-inset
                    {{ $vencidos->isNotEmpty()
                        ? 'bg-rose-50 text-rose-900 ring-rose-600/20'
                        : ($alCorriente->isNotEmpty()
                            ? 'bg-sky-50 text-sky-900 ring-sky-600/20'
                            : 'bg-emerald-50 text-emerald-900 ring-emerald-600/20') }}">
                    @if ($vencidos->isNotEmpty())
                        Tienes {{ trans_choice('{1} :count equipo vencido|[2,*] :count equipos vencidos', $vencidos->count()) }}.
                        Pasa al laboratorio a entregarlo: mientras no lo regreses no te podrán prestar nada más.
                    @elseif ($alCorriente->isNotEmpty())
                        Tienes {{ trans_choice('{1} :count equipo prestado|[2,*] :count equipos prestados', $alCorriente->count()) }}, todo dentro del plazo.
                    @else
                        No tienes nada pendiente. ¡Estás al corriente!
                    @endif
                </div>
            </div>

            {{-- ------------------------------------------------------------ --}}
            {{-- Caso 2: no debe nada                                         --}}
            {{-- ------------------------------------------------------------ --}}
            @if ($pendientes->isEmpty())
                <div class="rounded-3xl bg-white/95 p-6 text-center shadow-xl shadow-tinta-950/30 sm:p-8">
                    <span class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <x-icono nombre="cheque" class="h-7 w-7" />
                    </span>
                    <p class="mt-4 text-base font-bold text-tinta-900">Sin equipo pendiente</p>
                    <p class="mt-1 text-sm text-tinta-500">
                        Cuando te presten algo aparecerá aquí con su fecha de entrega.
                    </p>
                </div>
            @else

                {{-- -------------------------------------------------------- --}}
                {{-- Caso 3: lista de pendientes, los vencidos primero        --}}
                {{-- -------------------------------------------------------- --}}
                @foreach ([['titulo' => 'Entrega vencida', 'items' => $vencidos], ['titulo' => 'Dentro del plazo', 'items' => $alCorriente]] as $grupo)
                    @if ($grupo['items']->isNotEmpty())
                        <section class="rounded-3xl bg-white p-5 shadow-xl shadow-tinta-950/30 sm:p-6">
                            <h3 class="text-xs font-bold tracking-[0.12em] text-tinta-500 uppercase">
                                {{ $grupo['titulo'] }}
                            </h3>

                            <ul class="mt-3 space-y-3">
                                @foreach ($grupo['items'] as $prestamo)
                                    <li class="rounded-2xl p-4 ring-1 ring-inset
                                        {{ $prestamo->esta_vencido ? 'bg-rose-50/70 ring-rose-600/20' : 'bg-tinta-50 ring-tinta-900/[0.06]' }}">

                                        <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-2">
                                            <div class="min-w-0">
                                                <p class="font-mono text-xs font-bold tracking-wider text-tinta-500">
                                                    {{ $prestamo->equipo->numero_inventario }}
                                                </p>
                                                <p class="mt-0.5 text-[15px] font-bold leading-snug text-tinta-950">
                                                    {{ $prestamo->equipo->nombreCompleto() }}
                                                </p>
                                            </div>

                                            @if ($prestamo->esta_vencido)
                                                <x-atraso :dias="$prestamo->dias_atraso" />
                                            @endif
                                        </div>

                                        <dl class="mt-3 grid grid-cols-2 gap-3 border-t pt-3 text-sm
                                            {{ $prestamo->esta_vencido ? 'border-rose-600/10' : 'border-tinta-900/[0.06]' }}">
                                            <div>
                                                <dt class="text-[11px] font-bold tracking-wide text-tinta-400 uppercase">Lo recogiste</dt>
                                                <dd class="mt-0.5 font-semibold text-tinta-800">
                                                    {{ $prestamo->fecha_salida->translatedFormat('d M Y') }}
                                                </dd>
                                            </div>
                                            <div>
                                                <dt class="text-[11px] font-bold tracking-wide text-tinta-400 uppercase">Debes entregarlo</dt>
                                                <dd class="mt-0.5 font-semibold {{ $prestamo->esta_vencido ? 'text-rose-700' : 'text-tinta-800' }}">
                                                    {{ $prestamo->fecha_compromiso->translatedFormat('d M Y') }}
                                                </dd>
                                            </div>
                                        </dl>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                @endforeach
            @endif

            <div class="pt-1 text-center">
                <a href="{{ route('consulta.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-tinta-200 transition hover:bg-white/10 hover:text-white">
                    <x-icono nombre="flecha-izq" class="h-4 w-4" />
                    Consultar otra matrícula
                </a>
            </div>
        @endif
    </div>

</x-layouts.publico>
