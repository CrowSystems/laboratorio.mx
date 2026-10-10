<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CondicionDevolucion;
use Carbon\CarbonImmutable;
use Database\Factories\PrestamoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * El registro de que un equipo salió del laboratorio.
 *
 * Ciclo de vida:
 *   1. Se crea con fecha_salida y fecha_compromiso. El equipo pasa a Prestado.
 *   2. Mientras fecha_devolucion sea NULL el préstamo está abierto.
 *   3. Al devolver se llenan fecha_devolucion y condicion_devolucion, y el
 *      equipo regresa a Disponible o se va a EnReparacion según la condición.
 *
 * @property-read int $id
 * @property int $equipo_id
 * @property int $alumno_id
 * @property int $autorizado_por
 * @property CarbonImmutable $fecha_salida
 * @property CarbonImmutable $fecha_compromiso
 * @property CarbonImmutable|null $fecha_devolucion
 * @property CondicionDevolucion|null $condicion_devolucion
 * @property string|null $notas
 * @property-read bool $esta_abierto
 * @property-read bool $esta_vencido
 * @property-read int $dias_atraso
 */
#[Fillable([
    'equipo_id',
    'alumno_id',
    'autorizado_por',
    'fecha_salida',
    'fecha_compromiso',
    'fecha_devolucion',
    'condicion_devolucion',
    'notas',
])]
class Prestamo extends Model
{
    /** @use HasFactory<PrestamoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_salida' => 'immutable_datetime',
            'fecha_compromiso' => 'immutable_date',
            'fecha_devolucion' => 'immutable_datetime',
            'condicion_devolucion' => CondicionDevolucion::class,
        ];
    }

    /**
     * @return BelongsTo<Equipo, $this>
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * @return BelongsTo<Alumno, $this>
     */
    public function alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class);
    }

    /**
     * Usuario del laboratorio que autorizó la salida.
     *
     * @return BelongsTo<Usuario, $this>
     */
    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'autorizado_por');
    }

    /**
     * Préstamos que todavía no se devuelven.
     *
     * @param  Builder<Prestamo>  $query
     */
    #[Scope]
    protected function abiertos(Builder $query): void
    {
        $query->whereNull('fecha_devolucion');
    }

    /**
     * Préstamos ya cerrados.
     *
     * @param  Builder<Prestamo>  $query
     */
    #[Scope]
    protected function devueltos(Builder $query): void
    {
        $query->whereNotNull('fecha_devolucion');
    }

    /**
     * Préstamos vencidos: abiertos y con la fecha comprometida en el pasado.
     *
     * Este scope es la definición única de "vencido" en todo el sistema.
     * El tablero, el listado de vencidos y la consulta pública lo reutilizan,
     * así que si algún día la regla cambia solo se edita aquí.
     *
     * @param  Builder<Prestamo>  $query
     */
    #[Scope]
    protected function vencidos(Builder $query): void
    {
        $query->abiertos()->whereDate('fecha_compromiso', '<', today());
    }

    /**
     * Carga de un jalón las tres relaciones que los listados siempre muestran.
     *
     * Evita el problema N+1: una consulta por relación en lugar de una
     * consulta por cada fila de la tabla.
     *
     * @param  Builder<Prestamo>  $query
     */
    #[Scope]
    protected function conRelaciones(Builder $query): void
    {
        $query->with(['equipo', 'alumno', 'autorizadoPor']);
    }

    /**
     * ¿Sigue fuera el equipo?
     *
     * @return Attribute<bool, never>
     */
    protected function estaAbierto(): Attribute
    {
        return Attribute::get(fn (): bool => $this->fecha_devolucion === null);
    }

    /**
     * ¿Está vencido en este momento?
     *
     * @return Attribute<bool, never>
     */
    protected function estaVencido(): Attribute
    {
        return Attribute::get(
            fn (): bool => $this->esta_abierto && $this->fecha_compromiso->isBefore(today())
        );
    }

    /**
     * Días completos de atraso.
     *
     * Devuelve 0 si el préstamo está al corriente o si ya se devolvió.
     * Para un préstamo devuelto tarde usa `dias_atraso_al_devolver`.
     *
     * @return Attribute<int, never>
     */
    protected function diasAtraso(): Attribute
    {
        return Attribute::get(function (): int {
            if (! $this->esta_vencido) {
                return 0;
            }

            return (int) $this->fecha_compromiso->diffInDays(today());
        });
    }

    /**
     * Días de atraso con los que finalmente se entregó el equipo.
     *
     * @return Attribute<int, never>
     */
    protected function diasAtrasoAlDevolver(): Attribute
    {
        return Attribute::get(function (): int {
            if ($this->fecha_devolucion === null) {
                return 0;
            }

            $limite = $this->fecha_compromiso->endOfDay();

            return $this->fecha_devolucion->isAfter($limite)
                ? (int) $this->fecha_compromiso->diffInDays($this->fecha_devolucion->startOfDay())
                : 0;
        });
    }
}
