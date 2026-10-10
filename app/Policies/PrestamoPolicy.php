<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Prestamo;
use App\Models\Usuario;

/**
 * Los préstamos son el trabajo diario del mostrador: los dos roles pueden
 * registrarlos y recibirlos. Lo que nadie puede es borrarlos, porque el
 * historial de quién se llevó qué es el motivo por el que existe el sistema.
 */
class PrestamoPolicy
{
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Prestamo $prestamo): bool
    {
        return true;
    }

    public function create(Usuario $usuario): bool
    {
        return true;
    }

    /**
     * Permiso propio de este dominio: recibir el equipo de vuelta.
     *
     * Se consulta con $usuario->can('devolver', $prestamo). Un préstamo ya
     * cerrado no se puede volver a devolver.
     */
    public function devolver(Usuario $usuario, Prestamo $prestamo): bool
    {
        return $prestamo->esta_abierto;
    }

    /**
     * Nadie borra préstamos: ni el encargado. El historial es intocable.
     */
    public function delete(Usuario $usuario, Prestamo $prestamo): bool
    {
        return false;
    }
}
