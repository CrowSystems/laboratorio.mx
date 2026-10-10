<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\CondicionDevolucion;
use App\Enums\EstadoEquipo;
use App\Exceptions\ReglaDePrestamoViolada;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

/**
 * Las dos operaciones que mueven el laboratorio: prestar y recibir.
 *
 * Esta clase existe para que los controladores queden delgados. Un préstamo no
 * es "guardar una fila": son dos escrituras que tienen que ocurrir juntas
 * (crear el préstamo y cambiar el estado del equipo). Si una falla, ninguna
 * debe quedar. Eso es lo que hace DB::transaction().
 *
 * El controlador la recibe por inyección de dependencias en el constructor;
 * Laravel la construye solo porque no necesita argumentos.
 */
class ServicioDePrestamos
{
    /**
     * Entrega un equipo a un alumno y lo marca como prestado.
     *
     * @throws ReglaDePrestamoViolada si se rompe cualquiera de las tres reglas
     */
    public function prestar(
        Equipo $equipo,
        Alumno $alumno,
        Usuario $autoriza,
        CarbonInterface $fechaCompromiso,
        ?string $notas = null,
    ): Prestamo {
        return DB::transaction(function () use ($equipo, $alumno, $autoriza, $fechaCompromiso, $notas): Prestamo {
            // lockForUpdate() vuelve a leer el equipo bloqueando la fila hasta
            // el final de la transacción. Sin esto, dos auxiliares atendiendo
            // al mismo tiempo podrían prestar el mismo equipo dos veces.
            $equipo = Equipo::query()->lockForUpdate()->findOrFail($equipo->id);

            $this->verificarReglas($equipo, $alumno);

            $prestamo = $equipo->prestamos()->create([
                'alumno_id' => $alumno->id,
                'autorizado_por' => $autoriza->id,
                'fecha_salida' => now(),
                'fecha_compromiso' => $fechaCompromiso->toDateString(),
                'notas' => $notas,
            ]);

            // Al prestar, el equipo pasa a "prestado" automáticamente.
            $equipo->update(['estado' => EstadoEquipo::Prestado]);

            return $prestamo;
        });
    }

    /**
     * Recibe el equipo de vuelta y libera su estado.
     *
     * Si regresó con daño, el equipo NO vuelve a estar disponible: se va a
     * reparación. Esa decisión la toma el propio enum CondicionDevolucion.
     *
     * @throws ReglaDePrestamoViolada si el préstamo ya estaba cerrado
     */
    public function devolver(
        Prestamo $prestamo,
        CondicionDevolucion $condicion,
        ?string $notas = null,
    ): Prestamo {
        return DB::transaction(function () use ($prestamo, $condicion, $notas): Prestamo {
            $prestamo = Prestamo::query()
                ->with('equipo')
                ->lockForUpdate()
                ->findOrFail($prestamo->id);

            if (! $prestamo->esta_abierto) {
                throw ReglaDePrestamoViolada::prestamoYaDevuelto($prestamo);
            }

            $prestamo->update([
                'fecha_devolucion' => now(),
                'condicion_devolucion' => $condicion,
                // Las notas de la devolución se suman a las de la salida para
                // no perder lo que se anotó al prestar.
                'notas' => $this->unirNotas($prestamo->notas, $notas),
            ]);

            $prestamo->equipo->update(['estado' => $condicion->estadoResultante()]);

            return $prestamo->refresh();
        });
    }

    /**
     * Las tres reglas del laboratorio, en un solo lugar.
     *
     * @throws ReglaDePrestamoViolada
     */
    protected function verificarReglas(Equipo $equipo, Alumno $alumno): void
    {
        // Regla 3: solo equipo disponible.
        if (! $equipo->estado->permitePrestamo()) {
            throw ReglaDePrestamoViolada::equipoNoDisponible($equipo);
        }

        // Regla 1: equipo restringido por semestre.
        if ($alumno->semestre < $equipo->semestre_minimo) {
            throw ReglaDePrestamoViolada::semestreInsuficiente($alumno, $equipo);
        }

        // Regla 2: sin adeudos vencidos.
        if ($alumno->tieneAdeudoVencido()) {
            throw ReglaDePrestamoViolada::alumnoConAdeudo($alumno);
        }
    }

    protected function unirNotas(?string $alPrestar, ?string $alDevolver): ?string
    {
        $partes = [];

        if (filled($alPrestar)) {
            $partes[] = $alPrestar;
        }

        if (filled($alDevolver)) {
            $partes[] = 'Al devolver: '.$alDevolver;
        }

        return $partes === [] ? null : implode("\n", $partes);
    }
}
