<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use RuntimeException;

/**
 * Se lanza cuando alguien intenta una operación que las reglas del laboratorio
 * no permiten y que la validación del formulario no alcanzó a detener.
 *
 * Es la segunda línea de defensa. Las reglas ya se validan en el Form Request
 * (ahí producen un mensaje bonito junto al campo), pero el servicio las vuelve
 * a verificar dentro de la transacción. Así, si mañana alguien llama a
 * ServicioDePrestamos desde un comando de consola o desde una API, las reglas
 * se siguen cumpliendo aunque no haya pasado por el formulario.
 */
class ReglaDePrestamoViolada extends RuntimeException
{
    public static function equipoNoDisponible(Equipo $equipo): self
    {
        return new self(sprintf(
            'El equipo %s no está disponible (estado actual: %s).',
            $equipo->numero_inventario,
            $equipo->estado->etiqueta(),
        ));
    }

    public static function alumnoConAdeudo(Alumno $alumno): self
    {
        return new self(sprintf(
            '%s tiene préstamos vencidos sin devolver.',
            $alumno->nombre,
        ));
    }

    public static function semestreInsuficiente(Alumno $alumno, Equipo $equipo): self
    {
        return new self(sprintf(
            'El equipo %s requiere %d.º semestre y %s va en el %d.º.',
            $equipo->numero_inventario,
            $equipo->semestre_minimo,
            $alumno->nombre,
            $alumno->semestre,
        ));
    }

    public static function prestamoYaDevuelto(Prestamo $prestamo): self
    {
        return new self(sprintf(
            'El préstamo #%d ya se había devuelto el %s.',
            $prestamo->id,
            $prestamo->fecha_devolucion?->format('d/m/Y H:i') ?? 'antes',
        ));
    }
}
