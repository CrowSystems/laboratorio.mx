<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Valida el alta y la edición de un equipo del catálogo.
 *
 * Un Form Request es una clase que solo se encarga de responder dos preguntas:
 *   - authorize(): ¿este usuario puede hacer esto?
 *   - rules(): ¿los datos que mandó son válidos?
 * Si alguna falla, Laravel regresa al formulario con los errores y el
 * controlador nunca llega a ejecutarse. Por eso los controladores quedan cortos.
 */
class GuardarEquipoRequest extends FormRequest
{
    /**
     * La autorización real está en EquipoPolicy. Aquí solo se delega a ella,
     * así la regla de "quién puede" vive en un único archivo.
     */
    public function authorize(): bool
    {
        $equipo = $this->route('equipo');

        return $equipo instanceof Equipo
            ? $this->user()->can('update', $equipo)
            : $this->user()->can('create', Equipo::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Equipo|null $equipo */
        $equipo = $this->route('equipo');

        return [
            'numero_inventario' => [
                'required',
                'string',
                'max:30',
                // La etiqueta de inventario es única. Al editar hay que ignorar
                // el propio registro, o el equipo choca consigo mismo.
                Rule::unique('equipos', 'numero_inventario')->ignore($equipo),
            ],
            'tipo' => ['required', 'string', 'max:60'],
            'marca' => ['required', 'string', 'max:60'],
            'modelo' => ['required', 'string', 'max:80'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'estado' => ['required', new Enum(EstadoEquipo::class)],
            // El semestre mínimo va de 1 (sin restricción) a 9.
            'semestre_minimo' => ['required', 'integer', 'min:1', 'max:9'],
        ];
    }

    /**
     * Mensajes propios, en español y en el lenguaje del laboratorio.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'numero_inventario.required' => 'Captura el número de inventario que trae pegado el equipo.',
            'numero_inventario.unique' => 'Ya existe otro equipo registrado con ese número de inventario.',
            'estado.enum' => 'Selecciona un estado válido de la lista.',
            'semestre_minimo.min' => 'El semestre mínimo más bajo es 1 (equipo sin restricción).',
            'semestre_minimo.max' => 'La carrera tiene 9 semestres como máximo.',
        ];
    }

    /**
     * Nombres legibles de los campos, para que los mensajes automáticos de
     * Laravel digan "El número de inventario es obligatorio" y no
     * "El numero_inventario es obligatorio".
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'numero_inventario' => 'número de inventario',
            'tipo' => 'tipo de equipo',
            'marca' => 'marca',
            'modelo' => 'modelo',
            'descripcion' => 'descripción',
            'estado' => 'estado',
            'semestre_minimo' => 'semestre mínimo',
        ];
    }

    /**
     * Limpia los datos antes de validarlos: quita espacios sobrantes y
     * convierte el número de inventario a mayúsculas, que es como se etiqueta
     * el equipo en el laboratorio.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'numero_inventario' => mb_strtoupper(trim((string) $this->input('numero_inventario'))),
            'tipo' => trim((string) $this->input('tipo')),
            'marca' => trim((string) $this->input('marca')),
            'modelo' => trim((string) $this->input('modelo')),
        ]);
    }
}
