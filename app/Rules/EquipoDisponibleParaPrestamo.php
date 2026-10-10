<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Equipo;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA DE NEGOCIO 3 — Solo se presta equipo disponible.
 *
 * Un equipo que ya está prestado, en reparación o dado de baja no puede salir.
 * Aunque el formulario solo liste equipo disponible, la regla se valida en el
 * servidor: el <select> de la vista es comodidad para el usuario, no seguridad.
 * Cualquiera puede enviar un id distinto con curl o con las herramientas del
 * navegador, y aquí es donde se le dice que no.
 */
class EquipoDisponibleParaPrestamo implements ValidationRule
{
    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $equipo = Equipo::find($value);

        // Si el equipo no existe, la regla `exists` ya lo reportó.
        if (! $equipo instanceof Equipo) {
            return;
        }

        if ($equipo->estado->permitePrestamo()) {
            return;
        }

        $fail(sprintf(
            'El equipo %s no se puede prestar porque su estado es "%s".',
            $equipo->numero_inventario,
            $equipo->estado->etiqueta(),
        ));
    }
}
