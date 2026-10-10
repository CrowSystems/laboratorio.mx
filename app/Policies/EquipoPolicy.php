<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use App\Models\Usuario;

/**
 * Quién puede hacer qué con el catálogo de equipo.
 *
 * Laravel encuentra esta clase automáticamente por el nombre: para el modelo
 * App\Models\Equipo busca App\Policies\EquipoPolicy. No hay que registrarla.
 *
 * Importante: ocultar un botón en la vista NO es autorización. El botón se
 * oculta con @can para que la pantalla no mienta, pero lo que de verdad frena
 * la operación es esta Policy, invocada desde el Form Request y el controlador.
 */
class EquipoPolicy
{
    /**
     * Ver el catálogo: los dos roles lo necesitan para poder prestar.
     */
    public function viewAny(Usuario $usuario): bool
    {
        return true;
    }

    public function view(Usuario $usuario, Equipo $equipo): bool
    {
        return true;
    }

    /**
     * Dar de alta equipo: solo el encargado del laboratorio.
     */
    public function create(Usuario $usuario): bool
    {
        return $usuario->esAdmin();
    }

    /**
     * Editar la ficha del equipo: solo el encargado.
     */
    public function update(Usuario $usuario, Equipo $equipo): bool
    {
        return $usuario->esAdmin();
    }

    /**
     * Dar de baja equipo: solo el encargado, y nunca si el equipo está fuera
     * del laboratorio. Borrar un equipo prestado dejaría un préstamo abierto
     * apuntando a la nada.
     */
    public function delete(Usuario $usuario, Equipo $equipo): bool
    {
        return $usuario->esAdmin() && $equipo->estado !== EstadoEquipo::Prestado;
    }
}
