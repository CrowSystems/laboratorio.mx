<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la matrícula que el alumno escribe en la consulta pública.
 *
 * Es una ruta sin login, pero igual pasa por un Form Request: TODA entrada del
 * usuario se valida, sin excepciones. Que no haya sesión no significa que haya
 * que confiar en lo que llega.
 */
class ConsultarAdeudosRequest extends FormRequest
{
    /**
     * Ruta pública: cualquiera puede consultar su propia matrícula.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'matricula' => ['required', 'string', 'max:20', 'regex:/^[A-Z]{2}[0-9]{6}$/'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'matricula.required' => 'Escribe tu matrícula para consultar.',
            'matricula.regex' => 'La matrícula lleva dos letras y seis números, por ejemplo SC202401.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'matricula' => 'matrícula',
        ];
    }

    /**
     * El alumno puede escribir "sc202401" en el celular; aquí se normaliza.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'matricula' => mb_strtoupper(trim((string) $this->input('matricula'))),
        ]);
    }

    public function matricula(): string
    {
        return $this->string('matricula')->toString();
    }
}
