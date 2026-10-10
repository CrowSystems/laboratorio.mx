<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Alumno;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumno>
 */
class AlumnoFactory extends Factory
{
    protected $model = Alumno::class;

    /**
     * Carreras que ofrece el CESBA.
     *
     * @var list<string>
     */
    protected const CARRERAS = [
        'Licenciatura en Sistemas Computacionales',
        'Licenciatura en Administración',
        'Licenciatura en Contaduría',
        'Licenciatura en Mercadotecnia',
        'Licenciatura en Derecho',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Dos letras del plan y seis dígitos: SC202401.
        $matricula = fake()->unique()->regexify('[A-Z]{2}[0-9]{6}');

        return [
            'matricula' => $matricula,
            'nombre' => fake()->name(),
            'carrera' => fake()->randomElement(self::CARRERAS),
            'semestre' => fake()->numberBetween(1, 9),
            // El correo se deriva de la matrícula: si la matrícula es única, el
            // correo también. safeEmail() tiene un catálogo chico de nombres y
            // unique() solo recuerda lo generado en ESTE proceso, no lo que ya
            // está en la base, así que choca al volver a correr el seeder.
            'correo' => strtolower($matricula).'@alumnos.cesba.edu.mx',
        ];
    }

    /**
     * Alumno de un semestre concreto, para probar la regla del semestre mínimo.
     */
    public function enSemestre(int $semestre): static
    {
        return $this->state(['semestre' => $semestre]);
    }

    public function deSistemas(): static
    {
        return $this->state(['carrera' => 'Licenciatura en Sistemas Computacionales']);
    }
}
