@props([
    'dias',
])

{{--
    Los días de atraso de un préstamo vencido.

    OJO: el número ya viene calculado desde el modelo, en el atributo
    $prestamo->dias_atraso (ver App\Models\Prestamo::diasAtraso()). La vista NO
    resta fechas: solo decide cómo pintar lo que recibe.

    El color sube de tono con el atraso para que, al proyectar el listado en el
    salón, el caso urgente se vea desde la última fila.
--}}
@php
    $clases = match (true) {
        $dias >= 10 => 'bg-rose-600 text-white ring-rose-700/30',
        $dias >= 4 => 'bg-rose-100 text-rose-800 ring-rose-600/25',
        default => 'bg-amber-100 text-amber-900 ring-amber-600/25',
    };
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-bold whitespace-nowrap ring-1 ring-inset tabular-nums {{ $clases }}">
    <x-icono nombre="reloj" class="h-3.5 w-3.5" />
    {{ trans_choice('{1} :dias día|[2,*] :dias días', $dias, ['dias' => $dias]) }}
</span>
