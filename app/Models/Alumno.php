<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AlumnoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Alumno con derecho a solicitar equipo.
 *
 * @property-read int $id
 * @property string $matricula
 * @property string $nombre
 * @property string $carrera
 * @property int $semestre
 * @property string $correo
 */
#[Fillable(['matricula', 'nombre', 'carrera', 'semestre', 'correo'])]
class Alumno extends Model
{
    /** @use HasFactory<AlumnoFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'semestre' => 'integer',
        ];
    }

    /**
     * Todos los préstamos del alumno, nuevos primero.
     *
     * @return HasMany<Prestamo, $this>
     */
    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class)->latest('fecha_salida');
    }

    /**
     * Préstamos que el alumno todavía no ha devuelto.
     *
     * @return HasMany<Prestamo, $this>
     */
    public function prestamosAbiertos(): HasMany
    {
        return $this->prestamos()->whereNull('fecha_devolucion');
    }

    /**
     * Préstamos abiertos cuya fecha comprometida ya pasó.
     *
     * @return HasMany<Prestamo, $this>
     */
    public function prestamosVencidos(): HasMany
    {
        return $this->prestamosAbiertos()->whereDate('fecha_compromiso', '<', today());
    }

    /**
     * Busca por matrícula o por nombre.
     *
     * @param  Builder<Alumno>  $query
     */
    #[Scope]
    protected function buscar(Builder $query, ?string $termino): void
    {
        $query->when(
            filled($termino),
            fn (Builder $q) => $q->where(function (Builder $q) use ($termino): void {
                $patron = '%'.$termino.'%';

                $q->where('matricula', 'like', $patron)
                    ->orWhere('nombre', 'like', $patron);
            })
        );
    }

    /**
     * Solo los alumnos que hoy traen algún adeudo vencido.
     *
     * @param  Builder<Alumno>  $query
     */
    #[Scope]
    protected function conAdeudo(Builder $query): void
    {
        $query->whereHas('prestamosVencidos');
    }

    /**
     * Regla de negocio: un alumno con un préstamo vencido no puede llevarse
     * nada más hasta regresarlo.
     *
     * La consulta vive aquí, en el modelo, para que tanto la validación como
     * el servicio de préstamos pregunten exactamente lo mismo.
     */
    public function tieneAdeudoVencido(): bool
    {
        return $this->prestamosVencidos()->exists();
    }
}
