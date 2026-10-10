<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Usuarios del sistema: los dos encargados del laboratorio.
 *
 * Nota para el alumno: Laravel genera por defecto una tabla `users` con
 * columnas en inglés. Aquí la nombramos `usuarios` con columnas en español
 * para respetar la convención del proyecto. Eso obliga a dos ajustes que
 * puedes ver en el código:
 *   1. El modelo App\Models\Usuario declara $table = 'usuarios' y sobreescribe
 *      getAuthPassword() para que Laravel sepa que la contraseña vive en la
 *      columna `contrasena`.
 *   2. config/auth.php apunta al modelo Usuario en lugar de User.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->string('correo', 160)->unique();
            $table->string('contrasena');
            // El rol se guarda como texto corto y se convierte al enum
            // App\Enums\RolUsuario mediante los casts del modelo.
            $table->string('rol', 20)->default('auxiliar')->index();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
