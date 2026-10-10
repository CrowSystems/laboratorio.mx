<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use App\Models\Prestamo;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

/**
 * Arma los números del tablero.
 *
 * Está aparte del controlador por una razón concreta: el tablero necesita seis
 * consultas distintas. Si vivieran en el controlador, el método index() tendría
 * sesenta líneas y nadie podría probar los conteos sin levantar una petición
 * HTTP completa.
 */
class ResumenDelTablero
{
    /**
     * Conteos de equipo por estado, más el total.
     *
     * Usa UNA sola consulta agrupada en lugar de cinco COUNT separados.
     *
     * @return array{total: int, disponible: int, prestado: int, en_reparacion: int, baja: int}
     */
    public function conteosDeEquipo(): array
    {
        /** @var Collection<string, int> $porEstado */
        $porEstado = Equipo::query()
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $conteos = ['total' => (int) $porEstado->sum()];

        foreach (EstadoEquipo::cases() as $estado) {
            $conteos[$estado->value] = (int) $porEstado->get($estado->value, 0);
        }

        return $conteos;
    }

    /**
     * Cuántos préstamos están vencidos hoy.
     */
    public function prestamosVencidos(): int
    {
        return Prestamo::query()->vencidos()->count();
    }

    /**
     * Cuántos préstamos hay abiertos en total (vencidos o no).
     */
    public function prestamosAbiertos(): int
    {
        return Prestamo::query()->abiertos()->count();
    }

    /**
     * Los cinco equipos con más préstamos históricos.
     *
     * withCount() agrega una columna `prestamos_count` sin traer los préstamos:
     * la base de datos hace el conteo, no PHP.
     *
     * @return EloquentCollection<int, Equipo>
     */
    public function equiposMasSolicitados(int $cuantos = 5): EloquentCollection
    {
        return Equipo::query()
            ->withCount('prestamos')
            // has() descarta el equipo que nunca ha salido. Se usa esto y no
            // having('prestamos_count', '>', 0) porque HAVING sin GROUP BY no
            // es válido en SQLite, y el proyecto debe correr igual en SQLite
            // y en MySQL.
            ->has('prestamos')
            ->orderByDesc('prestamos_count')
            ->orderBy('numero_inventario')
            ->limit($cuantos)
            ->get();
    }

    /**
     * Los préstamos vencidos más atrasados, para mostrarlos en el tablero.
     *
     * @return EloquentCollection<int, Prestamo>
     */
    public function vencidosMasAntiguos(int $cuantos = 5): EloquentCollection
    {
        return Prestamo::query()
            ->vencidos()
            ->conRelaciones()
            ->orderBy('fecha_compromiso')
            ->limit($cuantos)
            ->get();
    }

    /**
     * Movimientos recientes para la columna de actividad.
     *
     * @return EloquentCollection<int, Prestamo>
     */
    public function ultimosMovimientos(int $cuantos = 6): EloquentCollection
    {
        return Prestamo::query()
            ->conRelaciones()
            ->latest('fecha_salida')
            ->limit($cuantos)
            ->get();
    }
}
