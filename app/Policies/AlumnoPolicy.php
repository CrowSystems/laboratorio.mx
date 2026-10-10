<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Alumno;
use App\Models\Usuario;

/**
 * Quién puede administrar el padrón de alumnos.
 *
 * El auxiliar los consulta (necesita buscarlos para prestar), pero el alta,
 * la edición y la baja son del encargado.
 */
class AlumnoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Alumno $alumno): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return $usuario->esAdmin();
    }

    public function update(Usuario $usuario, Alumno $alumno): bool
    {
        return $usuario->esAdmin();
    }

    /**
     * No se borra a un alumno que tenga historial de préstamos: ese historial
     * es justo lo que el laboratorio necesita conservar.
     */
    public function delete(Usuario $usuario, Alumno $alumno): bool
    {
        return $usuario->esAdmin() && ! $alumno->prestamos()->exists();
    }
}
