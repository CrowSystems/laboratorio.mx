<?php

declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| Mensajes de validación en español
|--------------------------------------------------------------------------
|
| Laravel busca aquí el mensaje de cada regla. El orden de prioridad es:
|   1. messages() del Form Request (el mensaje más específico).
|   2. 'custom' de este archivo.
|   3. El mensaje general de este archivo.
|
| Los :placeholders los rellena Laravel: :attribute es el nombre del campo
| (ver attributes(), al final), :min, :max, :other, etc. son los parámetros
| de la regla.
|
*/

return [

    'accepted' => 'Debes aceptar :attribute.',
    'active_url' => ':attribute no es una URL válida.',
    'after' => ':attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => ':attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => ':attribute solo debe contener letras.',
    'alpha_dash' => ':attribute solo debe contener letras, números, guiones y guiones bajos.',
    'alpha_num' => ':attribute solo debe contener letras y números.',
    'array' => ':attribute debe ser una lista.',
    'before' => ':attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => ':attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => ':attribute debe tener entre :min y :max elementos.',
        'file' => ':attribute debe pesar entre :min y :max kilobytes.',
        'numeric' => ':attribute debe estar entre :min y :max.',
        'string' => ':attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => ':attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña es incorrecta.',
    'date' => ':attribute no es una fecha válida.',
    'date_equals' => ':attribute debe ser una fecha igual a :date.',
    'date_format' => ':attribute no corresponde al formato :format.',
    'decimal' => ':attribute debe tener :decimal decimales.',
    'declined' => 'Debes rechazar :attribute.',
    'different' => ':attribute y :other deben ser diferentes.',
    'digits' => ':attribute debe tener :digits dígitos.',
    'digits_between' => ':attribute debe tener entre :min y :max dígitos.',
    'dimensions' => ':attribute tiene dimensiones de imagen no válidas.',
    'distinct' => ':attribute tiene un valor repetido.',
    'doesnt_end_with' => ':attribute no debe terminar con ninguno de estos valores: :values.',
    'doesnt_start_with' => ':attribute no debe comenzar con ninguno de estos valores: :values.',
    'email' => ':attribute debe ser una dirección de correo válida.',
    'ends_with' => ':attribute debe terminar con uno de estos valores: :values.',
    'enum' => 'El valor seleccionado en :attribute no es válido.',
    'exists' => 'El valor seleccionado en :attribute no existe.',
    'file' => ':attribute debe ser un archivo.',
    'filled' => ':attribute no puede quedar vacío.',
    'gt' => [
        'array' => ':attribute debe tener más de :value elementos.',
        'file' => ':attribute debe pesar más de :value kilobytes.',
        'numeric' => ':attribute debe ser mayor que :value.',
        'string' => ':attribute debe tener más de :value caracteres.',
    ],
    'gte' => [
        'array' => ':attribute debe tener :value elementos o más.',
        'file' => ':attribute debe pesar :value kilobytes o más.',
        'numeric' => ':attribute debe ser mayor o igual que :value.',
        'string' => ':attribute debe tener :value caracteres o más.',
    ],
    'image' => ':attribute debe ser una imagen.',
    'in' => 'El valor seleccionado en :attribute no es válido.',
    'in_array' => ':attribute no existe en :other.',
    'integer' => ':attribute debe ser un número entero.',
    'ip' => ':attribute debe ser una dirección IP válida.',
    'ipv4' => ':attribute debe ser una dirección IPv4 válida.',
    'ipv6' => ':attribute debe ser una dirección IPv6 válida.',
    'json' => ':attribute debe ser una cadena JSON válida.',
    'lowercase' => ':attribute debe estar en minúsculas.',
    'lt' => [
        'array' => ':attribute debe tener menos de :value elementos.',
        'file' => ':attribute debe pesar menos de :value kilobytes.',
        'numeric' => ':attribute debe ser menor que :value.',
        'string' => ':attribute debe tener menos de :value caracteres.',
    ],
    'lte' => [
        'array' => ':attribute no debe tener más de :value elementos.',
        'file' => ':attribute debe pesar :value kilobytes o menos.',
        'numeric' => ':attribute debe ser menor o igual que :value.',
        'string' => ':attribute debe tener :value caracteres o menos.',
    ],
    'max' => [
        'array' => ':attribute no debe tener más de :max elementos.',
        'file' => ':attribute no debe pesar más de :max kilobytes.',
        'numeric' => ':attribute no debe ser mayor que :max.',
        'string' => ':attribute no debe tener más de :max caracteres.',
    ],
    'mimes' => ':attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => ':attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => ':attribute debe tener al menos :min elementos.',
        'file' => ':attribute debe pesar al menos :min kilobytes.',
        'numeric' => ':attribute debe ser al menos :min.',
        'string' => ':attribute debe tener al menos :min caracteres.',
    ],
    'missing' => ':attribute no debe venir en la petición.',
    'multiple_of' => ':attribute debe ser múltiplo de :value.',
    'not_in' => 'El valor seleccionado en :attribute no es válido.',
    'not_regex' => 'El formato de :attribute no es válido.',
    'numeric' => ':attribute debe ser un número.',
    'present' => ':attribute debe venir en la petición.',
    'prohibited' => ':attribute está prohibido.',
    'prohibited_if' => ':attribute está prohibido cuando :other es :value.',
    'prohibited_unless' => ':attribute está prohibido salvo que :other esté en :values.',
    'regex' => 'El formato de :attribute no es válido.',
    'required' => ':attribute es obligatorio.',
    'required_array_keys' => ':attribute debe contener entradas para: :values.',
    'required_if' => ':attribute es obligatorio cuando :other es :value.',
    'required_if_accepted' => ':attribute es obligatorio cuando se acepta :other.',
    'required_unless' => ':attribute es obligatorio salvo que :other esté en :values.',
    'required_with' => ':attribute es obligatorio cuando :values está presente.',
    'required_with_all' => ':attribute es obligatorio cuando :values están presentes.',
    'required_without' => ':attribute es obligatorio cuando :values no está presente.',
    'required_without_all' => ':attribute es obligatorio cuando ninguno de :values está presente.',
    'same' => ':attribute y :other deben coincidir.',
    'size' => [
        'array' => ':attribute debe contener :size elementos.',
        'file' => ':attribute debe pesar :size kilobytes.',
        'numeric' => ':attribute debe ser :size.',
        'string' => ':attribute debe tener :size caracteres.',
    ],
    'starts_with' => ':attribute debe comenzar con uno de estos valores: :values.',
    'string' => ':attribute debe ser texto.',
    'timezone' => ':attribute debe ser una zona horaria válida.',
    'unique' => ':attribute ya está registrado.',
    'uploaded' => 'No se pudo subir :attribute.',
    'uppercase' => ':attribute debe estar en mayúsculas.',
    'url' => ':attribute debe ser una URL válida.',
    'uuid' => ':attribute debe ser un UUID válido.',

    /*
    |--------------------------------------------------------------------------
    | Mensajes por campo y regla
    |--------------------------------------------------------------------------
    |
    | Para mensajes que aplican a un campo concreto en toda la aplicación.
    | Si el mensaje es propio de un solo formulario, es mejor ponerlo en el
    | método messages() de su Form Request.
    |
    */

    'custom' => [
        'password' => [
            'min' => 'La contraseña debe tener al menos :min caracteres.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nombres legibles de los campos
    |--------------------------------------------------------------------------
    |
    | Reemplazan a :attribute en los mensajes. Así el aviso dice "El número de
    | inventario es obligatorio" en lugar de "El numero_inventario es
    | obligatorio". Los Form Requests pueden afinarlos con attributes().
    |
    */

    'attributes' => [
        'alumno_id' => 'alumno',
        'carrera' => 'carrera',
        'condicion_devolucion' => 'condición de devolución',
        'correo' => 'correo',
        'descripcion' => 'descripción',
        'equipo_id' => 'equipo',
        'estado' => 'estado',
        'fecha_compromiso' => 'fecha comprometida de devolución',
        'fecha_salida' => 'fecha de salida',
        'marca' => 'marca',
        'matricula' => 'matrícula',
        'modelo' => 'modelo',
        'nombre' => 'nombre',
        'notas' => 'notas',
        'numero_inventario' => 'número de inventario',
        'password' => 'contraseña',
        'rol' => 'rol',
        'semestre' => 'semestre',
        'semestre_minimo' => 'semestre mínimo',
        'tipo' => 'tipo',
    ],

];
