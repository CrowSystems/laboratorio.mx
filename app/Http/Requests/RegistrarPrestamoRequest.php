<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Prestamo;
use App\Rules\AlumnoSinAdeudoVencido;
use App\Rules\EquipoDisponibleParaPrestamo;
use App\Rules\SemestreSuficienteParaEquipo;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Valida la salida de un equipo.
 *
 * Aquí se aplican las TRES reglas de negocio del laboratorio, cada una en su
 * propia clase de app/Rules. El orden importa: primero `exists` comprueba que
 * los registros existan, y solo entonces las reglas de negocio los consultan.
 */
class RegistrarPrestamoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tanto el encargado como el auxiliar pueden prestar equipo;
        // la decisión está en PrestamoPolicy.
        return $this->user()->can('create', Prestamo::class);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'alumno_id' => [
                'required',
                'integer',
                'exists:alumnos,id',
                // REGLA 2: nada nuevo si trae un adeudo vencido.
                new AlumnoSinAdeudoVencido,
            ],
            'equipo_id' => [
                'required',
                'integer',
                'exists:equipos,id',
                // REGLA 3: el equipo tiene que estar disponible.
                new EquipoDisponibleParaPrestamo,
                // REGLA 1: el alumno debe alcanzar el semestre mínimo del equipo.
                new SemestreSuficienteParaEquipo,
            ],
            'fecha_compromiso' => [
                'required',
                'date',
                // No tiene sentido comprometer una devolución para ayer, ni
                // prestar algo por más de un mes.
                'after_or_equal:today',
                'before_or_equal:'.now()->addMonth()->toDateString(),
            ],
            'notas' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alumno_id.required' => 'Selecciona al alumno que recibe el equipo.',
            'alumno_id.exists' => 'Ese alumno no está registrado en el sistema.',
            'equipo_id.required' => 'Selecciona el equipo que va a salir.',
            'equipo_id.exists' => 'Ese equipo no existe en el catálogo.',
            'fecha_compromiso.required' => 'Indica para cuándo se compromete a regresarlo.',
            'fecha_compromiso.after_or_equal' => 'La fecha de devolución no puede ser anterior a hoy.',
            'fecha_compromiso.before_or_equal' => 'El préstamo no puede pasar de un mes.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'alumno_id' => 'alumno',
            'equipo_id' => 'equipo',
            'fecha_compromiso' => 'fecha comprometida de devolución',
            'notas' => 'notas',
        ];
    }
}
