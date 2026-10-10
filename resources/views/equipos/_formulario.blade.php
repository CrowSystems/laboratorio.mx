{{--
    Campos del formulario de equipo, compartidos por el alta y la edición.

    Esta parcial existe para no tener los mismos ocho campos en dos archivos.
    El día que se agregue una columna al equipo, se agrega aquí una vez y
    aparece en las dos pantallas. El guion bajo del nombre es una convención
    para marcar que es una parcial, no una vista que se devuelva sola.

    Variables: $estados (opciones del select) y $equipo (solo al editar).
--}}
@php
    $equipo ??= null;
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-campo
        nombre="numero_inventario"
        etiqueta="Número de inventario"
        :valor="$equipo?->numero_inventario"
        placeholder="LAB-1234"
        ayuda="La etiqueta física del equipo. No puede repetirse."
        requerido
        class="sm:col-span-2" />

    <x-campo
        nombre="tipo"
        etiqueta="Tipo de equipo"
        :valor="$equipo?->tipo"
        placeholder="Laptop, Proyector, Multímetro…"
        requerido />

    <x-campo
        nombre="marca"
        etiqueta="Marca"
        :valor="$equipo?->marca"
        placeholder="Dell, Epson, Rigol…"
        requerido />

    <x-campo
        nombre="modelo"
        etiqueta="Modelo"
        :valor="$equipo?->modelo"
        placeholder="Latitude 5450"
        requerido />

    <x-campo
        nombre="estado"
        etiqueta="Estado"
        tipo="select"
        :opciones="$estados"
        :valor="$equipo?->estado->value ?? App\Enums\EstadoEquipo::Disponible->value"
        ayuda="Al registrar un préstamo el estado cambia solo."
        requerido />

    <x-campo
        nombre="semestre_minimo"
        etiqueta="Semestre mínimo"
        tipo="number"
        min="1"
        max="9"
        step="1"
        :valor="$equipo?->semestre_minimo ?? 1"
        ayuda="Deja 1 si cualquier alumno puede llevárselo. Con 6, por ejemplo, solo se presta de 6.º semestre en adelante."
        requerido
        class="sm:col-span-2" />

    <x-campo
        nombre="descripcion"
        etiqueta="Descripción"
        tipo="textarea"
        :valor="$equipo?->descripcion"
        placeholder="Accesorios que se entregan con el equipo, detalles a revisar al recibirlo…"
        ayuda="Lo que el auxiliar debe revisar al entregar y al recibir."
        class="sm:col-span-2" />
</div>
