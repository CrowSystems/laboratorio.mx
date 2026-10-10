<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoEquipo;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Services\ResumenDelTablero;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tablero y listado de vencidos.
 */
class TableroTest extends TestCase
{
    #[Test]
    public function el_tablero_cuenta_el_equipo_por_estado(): void
    {
        Equipo::factory()->count(3)->conEstado(EstadoEquipo::Disponible)->create();
        Equipo::factory()->count(2)->conEstado(EstadoEquipo::Prestado)->create();
        Equipo::factory()->conEstado(EstadoEquipo::EnReparacion)->create();
        Equipo::factory()->conEstado(EstadoEquipo::Baja)->create();

        $conteos = app(ResumenDelTablero::class)->conteosDeEquipo();

        $this->assertSame(7, $conteos['total']);
        $this->assertSame(3, $conteos['disponible']);
        $this->assertSame(2, $conteos['prestado']);
        $this->assertSame(1, $conteos['en_reparacion']);
        $this->assertSame(1, $conteos['baja']);
    }

    #[Test]
    public function el_tablero_muestra_los_conteos_en_pantalla(): void
    {
        Equipo::factory()->count(4)->conEstado(EstadoEquipo::Disponible)->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('tablero'))
            ->assertOk()
            ->assertSee('Equipo total', escape: false)
            ->assertSee('Disponible')
            ->assertSee('En reparación', escape: false)
            ->assertSee('Vencidos');
    }

    #[Test]
    public function el_tablero_lista_los_cinco_equipos_mas_solicitados(): void
    {
        $estrella = Equipo::factory()->create(['numero_inventario' => 'LAB-1001']);
        $ocasional = Equipo::factory()->create(['numero_inventario' => 'LAB-9001']);

        Prestamo::factory()->count(5)->devuelto()->create(['equipo_id' => $estrella->id]);
        Prestamo::factory()->devuelto()->create(['equipo_id' => $ocasional->id]);

        $ranking = app(ResumenDelTablero::class)->equiposMasSolicitados();

        $this->assertSame('LAB-1001', $ranking->first()->numero_inventario);
        $this->assertSame(5, $ranking->first()->prestamos_count);
        // Un equipo que nunca salió no aparece en el ranking.
        $this->assertCount(2, $ranking);
    }

    #[Test]
    public function el_listado_de_vencidos_muestra_los_dias_de_atraso(): void
    {
        $alumno = Alumno::factory()->create(['nombre' => 'Rodrigo Alcántara Peña']);

        Prestamo::factory()
            ->deAlumno($alumno)
            ->vencido(diasDeAtraso: 6)
            ->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('vencidos.index'))
            ->assertOk()
            ->assertSee('Rodrigo Alcántara Peña', escape: false)
            ->assertSee('6 días', escape: false);
    }

    #[Test]
    public function el_listado_de_vencidos_ignora_lo_que_ya_se_devolvio(): void
    {
        // Devuelto tarde, pero devuelto: ya no es un adeudo.
        $devuelto = Prestamo::factory()->devuelto()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('vencidos.index'))
            ->assertOk()
            ->assertSee('Nada atrasado', escape: false)
            ->assertDontSee($devuelto->equipo->numero_inventario);
    }

    #[Test]
    public function el_listado_de_vencidos_ignora_lo_que_esta_dentro_del_plazo(): void
    {
        Prestamo::factory()->create([
            'fecha_compromiso' => now()->addDays(5)->toDateString(),
        ]);

        $this->assertSame(0, app(ResumenDelTablero::class)->prestamosVencidos());
    }

    #[Test]
    public function el_auxiliar_tambien_ve_el_tablero_y_los_vencidos(): void
    {
        $auxiliar = Usuario::factory()->auxiliar()->create();

        $this->actingAs($auxiliar)->get(route('tablero'))->assertOk();
        $this->actingAs($auxiliar)->get(route('vencidos.index'))->assertOk();
    }
}
