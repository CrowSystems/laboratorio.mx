<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\CondicionDevolucion;
use App\Enums\EstadoEquipo;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Registro de préstamos y LAS TRES REGLAS DE NEGOCIO del laboratorio.
 *
 *   Regla 1 — Equipo restringido por semestre.
 *   Regla 2 — Un alumno con adeudo vencido no se lleva nada más.
 *   Regla 3 — Solo se presta equipo disponible.
 *
 * Cada regla tiene su prueba del caso que debe fallar Y del caso que debe
 * pasar. Probar solo el fallo es trampa: una regla que siempre dice "no"
 * también pasaría esa prueba.
 */
class PrestamoTest extends TestCase
{
    #[Test]
    public function registrar_un_prestamo_marca_el_equipo_como_prestado(): void
    {
        $equipo = Equipo::factory()->sinRestriccion()->create();
        $alumno = Alumno::factory()->enSemestre(3)->create();
        $encargado = Usuario::factory()->admin()->create();

        $this->actingAs($encargado)
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addDays(3)->toDateString(),
                'notas' => 'Se entrega con cargador.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('prestamos', [
            'equipo_id' => $equipo->id,
            'alumno_id' => $alumno->id,
            'autorizado_por' => $encargado->id,
            'fecha_devolucion' => null,
        ]);

        // El cambio de estado es automático: el auxiliar no lo captura.
        $this->assertSame(EstadoEquipo::Prestado, $equipo->fresh()->estado);
    }

    #[Test]
    public function el_auxiliar_si_puede_registrar_prestamos(): void
    {
        $equipo = Equipo::factory()->sinRestriccion()->create();
        $alumno = Alumno::factory()->enSemestre(1)->create();

        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addDay()->toDateString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('prestamos', 1);
    }

    // -----------------------------------------------------------------------
    // REGLA 1 — Equipo restringido por semestre
    // -----------------------------------------------------------------------

    #[Test]
    public function regla_1_un_alumno_de_semestre_menor_no_puede_recibir_equipo_restringido(): void
    {
        // El osciloscopio se presta de 6.º semestre en adelante…
        $osciloscopio = Equipo::factory()->restringidoDesde(6)->create([
            'numero_inventario' => 'LAB-3007',
            'tipo' => 'Osciloscopio',
        ]);

        // …y este alumno va en 4.º.
        $alumno = Alumno::factory()->enSemestre(4)->create(['nombre' => 'Itzel Marisol Padilla']);

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $osciloscopio->id,
                'fecha_compromiso' => now()->addDays(2)->toDateString(),
            ])
            ->assertSessionHasErrors('equipo_id');

        // Ni préstamo creado ni estado cambiado.
        $this->assertDatabaseCount('prestamos', 0);
        $this->assertSame(EstadoEquipo::Disponible, $osciloscopio->fresh()->estado);
    }

    #[Test]
    public function regla_1_el_mensaje_de_error_dice_que_semestre_se_necesita(): void
    {
        $equipo = Equipo::factory()->restringidoDesde(7)->create(['numero_inventario' => 'LAB-4005']);
        $alumno = Alumno::factory()->enSemestre(2)->create();

        $respuesta = $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addDay()->toDateString(),
            ]);

        $errores = $respuesta->assertSessionHasErrors('equipo_id')
            ->getSession()
            ->get('errors')
            ->get('equipo_id');

        $this->assertStringContainsString('7.º semestre', $errores[0]);
        $this->assertStringContainsString('LAB-4005', $errores[0]);
    }

    #[Test]
    public function regla_1_un_alumno_con_el_semestre_suficiente_si_recibe_el_equipo(): void
    {
        $equipo = Equipo::factory()->restringidoDesde(6)->create();
        // Exactamente en el semestre mínimo: el límite es inclusivo.
        $alumno = Alumno::factory()->enSemestre(6)->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addDays(2)->toDateString(),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseCount('prestamos', 1);
    }

    // -----------------------------------------------------------------------
    // REGLA 2 — Nada nuevo con un adeudo vencido
    // -----------------------------------------------------------------------

    #[Test]
    public function regla_2_un_alumno_con_prestamo_vencido_no_puede_llevarse_nada_mas(): void
    {
        $alumno = Alumno::factory()->enSemestre(8)->create(['nombre' => 'Emiliano Zúñiga Robles']);

        // Ya trae una laptop con nueve días de atraso.
        Prestamo::factory()
            ->deAlumno($alumno)
            ->vencido(diasDeAtraso: 9)
            ->create();

        $otroEquipo = Equipo::factory()->sinRestriccion()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $otroEquipo->id,
                'fecha_compromiso' => now()->addDays(2)->toDateString(),
            ])
            ->assertSessionHasErrors('alumno_id');

        // Sigue habiendo un solo préstamo: el vencido.
        $this->assertDatabaseCount('prestamos', 1);
        $this->assertSame(EstadoEquipo::Disponible, $otroEquipo->fresh()->estado);
    }

    #[Test]
    public function regla_2_un_prestamo_abierto_pero_al_corriente_no_bloquea_al_alumno(): void
    {
        $alumno = Alumno::factory()->enSemestre(8)->create();

        // Trae equipo, pero su fecha comprometida aún no llega.
        Prestamo::factory()->deAlumno($alumno)->create([
            'fecha_compromiso' => now()->addDays(3)->toDateString(),
        ]);

        $otroEquipo = Equipo::factory()->sinRestriccion()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $otroEquipo->id,
                'fecha_compromiso' => now()->addDays(2)->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('prestamos', 2);
    }

    #[Test]
    public function regla_2_al_regresar_lo_vencido_el_alumno_vuelve_a_poder_prestar(): void
    {
        $alumno = Alumno::factory()->enSemestre(8)->create();
        $encargado = Usuario::factory()->admin()->create();

        $vencido = Prestamo::factory()
            ->deAlumno($alumno)
            ->vencido(diasDeAtraso: 4)
            ->create();

        // Primero regresa lo que debía…
        $this->actingAs($encargado)
            ->post(route('devoluciones.store', $vencido), [
                'condicion_devolucion' => CondicionDevolucion::Bien->value,
            ])
            ->assertSessionHasNoErrors();

        // …y ahora sí le pueden prestar otra cosa.
        $otroEquipo = Equipo::factory()->sinRestriccion()->create();

        $this->actingAs($encargado)
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $otroEquipo->id,
                'fecha_compromiso' => now()->addDays(2)->toDateString(),
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame(EstadoEquipo::Prestado, $otroEquipo->fresh()->estado);
    }

    // -----------------------------------------------------------------------
    // REGLA 3 — Solo equipo disponible
    // -----------------------------------------------------------------------

    #[Test]
    public function regla_3_no_se_puede_prestar_equipo_que_ya_esta_prestado(): void
    {
        $equipo = Equipo::factory()->prestado()->sinRestriccion()->create();
        $alumno = Alumno::factory()->enSemestre(5)->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('equipo_id');

        $this->assertDatabaseCount('prestamos', 0);
    }

    #[Test]
    public function regla_3_no_se_puede_prestar_equipo_en_reparacion_ni_dado_de_baja(): void
    {
        $alumno = Alumno::factory()->enSemestre(9)->create();
        $encargado = Usuario::factory()->admin()->create();

        foreach ([EstadoEquipo::EnReparacion, EstadoEquipo::Baja] as $estado) {
            $equipo = Equipo::factory()->sinRestriccion()->conEstado($estado)->create();

            $this->actingAs($encargado)
                ->post(route('prestamos.store'), [
                    'alumno_id' => $alumno->id,
                    'equipo_id' => $equipo->id,
                    'fecha_compromiso' => now()->addDay()->toDateString(),
                ])
                ->assertSessionHasErrors('equipo_id');
        }

        $this->assertDatabaseCount('prestamos', 0);
    }

    // -----------------------------------------------------------------------
    // Validación del resto del formulario
    // -----------------------------------------------------------------------

    #[Test]
    public function la_fecha_de_compromiso_no_puede_ser_anterior_a_hoy(): void
    {
        $equipo = Equipo::factory()->sinRestriccion()->create();
        $alumno = Alumno::factory()->enSemestre(5)->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->subDay()->toDateString(),
            ])
            ->assertSessionHasErrors('fecha_compromiso');
    }

    #[Test]
    public function el_prestamo_no_puede_pasar_de_un_mes(): void
    {
        $equipo = Equipo::factory()->sinRestriccion()->create();
        $alumno = Alumno::factory()->enSemestre(5)->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => $alumno->id,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addMonths(3)->toDateString(),
            ])
            ->assertSessionHasErrors('fecha_compromiso');
    }

    #[Test]
    public function no_se_puede_prestar_a_un_alumno_que_no_existe(): void
    {
        $equipo = Equipo::factory()->sinRestriccion()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('prestamos.store'), [
                'alumno_id' => 999999,
                'equipo_id' => $equipo->id,
                'fecha_compromiso' => now()->addDay()->toDateString(),
            ])
            ->assertSessionHasErrors('alumno_id');
    }

    #[Test]
    public function el_listado_de_prestamos_filtra_los_que_siguen_abiertos(): void
    {
        $abierto = Prestamo::factory()->create();
        $cerrado = Prestamo::factory()->devuelto()->create();

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('prestamos.index', ['filtro' => 'abiertos']))
            ->assertOk()
            ->assertSee($abierto->equipo->numero_inventario)
            ->assertDontSee($cerrado->equipo->numero_inventario);
    }
}
