<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\Alumno;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * REGLA DE NEGOCIO 2 — Nada nuevo mientras haya un adeudo vencido.
 *
 * Si el alumno trae equipo que debió regresar y no lo ha hecho, el mostrador
 * no le entrega nada más hasta que lo devuelva. Es la regla que convierte al
 * sistema en algo útil: sin ella, el listado de vencidos solo informa, no frena.
 *
 * La pregunta real la responde el modelo (Alumno::tieneAdeudoVencido()); esta
 * clase solo la conecta al validador y escribe el mensaje para el usuario.
 */
class AlumnoSinAdeudoVencido implements ValidationRule
{
    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $alumno = Alumno::find($value);

        // Si el alumno no existe, la regla `exists` ya lo reportó.
        if (! $alumno instanceof Alumno) {
            return;
        }

        if (! $alumno->tieneAdeudoVencido()) {
            return;
        }

        $pendientes = $alumno->prestamosVencidos()->with('equipo')->get();

        $inventarios = $pendientes
            ->map(fn ($prestamo): string => (string) $prestamo->equipo?->numero_inventario)
            ->filter()
            ->implode(', ');

        $fail(sprintf(
            '%s tiene %d préstamo(s) vencido(s) sin devolver (%s). No puede llevarse más equipo hasta regresarlo.',
            $alumno->nombre,
            $pendientes->count(),
            $inventarios,
        ));
    }
}
