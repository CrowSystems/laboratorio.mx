<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Los dos roles con los que opera el laboratorio.
 *
 * - Admin (encargado del laboratorio): puede todo, incluyendo dar de alta
 *   y de baja equipo y alumnos.
 * - Auxiliar: atiende el mostrador. Presta y recibe equipo, pero no toca
 *   el catálogo.
 *
 * Quién puede hacer qué NO se decide aquí: se decide en las Policies
 * (app/Policies). Este enum solo nombra los roles.
 */
enum RolUsuario: string
{
    case Admin = 'admin';
    case Auxiliar = 'auxiliar';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Admin => 'Encargado del laboratorio',
            self::Auxiliar => 'Auxiliar de mostrador',
        };
    }

    public function clasesInsignia(): string
    {
        return match ($this) {
            self::Admin => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
            self::Auxiliar => 'bg-teal-50 text-teal-700 ring-teal-600/20',
        };
    }
}
