<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Alumno;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Valida el alta y la edición de un alumno.
 */
class GuardarAlumnoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $alumno = $this->route('alumno');

        return $alumno instanceof Alumno
            ? $this->user()->can('update', $alumno)
            : $this->user()->can('create', Alumno::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        /** @var Alumno|null $alumno */
        $alumno = $this->route('alumno');

        return [
            'matricula' => [
                'required',
                'string',
                'max:20',
                // Formato del CESBA: dos letras y seis dígitos, p. ej. SC202401.
                'regex:/^[A-Z]{2}[0-9]{6}$/',
                Rule::unique('alumnos', 'matricula')->ignore($alumno),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'carrera' => ['required', 'string', 'max:120'],
            'semestre' => ['required', 'integer', 'min:1', 'max:9'],
            'correo' => [
                'required',
                'email:rfc',
                'max:160',
                Rule::unique('alumnos', 'correo')->ignore($alumno),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'matricula.regex' => 'La matrícula lleva dos letras y seis números, por ejemplo SC202401.',
            'matricula.unique' => 'Ya hay un alumno registrado con esa matrícula.',
            'correo.unique' => 'Ese correo ya está registrado para otro alumno.',
            'semestre.min' => 'El semestre va del 1 al 9.',
            'semestre.max' => 'El semestre va del 1 al 9.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'matricula' => 'matrícula',
            'nombre' => 'nombre completo',
            'carrera' => 'carrera',
            'semestre' => 'semestre',
            'correo' => 'correo institucional',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'matricula' => mb_strtoupper(trim((string) $this->input('matricula'))),
            'nombre' => trim((string) $this->input('nombre')),
            'correo' => mb_strtolower(trim((string) $this->input('correo'))),
        ]);
    }
}
