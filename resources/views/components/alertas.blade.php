{{--
    Mensajes de una sola vez (flash messages).

    Los controladores los dejan en la sesión con ->with('exito', '...') al
    redirigir, y aquí se muestran una vez y desaparecen. Hay tres canales:
      exito   → verde, la operación salió bien
      aviso   → ámbar, salió bien pero con un matiz que hay que leer
      error   → rojo, no se pudo hacer

    También se muestra aquí el resumen de errores de validación, para que el
    usuario vea que algo falló aunque el campo con el problema esté más abajo.
--}}
@php
    $canales = [
        'exito' => ['clases' => 'bg-emerald-50 text-emerald-900 ring-emerald-600/20', 'icono' => 'cheque', 'color' => 'text-emerald-600'],
        'aviso' => ['clases' => 'bg-amber-50 text-amber-900 ring-amber-600/25', 'icono' => 'alerta', 'color' => 'text-amber-600'],
        'error' => ['clases' => 'bg-rose-50 text-rose-900 ring-rose-600/20', 'icono' => 'alerta', 'color' => 'text-rose-600'],
    ];
@endphp

@if (session()->hasAny(array_keys($canales)) || $errors->any())
    <div {{ $attributes->merge(['class' => 'space-y-3']) }}>
        @foreach ($canales as $canal => $estilo)
            @if (session()->has($canal))
                <div role="alert"
                     class="flex items-start gap-3 rounded-2xl p-4 text-sm font-medium ring-1 ring-inset {{ $estilo['clases'] }}">
                    <x-icono :nombre="$estilo['icono']" class="mt-px h-5 w-5 shrink-0 {{ $estilo['color'] }}" />
                    <p class="min-w-0">{{ session($canal) }}</p>
                </div>
            @endif
        @endforeach

        @if ($errors->any())
            <div role="alert"
                 class="rounded-2xl bg-rose-50 p-4 ring-1 ring-inset ring-rose-600/20">
                <div class="flex items-start gap-3">
                    <x-icono nombre="alerta" class="mt-px h-5 w-5 shrink-0 text-rose-600" />
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-rose-900">
                            {{ trans_choice('No se pudo guardar: hay :count dato por corregir.|No se pudo guardar: hay :count datos por corregir.', $errors->count()) }}
                        </p>
                        <ul class="mt-1.5 list-disc space-y-0.5 pl-5 text-sm text-rose-800">
                            @foreach ($errors->all() as $mensaje)
                                <li>{{ $mensaje }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endif
