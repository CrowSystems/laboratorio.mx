<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * Los dos encargados del laboratorio.
 *
 * updateOrCreate() en lugar de create(): así el seeder se puede volver a correr
 * sin chocar con la restricción de unicidad del correo.
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::updateOrCreate(
            ['correo' => 'encargado@cesba.edu.mx'],
            [
                'nombre' => 'Mariana Esquivel Rangel',
                'contrasena' => 'laboratorio123',
                'rol' => RolUsuario::Admin,
            ],
        );

        Usuario::updateOrCreate(
            ['correo' => 'auxiliar@cesba.edu.mx'],
            [
                'nombre' => 'Jonathan Ibarra Mejía',
                'contrasena' => 'laboratorio123',
                'rol' => RolUsuario::Auxiliar,
            ],
        );
    }
}
