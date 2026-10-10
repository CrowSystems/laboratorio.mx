<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoEquipo;
use Database\Factories\EquipoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Una pieza de equipo del catálogo del laboratorio.
 *
 * @property-read int $id
 * @property string $numero_inventario
 * @property string $tipo
 * @property string $marca
 * @property string $modelo
 * @property string|null $descripcion
 * @property EstadoEquipo $estado
 * @property int $semestre_minimo
 */
#[Fillable([
    'numero_inventario',
    'tipo',
    'marca',
    'modelo',
    'descripcion',
    'estado',
    'semestre_minimo',
])]
class Equipo extends Model
{
    /** @use HasFactory<EquipoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'estado' => EstadoEquipo::class,
            'semestre_minimo' => 'integer',
        ];
    }

    /**
     * Historial completo de préstamos de este equipo.
     *
     * @return HasMany<Prestamo, $this>
     */
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class);
    }

    /**
     * El préstamo abierto, si el equipo está fuera del laboratorio.
     *
     * Es una relación hasOne y no un método que consulte la base de datos,
     * para poder precargarla con ->with('prestamoVigente') y evitar el
     * problema N+1 en los listados.
     *
     * @return HasOne<Prestamo, $this>
     */
    public function prestamoVigente(): HasOne
    {
        return $this->hasOne(Prestamo::class)
            ->whereNull('fecha_devolucion')
            ->latestOfMany();
    }

    /**
     * Solo el equipo que se puede prestar hoy.
     *
     * Los scopes de Laravel 13 se marcan con el atributo #[Scope] y se usan
     * como si fueran métodos del query builder: Equipo::disponibles()->get().
     *
     * @param  Builder<Equipo>  $query
     */
    #[Scope]
    protected function disponibles(Builder $query): void
    {
        $query->where('estado', EstadoEquipo::Disponible);
    }

    /**
     * Filtra por un estado concreto.
     *
     * @param  Builder<Equipo>  $query
     */
    #[Scope]
    protected function porEstado(Builder $query, EstadoEquipo $estado): void
    {
        $query->where('estado', $estado);
    }

    /**
     * Busca por número de inventario, tipo, marca o modelo.
     *
     * @param  Builder<Equipo>  $query
     */
    #[Scope]
    protected function buscar(Builder $query, ?string $termino): void
    {
        $query->when(
            filled($termino),
            fn (Builder $q) => $q->where(function (Builder $q) use ($termino): void {
                $patron = '%'.$termino.'%';

                $q->where('numero_inventario', 'like', $patron)
                    ->orWhere('tipo', 'like', $patron)
                    ->orWhere('marca', 'like', $patron)
                    ->orWhere('modelo', 'like', $patron);
            })
        );
    }

    /**
     * Ordena por número de inventario, que es como lo buscan los encargados.
     *
     * @param  Builder<Equipo>  $query
     */
    #[Scope]
    protected function porInventario(Builder $query): void
    {
        $query->orderBy('numero_inventario');
    }

    /**
     * Texto corto para mostrar el equipo en una sola línea.
     */
    public function nombreCompleto(): string
    {
        return "{$this->tipo} {$this->marca} {$this->modelo}";
    }

    /**
     * ¿Tiene restricción de semestre?
     */
    public function estaRestringido(): bool
    {
        return $this->semestre_minimo > 1;
    }
}
