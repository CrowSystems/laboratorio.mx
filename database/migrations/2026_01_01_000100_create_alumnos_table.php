<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Alumnos que pueden solicitar equipo en préstamo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();
            // La matrícula es el dato con el que el alumno se identifica en la
            // consulta pública, así que debe ser única y estar indexada:
            // sobre ella se hace la búsqueda más frecuente del sistema.
            $table->string('matricula', 20)->unique();
            $table->string('nombre', 150);
            $table->string('carrera', 120);
            // El semestre decide si el alumno alcanza el equipo restringido.
            $table->unsignedTinyInteger('semestre');
            $table->string('correo', 160)->unique();
            $table->timestamps();

            $table->index('carrera');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};
