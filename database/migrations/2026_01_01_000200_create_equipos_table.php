<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de equipo del laboratorio de cómputo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            // El número de inventario es la etiqueta física pegada al equipo.
            // No pueden existir dos iguales: lo garantiza la base de datos,
            // no solo la validación del formulario.
            $table->string('numero_inventario', 30)->unique();
            $table->string('tipo', 60);
            $table->string('marca', 60);
            $table->string('modelo', 80);
            $table->text('descripcion')->nullable();
            $table->string('estado', 20)->default('disponible');
            // Regla de negocio: equipo restringido por semestre. Un alumno de
            // semestre menor a este número no puede recibirlo en préstamo.
            // El valor 1 significa "sin restricción".
            $table->unsignedTinyInteger('semestre_minimo')->default(1);
            $table->timestamps();

            // El tablero y los listados filtran constantemente por estado y
            // por tipo; estos índices evitan recorrer la tabla completa.
            $table->index('estado');
            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
