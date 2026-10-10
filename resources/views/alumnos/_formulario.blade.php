{{--
    Campos del alumno, compartidos por el alta y la edición.

    Variable: $alumno (solo al editar).
--}}
@php
    $alumno ??= null;

    // Las carreras se ofrecen en una lista para que el dato quede limpio: si
    // cada quien la escribe a mano, el filtro por carrera deja de servir.
    $carreras = [
        'Licenciatura en Sistemas Computacionales' => 'Licenciatura en Sistemas Computacionales',
        'Licenciatura en Administración' => 'Licenciatura en Administración',
        'Licenciatura en Contaduría' => 'Licenciatura en Contaduría',
        'Licenciatura en Mercadotecnia' => 'Licenciatura en Mercadotecnia',
        'Licenciatura en Derecho' => 'Licenciatura en Derecho',
    ];
@endphp

<div class="grid gap-5 sm:grid-cols-2">
    <x-campo
        nombre="matricula"
        etiqueta="Matrícula"
        :valor="$alumno?->matricula"
        placeholder="SC202401"
        ayuda="Dos letras del plan y seis números."
        requerido />

    <x-campo
        nombre="semestre"
        etiqueta="Semestre"
        tipo="number"
        min="1"
        max="9"
        step="1"
        :valor="$alumno?->semestre"
        ayuda="Decide qué equipo restringido puede llevarse."
        requerido />

    <x-campo
        nombre="nombre"
        etiqueta="Nombre completo"
        :valor="$alumno?->nombre"
        placeholder="Diana Laura Moreno Vázquez"
        requerido
        class="sm:col-span-2" />

    <x-campo
        nombre="carrera"
        etiqueta="Carrera"
        tipo="select"
        :opciones="$carreras"
        :valor="$alumno?->carrera"
        requerido
        class="sm:col-span-2" />

    <x-campo
        nombre="correo"
        etiqueta="Correo institucional"
        tipo="email"
        :valor="$alumno?->correo"
        placeholder="dmoreno@cesba.edu.mx"
        requerido
        class="sm:col-span-2" />
</div>
