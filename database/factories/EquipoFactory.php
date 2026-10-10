<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Equipo>
 */
class EquipoFactory extends Factory
{
    protected $model = Equipo::class;

    /**
     * Equipo típico de un laboratorio de cómputo, con su marca y modelo reales.
     *
     * @var list<array{tipo: string, marca: string, modelo: string, semestre: int}>
     */
    protected const CATALOGO = [
        ['tipo' => 'Laptop', 'marca' => 'Dell', 'modelo' => 'Latitude 5450', 'semestre' => 1],
        ['tipo' => 'Laptop', 'marca' => 'Lenovo', 'modelo' => 'ThinkPad E14', 'semestre' => 1],
        ['tipo' => 'Proyector', 'marca' => 'Epson', 'modelo' => 'PowerLite E20', 'semestre' => 1],
        ['tipo' => 'Tableta gráfica', 'marca' => 'Wacom', 'modelo' => 'Intuos S', 'semestre' => 3],
        ['tipo' => 'Kit de desarrollo', 'marca' => 'Arduino', 'modelo' => 'Starter Kit UNO R3', 'semestre' => 2],
        ['tipo' => 'Kit de desarrollo', 'marca' => 'Espressif', 'modelo' => 'ESP32 DevKit V1', 'semestre' => 3],
        ['tipo' => 'Multímetro', 'marca' => 'Steren', 'modelo' => 'HER-410', 'semestre' => 2],
        ['tipo' => 'Osciloscopio', 'marca' => 'Rigol', 'modelo' => 'DS1054Z', 'semestre' => 6],
        ['tipo' => 'Analizador de red', 'marca' => 'Fluke', 'modelo' => 'LinkIQ', 'semestre' => 7],
        ['tipo' => 'Switch administrable', 'marca' => 'Cisco', 'modelo' => 'Catalyst 1000-8T', 'semestre' => 5],
        ['tipo' => 'Router', 'marca' => 'Ubiquiti', 'modelo' => 'EdgeRouter X', 'semestre' => 5],
        ['tipo' => 'Cámara web', 'marca' => 'Logitech', 'modelo' => 'C920 HD Pro', 'semestre' => 1],
        ['tipo' => 'Audífonos', 'marca' => 'HyperX', 'modelo' => 'Cloud Stinger 2', 'semestre' => 1],
        ['tipo' => 'Disco externo', 'marca' => 'Seagate', 'modelo' => 'Expansion 2 TB', 'semestre' => 1],
        ['tipo' => 'Impresora 3D', 'marca' => 'Creality', 'modelo' => 'Ender-3 V3 SE', 'semestre' => 4],
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $articulo = fake()->randomElement(self::CATALOGO);

        return [
            'numero_inventario' => 'LAB-'.fake()->unique()->numberBetween(1000, 9999),
            'tipo' => $articulo['tipo'],
            'marca' => $articulo['marca'],
            'modelo' => $articulo['modelo'],
            'descripcion' => "{$articulo['tipo']} {$articulo['marca']} {$articulo['modelo']} del laboratorio de cómputo.",
            'estado' => EstadoEquipo::Disponible,
            'semestre_minimo' => $articulo['semestre'],
        ];
    }

    /**
     * Equipo sin restricción de semestre: lo puede llevar cualquier alumno.
     */
    public function sinRestriccion(): static
    {
        return $this->state(['semestre_minimo' => 1]);
    }

    /**
     * Equipo reservado para semestres avanzados.
     */
    public function restringidoDesde(int $semestre): static
    {
        return $this->state(['semestre_minimo' => $semestre]);
    }

    public function conEstado(EstadoEquipo $estado): static
    {
        return $this->state(['estado' => $estado]);
    }

    public function prestado(): static
    {
        return $this->conEstado(EstadoEquipo::Prestado);
    }

    public function enReparacion(): static
    {
        return $this->conEstado(EstadoEquipo::EnReparacion);
    }
}
