<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RolUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Usuario>
 */
class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'correo' => fake()->unique()->safeEmail(),
            'contrasena' => 'laboratorio123',
            'rol' => RolUsuario::Auxiliar,
        ];
    }

    /**
     * Estado de la factory: crea un encargado del laboratorio.
     *
     * Se usa así en las pruebas: Usuario::factory()->admin()->create().
     * Tener estados con nombre evita repetir ['rol' => ...] en cada prueba.
     */
    public function admin(): static
    {
        return $this->state(['rol' => RolUsuario::Admin]);
    }

    public function auxiliar(): static
    {
        return $this->state(['rol' => RolUsuario::Auxiliar]);
    }
}
