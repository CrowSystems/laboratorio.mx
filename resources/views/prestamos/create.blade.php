{{--
    Formulario de salida de equipo.

    Los dos <select> solo ofrecen opciones válidas (equipo disponible, alumnos
    del padrón). Eso es comodidad para el auxiliar. Las tres reglas de negocio
    se validan en el servidor, en RegistrarPrestamoRequest: si alguien manipula
    el formulario y manda otro id, el sistema lo rechaza igual.
--}}
<x-layouts.app
    titulo="Registrar préstamo"
    subtitulo="Entrega de equipo a un alumno. El equipo pasa a «prestado» automáticamente.">

    <div class="grid max-w-5xl items-start gap-5 lg:grid-cols-3">

        <div class="lg:col-span-2">
            <x-tarjeta>
                <form method="POST" action="{{ route('prestamos.store') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-5">
                        <x-campo
                            nombre="alumno_id"
                            etiqueta="Alumno que recibe"
                            tipo="select"
                            :opciones="$alumnos->mapWithKeys(fn ($alumno) => [
                                $alumno->id => $alumno->matricula.' — '.$alumno->nombre.' ('.$alumno->semestre.'.º sem.)',
                            ])"
                            placeholder="— Busca por matrícula o nombre —"
                            requerido />

                        <x-campo
                            nombre="equipo_id"
                            etiqueta="Equipo que sale"
                            tipo="select"
                            :opciones="$equipos->mapWithKeys(fn ($equipo) => [
                                $equipo->id => $equipo->numero_inventario.' — '.$equipo->nombreCompleto()
                                    .($equipo->estaRestringido() ? ' · desde '.$equipo->semestre_minimo.'.º sem.' : ''),
                            ])"
                            placeholder="— Solo aparece el equipo disponible —"
                            :ayuda="$equipos->isEmpty()
                                ? 'No hay equipo disponible en este momento.'
                                : $equipos->count().' equipos disponibles para prestar.'"
                            requerido />

                        <x-campo
                            nombre="fecha_compromiso"
                            etiqueta="Fecha comprometida de devolución"
                            tipo="date"
                            :valor="$fechaSugerida"
                            :min="now()->toDateString()"
                            :max="now()->addMonth()->toDateString()"
                            ayuda="Máximo un mes. Pasada esta fecha el préstamo aparece como vencido."
                            requerido
                            class="sm:max-w-xs" />

                        <x-campo
                            nombre="notas"
                            etiqueta="Notas de la entrega"
                            tipo="textarea"
                            placeholder="Accesorios que se entregan, estado visible del equipo, para qué materia…"
                            ayuda="Opcional, pero es lo que salva al laboratorio cuando el equipo regresa incompleto." />
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-tinta-100 pt-5">
                        <x-boton variante="secundario" :href="route('prestamos.index')">Cancelar</x-boton>
                        <x-boton variante="acento" icono="prestamo" :disabled="$equipos->isEmpty()">
                            Entregar equipo
                        </x-boton>
                    </div>
                </form>
            </x-tarjeta>
        </div>

        {{-- Recordatorio de las reglas, a la vista del auxiliar. --}}
        <x-tarjeta titulo="Antes de entregar" descripcion="Las tres reglas que el sistema va a revisar.">
            <ol class="space-y-4 text-sm">
                <li class="flex gap-3">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-tinta-100 text-xs font-bold text-tinta-700">1</span>
                    <span>
                        <strong class="block text-tinta-900">Semestre suficiente</strong>
                        <span class="text-tinta-600">Hay equipo reservado para semestres avanzados. Si el alumno no alcanza, el sistema no deja entregarlo.</span>
                    </span>
                </li>
                <li class="flex gap-3">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-tinta-100 text-xs font-bold text-tinta-700">2</span>
                    <span>
                        <strong class="block text-tinta-900">Sin adeudos vencidos</strong>
                        <span class="text-tinta-600">Si el alumno trae algo atrasado, primero lo regresa.</span>
                    </span>
                </li>
                <li class="flex gap-3">
                    <span class="grid h-6 w-6 shrink-0 place-items-center rounded-lg bg-tinta-100 text-xs font-bold text-tinta-700">3</span>
                    <span>
                        <strong class="block text-tinta-900">Equipo disponible</strong>
                        <span class="text-tinta-600">No se presta lo que está prestado, en reparación o dado de baja.</span>
                    </span>
                </li>
            </ol>

            <p class="mt-5 border-t border-tinta-100 pt-4 text-xs text-tinta-500">
                Autoriza la entrega <strong class="font-semibold text-tinta-700">{{ auth()->user()->nombre }}</strong>,
                y queda registrado en el préstamo.
            </p>
        </x-tarjeta>
    </div>

</x-layouts.app>
