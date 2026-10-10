<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Alumno;
use App\Models\Equipo;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA DE NEGOCIO 1 — Equipo restringido por semestre.
 *
 * Hay equipo del laboratorio que solo se presta a alumnos de semestres
 * avanzados (un osciloscopio, por ejemplo). Cada equipo guarda su
 * `semestre_minimo`; si el alumno va en un semestre menor, no se le entrega.
 *
 * ¿Por qué una regla propia y no un `if` en el controlador?
 *   - Se puede reutilizar en cualquier formulario que preste equipo.
 *   - El mensaje de error llega al usuario por el camino normal de Laravel,
 *     junto a los demás errores de validación.
 *   - Se puede probar de forma aislada (ver tests/Unit/ReglasDePrestamoTest.php).
 *
 * Implementa DataAwareRule porque necesita ver DOS campos del formulario a la
 * vez: el equipo solicitado y el alumno que lo pide. setData() le entrega todo
 * lo que el usuario envió.
 */
class SemestreSuficienteParaEquipo implements DataAwareRule, ValidationRule
{
    /**
     * Todos los datos que se están validando.
     *
     * @var array<string, mixed>
     */
    protected array $datos = [];

    /**
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->datos = $data;

        return $this;
    }

    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $equipo = Equipo::find($value);
        $alumno = Alumno::find($this->datos['alumno_id'] ?? null);

        // Si falta alguno de los dos, otras reglas (`exists`) ya reportaron el
        // problema. Esta regla no vuelve a quejarse del mismo error.
        if (! $equipo instanceof Equipo || ! $alumno instanceof Alumno) {
            return;
        }

        if ($alumno->semestre < $equipo->semestre_minimo) {
            $fail(sprintf(
                'El equipo %s está reservado para alumnos de %d.º semestre o superior, y %s va en el %d.º.',
                $equipo->numero_inventario,
                $equipo->semestre_minimo,
                $alumno->nombre,
                $alumno->semestre,
            ));
        }
    }
}
