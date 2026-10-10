<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder principal: el que corre `php artisan db:seed`.
 *
 * El orden NO es opcional. Un préstamo necesita que ya existan el equipo, el
 * alumno y el usuario que lo autoriza, porque las tres son llaves foráneas.
 * Si se invierte el orden, la base de datos rechaza la inserción.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            UsuarioSeeder::class,
            AlumnoSeeder::class,
            EquipoSeeder::class,
            PrestamoSeeder::class,
        ]);
    }
}
