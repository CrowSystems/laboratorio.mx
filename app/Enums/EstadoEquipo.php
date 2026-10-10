<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Estados posibles de un equipo del laboratorio.
 *
 * Es un "enum respaldado" (backed enum) de PHP 8: cada caso tiene un valor
 * de texto que es exactamente lo que se guarda en la columna `estado` de la
 * tabla `equipos`. El modelo Equipo lo declara en su arreglo de `casts`, así
 * que al leer un equipo recibimos este enum y no una cadena suelta.
 *
 * Ventaja para el alumno: si escribes EstadoEquipo::Disponibl (con un error de
 * dedo) el programa falla al instante, mientras que 'disponibl' pasaría
 * inadvertido hasta que alguien revise la base de datos.
 */
enum EstadoEquipo: string
{
    case Disponible = 'disponible';
    case Prestado = 'prestado';
    case EnReparacion = 'en_reparacion';
    case Baja = 'baja';

    /**
     * Texto que se muestra al usuario final.
     */
    public function etiqueta(): string
    {
        return match ($this) {
            self::Disponible => 'Disponible',
            self::Prestado => 'Prestado',
            self::EnReparacion => 'En reparación',
            self::Baja => 'Baja',
        };
    }

    /**
     * Clases de Tailwind para pintar la insignia del estado.
     *
     * Vive aquí, y no en la vista, para que el color de un estado se defina
     * una sola vez en todo el proyecto.
     */
    public function clasesInsignia(): string
    {
        return match ($this) {
            self::Disponible => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
            self::Prestado => 'bg-sky-50 text-sky-700 ring-sky-600/20',
            self::EnReparacion => 'bg-amber-50 text-amber-800 ring-amber-600/30',
            self::Baja => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        };
    }

    /**
     * ¿Se puede entregar en préstamo un equipo que está en este estado?
     */
    public function permitePrestamo(): bool
    {
        return $this === self::Disponible;
    }

    /**
     * Lista para los <select> de los formularios: valor => etiqueta.
     *
     * @return array<string, string>
     */
    public static function paraSelect(): array
    {
        $opciones = [];

        foreach (self::cases() as $estado) {
            $opciones[$estado->value] = $estado->etiqueta();
        }

        return $opciones;
    }
}
