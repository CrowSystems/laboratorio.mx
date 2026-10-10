<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Préstamos: el corazón del sistema.
 *
 * Un préstamo abierto es el que todavía no tiene `fecha_devolucion`.
 * Un préstamo vencido es uno abierto cuya `fecha_compromiso` ya pasó.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();

            // Llaves foráneas. restrictOnDelete() impide borrar un equipo o un
            // alumno que tenga historial de préstamos: así no quedan registros
            // huérfanos apuntando a un id que ya no existe.
            $table->foreignId('equipo_id')->constrained('equipos')->restrictOnDelete();
            $table->foreignId('alumno_id')->constrained('alumnos')->restrictOnDelete();
            // Quién autorizó la salida del equipo (el encargado o el auxiliar).
            $table->foreignId('autorizado_por')->constrained('usuarios')->restrictOnDelete();

            $table->dateTime('fecha_salida');
            $table->date('fecha_compromiso');
            $table->dateTime('fecha_devolucion')->nullable();
            // Se llena solo al devolver; se convierte al enum CondicionDevolucion.
            $table->string('condicion_devolucion', 20)->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();

            // El listado de vencidos busca por "fecha_devolucion nula y
            // fecha_compromiso anterior a hoy": este índice compuesto es
            // exactamente el que necesita esa consulta.
            $table->index(['fecha_devolucion', 'fecha_compromiso']);
            $table->index(['alumno_id', 'fecha_devolucion']);
            $table->index(['equipo_id', 'fecha_devolucion']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
