<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CondicionDevolucion;
use App\Enums\EstadoEquipo;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prestamo>
 */
class PrestamoFactory extends Factory
{
    protected $model = Prestamo::class;

    /**
     * Por omisión genera un préstamo abierto y al corriente.
     *
     * Las llaves foráneas se declaran como factories: si no se les pasa un
     * equipo o un alumno, los crea solos. Eso permite escribir en una prueba
     * un simple Prestamo::factory()->create() y tener todo lo necesario.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $salida = fake()->dateTimeBetween('-10 days', '-1 day');

        return [
            'equipo_id' => Equipo::factory()->prestado(),
            'alumno_id' => Alumno::factory(),
            'autorizado_por' => Usuario::factory(),
            'fecha_salida' => $salida,
            'fecha_compromiso' => now()->addDays(fake()->numberBetween(1, 7))->toDateString(),
            'fecha_devolucion' => null,
            'condicion_devolucion' => null,
            'notas' => null,
        ];
    }

    /**
     * Préstamo vencido: salió hace tiempo y la fecha comprometida ya pasó.
     */
    public function vencido(int $diasDeAtraso = 5): static
    {
        return $this->state(fn (): array => [
            'fecha_salida' => now()->subDays($diasDeAtraso + 7),
            'fecha_compromiso' => now()->subDays($diasDeAtraso)->toDateString(),
            'fecha_devolucion' => null,
        ]);
    }

    /**
     * Préstamo ya cerrado. El equipo queda según la condición de devolución.
     */
    public function devuelto(CondicionDevolucion $condicion = CondicionDevolucion::Bien): static
    {
        return $this
            ->state(fn (): array => [
                'equipo_id' => Equipo::factory()->conEstado($condicion->estadoResultante()),
                'fecha_salida' => now()->subDays(12),
                'fecha_compromiso' => now()->subDays(5)->toDateString(),
                'fecha_devolucion' => now()->subDays(6),
                'condicion_devolucion' => $condicion,
                'notas' => $condicion === CondicionDevolucion::ConDano
                    ? 'Al devolver: la bisagra de la pantalla quedó floja.'
                    : null,
            ]);
    }

    /**
     * Préstamo de un equipo concreto, dejándolo marcado como prestado.
     */
    public function deEquipo(Equipo $equipo): static
    {
        $equipo->update(['estado' => EstadoEquipo::Prestado]);

        return $this->state(['equipo_id' => $equipo->id]);
    }

    public function deAlumno(Alumno $alumno): static
    {
        return $this->state(['alumno_id' => $alumno->id]);
    }
}
