<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\CondicionDevolucion;
use App\Models\Prestamo;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

/**
 * Valida la recepción del equipo.
 */
class RegistrarDevolucionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Prestamo $prestamo */
        $prestamo = $this->route('prestamo');

        return $this->user()->can('devolver', $prestamo);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'condicion_devolucion' => ['required', new Enum(CondicionDevolucion::class)],
            // Si el equipo regresó dañado, explicar el daño es obligatorio:
            // es la nota que el encargado va a leer para mandarlo a reparar.
            'notas' => [
                'nullable',
                'required_if:condicion_devolucion,'.CondicionDevolucion::ConDano->value,
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'condicion_devolucion.required' => 'Indica si el equipo regresó bien o con daño.',
            'condicion_devolucion.enum' => 'Selecciona una condición válida de la lista.',
            'notas.required_if' => 'Si el equipo regresó con daño, describe qué le pasó.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'condicion_devolucion' => 'condición de devolución',
            'notas' => 'observaciones',
        ];
    }

    /**
     * La condición ya validada, lista para usarse como enum.
     *
     * Este método evita que el controlador tenga que hacer el `from()` a mano.
     */
    public function condicion(): CondicionDevolucion
    {
        return CondicionDevolucion::from($this->string('condicion_devolucion')->toString());
    }
}
