<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Cómo regresó el equipo al laboratorio.
 *
 * El valor elegido decide el estado al que vuelve el equipo:
 * "bien" lo regresa a Disponible y "con_dano" lo manda a EnReparacion.
 * Esa decisión está en el método estadoResultante() para que la regla viva
 * en un solo lugar y las pruebas la puedan verificar directamente.
 */
enum CondicionDevolucion: string
{
    case Bien = 'bien';
    case ConDano = 'con_dano';

    public function etiqueta(): string
    {
        return match ($this) {
            self::Bien => 'Regresó bien',
            self::ConDano => 'Regresó con daño',
        };
    }

    public function clasesInsignia(): string
    {
        return match ($this) {
            self::Bien => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::ConDano => 'bg-rose-50 text-rose-700 ring-rose-600/20',
        };
    }

    /**
     * Estado en el que queda el equipo después de esta devolución.
     *
     * Regla de negocio: un equipo que regresa dañado NO vuelve al catálogo
     * como disponible, se va a reparación hasta que el encargado lo revise.
     */
    public function estadoResultante(): EstadoEquipo
    {
        return match ($this) {
            self::Bien => EstadoEquipo::Disponible,
            self::ConDano => EstadoEquipo::EnReparacion,
        };
    }

    /**
     * @return array<string, string>
     */
    public static function paraSelect(): array
    {
        $opciones = [];

        foreach (self::cases() as $condicion) {
            $opciones[$condicion->value] = $condicion->etiqueta();
        }

        return $opciones;
    }
}
