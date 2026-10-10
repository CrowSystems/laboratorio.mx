@props([
    'nombre',
    'etiqueta',
    'tipo' => 'text',
    'valor' => null,
    'opciones' => null,
    'ayuda' => null,
    'requerido' => false,
    'placeholder' => null,
])

{{--
    Un campo de formulario completo: etiqueta, control, texto de ayuda y error.

    Es el componente que más trabajo ahorra en todo el proyecto. Sin él, cada
    campo de cada formulario tendría que repetir:
      - la etiqueta con su `for` apuntando al `id` correcto,
      - old('campo') para no perder lo que el usuario escribió si falla la
        validación,
      - @error para pintar el mensaje y el borde rojo,
      - aria-invalid y aria-describedby para los lectores de pantalla.

    Según `tipo` dibuja un <select>, un <textarea> o un <input>.

    Uso:
      <x-campo nombre="marca" etiqueta="Marca" requerido />
      <x-campo nombre="estado" etiqueta="Estado" tipo="select" :opciones="$estados" />
      <x-campo nombre="notas" etiqueta="Notas" tipo="textarea" />
--}}
@php
    // old() devuelve lo que el usuario había escrito si la validación falló.
    // Si no hay nada, se usa el valor que pasó la vista (p. ej. al editar).
    $actual = old($nombre, $valor);
    $hayError = $errors->has($nombre);
    $idAyuda = $hayError ? $nombre.'-error' : ($ayuda ? $nombre.'-ayuda' : null);
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    <label for="{{ $nombre }}" class="block text-sm font-semibold text-tinta-800">
        {{ $etiqueta }}
        @if ($requerido)
            <span class="text-rose-500" title="Campo obligatorio" aria-hidden="true">*</span>
            <span class="sr-only">(obligatorio)</span>
        @endif
    </label>

    @if ($tipo === 'select')
        <select id="{{ $nombre }}" name="{{ $nombre }}"
                @if ($requerido) required @endif
                @if ($hayError) aria-invalid="true" @endif
                @if ($idAyuda) aria-describedby="{{ $idAyuda }}" @endif
                {{-- Se conserva la flecha nativa del navegador: es la señal que
                     el usuario ya conoce de que el campo es una lista. --}}
                {{ $attributes->except('class')->merge(['class' => 'campo-base pr-9'.($hayError ? ' campo-error' : '')]) }}>
            <option value="">{{ $placeholder ?? '— Selecciona una opción —' }}</option>
            @foreach ($opciones ?? [] as $clave => $texto)
                <option value="{{ $clave }}" @selected((string) $actual === (string) $clave)>{{ $texto }}</option>
            @endforeach
        </select>

    @elseif ($tipo === 'textarea')
        <textarea id="{{ $nombre }}" name="{{ $nombre }}" rows="3"
                  placeholder="{{ $placeholder }}"
                  @if ($requerido) required @endif
                  @if ($hayError) aria-invalid="true" @endif
                  @if ($idAyuda) aria-describedby="{{ $idAyuda }}" @endif
                  {{ $attributes->except('class')->merge(['class' => 'campo-base'.($hayError ? ' campo-error' : '')]) }}>{{ $actual }}</textarea>

    @else
        <input id="{{ $nombre }}" name="{{ $nombre }}" type="{{ $tipo }}"
               value="{{ $actual }}"
               placeholder="{{ $placeholder }}"
               @if ($requerido) required @endif
               @if ($hayError) aria-invalid="true" @endif
               @if ($idAyuda) aria-describedby="{{ $idAyuda }}" @endif
               {{ $attributes->except('class')->merge(['class' => 'campo-base'.($hayError ? ' campo-error' : '')]) }}>
    @endif

    @error($nombre)
        <p id="{{ $nombre }}-error" class="flex items-start gap-1.5 text-sm font-medium text-rose-600">
            <x-icono nombre="alerta" class="mt-0.5 h-4 w-4 shrink-0" />
            <span>{{ $message }}</span>
        </p>
    @else
        @if ($ayuda)
            <p id="{{ $nombre }}-ayuda" class="text-xs text-tinta-500">{{ $ayuda }}</p>
        @endif
    @enderror
</div>
