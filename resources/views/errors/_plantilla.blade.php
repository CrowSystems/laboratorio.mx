{{--
    Plantilla de las páginas de error.

    Laravel busca automáticamente una vista llamada resources/views/errors/403,
    404, 419, 500… cuando ocurre ese código HTTP. Si la encuentra, la usa en
    lugar de su pantalla genérica en inglés.

    Las cuatro páginas de errors/ heredan de aquí para no repetir el diseño.
    Variables: $codigo, $titulo, $mensaje.
--}}
<x-layouts.publico :titulo="$titulo">

    <div class="mx-auto max-w-xl rounded-3xl bg-white p-7 text-center shadow-2xl shadow-tinta-950/40 sm:p-10">
        <p class="font-mono text-6xl font-extrabold tracking-tighter text-tinta-200">{{ $codigo }}</p>

        <h2 class="mt-3 text-2xl font-extrabold tracking-tight text-tinta-950">{{ $titulo }}</h2>

        <p class="mx-auto mt-2 max-w-md text-[15px] leading-relaxed text-tinta-600">{{ $mensaje }}</p>

        <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
            @auth
                <x-boton variante="primario" :href="route('tablero')" icono="tablero">Ir al tablero</x-boton>
            @endauth

            <x-boton variante="secundario" :href="route('consulta.index')" icono="celular">
                Consultar una matrícula
            </x-boton>
        </div>
    </div>

</x-layouts.publico>
