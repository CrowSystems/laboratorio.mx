<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CondicionDevolucion;
use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Recepción del equipo.
 *
 * La regla que importa: un equipo que regresa con daño NO vuelve al catálogo
 * como disponible, se va a «en reparación».
 */
class DevolucionTest extends TestCase
{
    #[Test]
    public function una_devolucion_en_buen_estado_libera_el_equipo(): void
    {
        $equipo = Equipo::factory()->prestado()->create();
        $prestamo = Prestamo::factory()->create(['equipo_id' => $equipo->id]);

        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->post(route('devoluciones.store', $prestamo), [
                'condicion_devolucion' => CondicionDevolucion::Bien->value,
            ])
            ->assertRedirect(route('prestamos.show', $prestamo));

        $prestamo->refresh();

        $this->assertNotNull($prestamo->fecha_devolucion);
        $this->assertSame(CondicionDevolucion::Bien, $prestamo->condicion_devolucion);
        $this->assertSame(EstadoEquipo::Disponible, $equipo->fresh()->estado);
    }

    #[Test]
    public function una_devolucion_con_dano_manda_el_equipo_a_reparacion(): void
    {
        $equipo = Equipo::factory()->prestado()->create();
        $prestamo = Prestamo::factory()->create(['equipo_id' => $equipo->id]);

        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->post(route('devoluciones.store', $prestamo), [
                'condicion_devolucion' => CondicionDevolucion::ConDano->value,
                'notas' => 'La bisagra de la pantalla quedó floja.',
            ])
            ->assertRedirect();

        // Lo central de esta prueba: NO queda disponible.
        $this->assertSame(EstadoEquipo::EnReparacion, $equipo->fresh()->estado);
        $this->assertSame(CondicionDevolucion::ConDano, $prestamo->fresh()->condicion_devolucion);
    }

    #[Test]
    public function la_devolucion_con_dano_exige_describir_el_dano(): void
    {
        $equipo = Equipo::factory()->prestado()->create();
        $prestamo = Prestamo::factory()->create(['equipo_id' => $equipo->id]);

        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->post(route('devoluciones.store', $prestamo), [
                'condicion_devolucion' => CondicionDevolucion::ConDano->value,
                // Sin notas.
            ])
            ->assertSessionHasErrors('notas');

        // El préstamo sigue abierto y el equipo sigue prestado.
        $this->assertNull($prestamo->fresh()->fecha_devolucion);
        $this->assertSame(EstadoEquipo::Prestado, $equipo->fresh()->estado);
    }

    #[Test]
    public function la_devolucion_conserva_la_nota_de_la_entrega(): void
    {
        $equipo = Equipo::factory()->prestado()->create();
        $prestamo = Prestamo::factory()->create([
            'equipo_id' => $equipo->id,
            'notas' => 'Se entregó con cargador y funda.',
        ]);

        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->post(route('devoluciones.store', $prestamo), [
                'condicion_devolucion' => CondicionDevolucion::ConDano->value,
                'notas' => 'Regresó sin la funda.',
            ]);

        $notas = $prestamo->fresh()->notas;

        $this->assertStringContainsString('Se entregó con cargador y funda.', $notas);
        $this->assertStringContainsString('Regresó sin la funda.', $notas);
    }

    #[Test]
    public function un_prestamo_ya_devuelto_no_se_puede_devolver_otra_vez(): void
    {
        $prestamo = Prestamo::factory()->devuelto()->create();

        // PrestamoPolicy::devolver() niega el permiso si ya está cerrado.
        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('devoluciones.store', $prestamo), [
                'condicion_devolucion' => CondicionDevolucion::Bien->value,
            ])
            ->assertForbidden();
    }

    #[Test]
    public function el_formulario_de_devolucion_de_un_prestamo_cerrado_no_se_abre(): void
    {
        $prestamo = Prestamo::factory()->devuelto()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('devoluciones.create', $prestamo))
            ->assertForbidden();
    }

    #[Test]
    public function la_condicion_de_devolucion_es_obligatoria(): void
    {
        $prestamo = Prestamo::factory()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('devoluciones.store', $prestamo), [])
            ->assertSessionHasErrors('condicion_devolucion');
    }
}
