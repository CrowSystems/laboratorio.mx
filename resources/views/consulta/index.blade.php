{{--
    Pantalla de entrada del alumno. Es la URL que se pega en un cartel junto al
    mostrador del laboratorio: el alumno la abre en el celular, escribe su
    matrícula y ve qué debe. Sin cuenta, sin contraseña, sin instalar nada.
--}}
<x-layouts.publico titulo="Consulta tus préstamos">

    <div class="rounded-3xl bg-white p-6 shadow-2xl shadow-tinta-950/40 ring-1 ring-white/10 sm:p-9">

        <p class="inline-flex items-center gap-2 rounded-full bg-tinta-50 px-3 py-1 text-[11px] font-bold tracking-[0.14em] text-tinta-600 uppercase ring-1 ring-tinta-900/[0.06]">
            <x-icono nombre="celular" class="h-3.5 w-3.5" />
            Consulta pública
        </p>

        <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-tinta-950 sm:text-3xl">
            ¿Qué equipo tengo pendiente?
        </h2>

        <p class="mt-2 text-[15px] leading-relaxed text-tinta-600">
            Escribe tu matrícula y verás el equipo del laboratorio que todavía no has
            regresado y la fecha en la que lo debes entregar.
        </p>

        <form method="POST" action="{{ route('consulta.buscar') }}" class="mt-7 space-y-4">
            {{--
                @csrf agrega un campo oculto con un token único de la sesión.
                Sin él, Laravel rechaza el POST con un error 419. Es lo que evita
                que otro sitio web envíe formularios en tu nombre.
            --}}
            @csrf

            <x-campo
                nombre="matricula"
                etiqueta="Tu matrícula"
                placeholder="SC202401"
                ayuda="Dos letras y seis números, como aparece en tu credencial."
                requerido
                autofocus
                autocomplete="off"
                autocapitalize="characters"
                spellcheck="false"
                inputmode="text"
                maxlength="20"
                class="text-left"
            />

            <x-boton variante="acento" icono="buscar" class="w-full py-3 text-base">
                Consultar
            </x-boton>
        </form>

        {{-- Matrículas de ejemplo, para la demostración en clase. --}}
        <div class="mt-7 rounded-2xl bg-tinta-50 p-4 ring-1 ring-tinta-900/[0.06]">
            <p class="text-xs font-bold tracking-wide text-tinta-700 uppercase">Para probar en clase</p>
            <dl class="mt-2 space-y-1 text-sm text-tinta-600">
                <div class="flex gap-2">
                    <dt class="font-mono font-semibold text-tinta-900">SC202402</dt>
                    <dd>— trae un préstamo vencido</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="font-mono font-semibold text-tinta-900">SC202401</dt>
                    <dd>— tiene equipo prestado, al corriente</dd>
                </div>
                <div class="flex gap-2">
                    <dt class="font-mono font-semibold text-tinta-900">SC202407</dt>
                    <dd>— no debe nada</dd>
                </div>
            </dl>
        </div>
    </div>

</x-layouts.publico>
