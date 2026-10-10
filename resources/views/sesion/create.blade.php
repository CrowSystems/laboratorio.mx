{{-- Acceso para el encargado y el auxiliar del laboratorio. --}}
<x-layouts.publico titulo="Acceso del personal">

    <div class="mx-auto max-w-md">
        <div class="rounded-3xl bg-white p-6 shadow-2xl shadow-tinta-950/40 sm:p-8">

            <span class="grid h-12 w-12 place-items-center rounded-2xl bg-tinta-50 text-tinta-600 ring-1 ring-tinta-900/[0.06]">
                <x-icono nombre="candado" class="h-6 w-6" />
            </span>

            <h2 class="mt-4 text-2xl font-extrabold tracking-tight text-tinta-950">Acceso del personal</h2>
            <p class="mt-1.5 text-sm text-tinta-600">
                Solo para el encargado y el auxiliar del laboratorio. Si eres alumno,
                <a href="{{ route('consulta.index') }}" class="font-semibold text-tinta-700 underline decoration-tinta-300 underline-offset-2 hover:text-tinta-900">consulta tus préstamos aquí</a>.
            </p>

            <x-alertas class="mt-5" />

            <form method="POST" action="{{ route('sesion.store') }}" class="mt-6 space-y-4">
                @csrf

                <x-campo
                    nombre="correo"
                    etiqueta="Correo"
                    tipo="email"
                    placeholder="encargado@cesba.edu.mx"
                    requerido
                    autofocus
                    autocomplete="username"
                />

                <x-campo
                    nombre="password"
                    etiqueta="Contraseña"
                    tipo="password"
                    requerido
                    autocomplete="current-password"
                />

                <label class="flex cursor-pointer items-center gap-2.5 text-sm text-tinta-600">
                    <input type="checkbox" name="recordarme" value="1"
                           class="h-4 w-4 rounded border-tinta-300 text-tinta-700 focus:ring-tinta-500"
                           @checked(old('recordarme'))>
                    Mantener la sesión abierta en esta computadora
                </label>

                <x-boton variante="primario" class="w-full py-3 text-base">
                    Entrar
                </x-boton>
            </form>
        </div>

        {{-- Credenciales de la demostración. En un sistema real NUNCA se
             imprimen contraseñas en la pantalla de acceso: esto está aquí
             porque el proyecto es material de clase. --}}
        <div class="mt-5 rounded-2xl bg-white/10 p-4 ring-1 ring-white/15">
            <p class="text-[11px] font-bold tracking-[0.14em] text-amber-300 uppercase">Usuarios de prueba</p>
            <ul class="mt-2 space-y-1.5 text-sm text-tinta-200">
                <li>
                    <span class="font-mono text-white">encargado@cesba.edu.mx</span>
                    <span class="text-tinta-400"> · admin</span>
                </li>
                <li>
                    <span class="font-mono text-white">auxiliar@cesba.edu.mx</span>
                    <span class="text-tinta-400"> · auxiliar</span>
                </li>
                <li class="pt-1 text-tinta-300">
                    Contraseña de los dos: <span class="font-mono text-white">laboratorio123</span>
                </li>
            </ul>
        </div>
    </div>

</x-layouts.publico>
