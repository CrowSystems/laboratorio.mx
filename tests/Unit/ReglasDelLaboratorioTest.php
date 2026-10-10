<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\CondicionDevolucion;
use App\Enums\EstadoEquipo;
use App\Enums\RolUsuario;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use App\Rules\SemestreSuficienteParaEquipo;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Pruebas de las piezas sueltas: enums, scopes, atributos y reglas.
 *
 * Son pruebas "de unidad": no levantan una petición HTTP, le hablan
 * directamente a la clase. Corren más rápido y, cuando fallan, dicen con
 * exactitud qué pieza se rompió.
 */
class ReglasDelLaboratorioTest extends TestCase
{
    #[Test]
    public function el_enum_de_estados_sabe_cual_se_puede_prestar(): void
    {
        $this->assertTrue(EstadoEquipo::Disponible->permitePrestamo());

        $this->assertFalse(EstadoEquipo::Prestado->permitePrestamo());
        $this->assertFalse(EstadoEquipo::EnReparacion->permitePrestamo());
        $this->assertFalse(EstadoEquipo::Baja->permitePrestamo());
    }

    #[Test]
    public function la_condicion_de_devolucion_decide_el_estado_resultante(): void
    {
        $this->assertSame(EstadoEquipo::Disponible, CondicionDevolucion::Bien->estadoResultante());
        $this->assertSame(EstadoEquipo::EnReparacion, CondicionDevolucion::ConDano->estadoResultante());
    }

    #[Test]
    public function los_enums_tienen_etiqueta_en_espanol_para_cada_caso(): void
    {
        foreach (EstadoEquipo::cases() as $estado) {
            $this->assertNotSame('', $estado->etiqueta());
        }

        foreach (CondicionDevolucion::cases() as $condicion) {
            $this->assertNotSame('', $condicion->etiqueta());
        }

        $this->assertSame('Encargado del laboratorio', RolUsuario::Admin->etiqueta());
        $this->assertSame('Auxiliar de mostrador', RolUsuario::Auxiliar->etiqueta());
    }

    #[Test]
    public function el_estado_se_lee_como_enum_y_no_como_texto(): void
    {
        // Esto es lo que aportan los casts: la columna guarda 'en_reparacion'
        // y el modelo devuelve el caso del enum.
        $equipo = Equipo::factory()->create(['estado' => EstadoEquipo::EnReparacion]);

        $this->assertSame(EstadoEquipo::EnReparacion, $equipo->fresh()->estado);
        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'estado' => 'en_reparacion']);
    }

    #[Test]
    public function el_scope_vencidos_solo_trae_prestamos_abiertos_y_atrasados(): void
    {
        $vencido = Prestamo::factory()->vencido(diasDeAtraso: 3)->create();
        $alCorriente = Prestamo::factory()->create(['fecha_compromiso' => now()->addDays(2)->toDateString()]);
        $devuelto = Prestamo::factory()->devuelto()->create();

        $ids = Prestamo::query()->vencidos()->pluck('id');

        $this->assertTrue($ids->contains($vencido->id));
        $this->assertFalse($ids->contains($alCorriente->id));
        $this->assertFalse($ids->contains($devuelto->id));
    }

    #[Test]
    public function el_scope_disponibles_filtra_el_catalogo(): void
    {
        $libre = Equipo::factory()->create();
        $fuera = Equipo::factory()->prestado()->create();
        $taller = Equipo::factory()->enReparacion()->create();

        $ids = Equipo::query()->disponibles()->pluck('id');

        $this->assertTrue($ids->contains($libre->id));
        $this->assertFalse($ids->contains($fuera->id));
        $this->assertFalse($ids->contains($taller->id));
    }

    #[Test]
    public function el_atributo_dias_atraso_cuenta_los_dias_completos(): void
    {
        $prestamo = Prestamo::factory()->vencido(diasDeAtraso: 5)->create();

        $this->assertTrue($prestamo->esta_vencido);
        $this->assertSame(5, $prestamo->dias_atraso);
    }

    #[Test]
    public function un_prestamo_al_corriente_tiene_cero_dias_de_atraso(): void
    {
        $prestamo = Prestamo::factory()->create([
            'fecha_compromiso' => now()->addDays(4)->toDateString(),
        ]);

        $this->assertFalse($prestamo->esta_vencido);
        $this->assertTrue($prestamo->esta_abierto);
        $this->assertSame(0, $prestamo->dias_atraso);
    }

    #[Test]
    public function un_prestamo_devuelto_ya_no_esta_vencido_aunque_se_haya_entregado_tarde(): void
    {
        $prestamo = Prestamo::factory()->devuelto()->create([
            'fecha_compromiso' => now()->subDays(10)->toDateString(),
            'fecha_devolucion' => now()->subDays(6),
        ]);

        $this->assertFalse($prestamo->esta_abierto);
        $this->assertFalse($prestamo->esta_vencido);
        $this->assertSame(0, $prestamo->dias_atraso);
        // Pero sí queda el registro de que se entregó con cuatro días de retraso.
        $this->assertSame(4, $prestamo->dias_atraso_al_devolver);
    }

    #[Test]
    public function el_alumno_sabe_si_trae_adeudo_vencido(): void
    {
        $deudor = Alumno::factory()->create();
        Prestamo::factory()->deAlumno($deudor)->vencido()->create();

        $alDia = Alumno::factory()->create();
        Prestamo::factory()->deAlumno($alDia)->create([
            'fecha_compromiso' => now()->addDays(3)->toDateString(),
        ]);

        $this->assertTrue($deudor->tieneAdeudoVencido());
        $this->assertFalse($alDia->tieneAdeudoVencido());
    }

    #[Test]
    public function la_regla_de_semestre_rechaza_al_alumno_que_no_alcanza(): void
    {
        $equipo = Equipo::factory()->restringidoDesde(6)->create();
        $alumno = Alumno::factory()->enSemestre(3)->create();

        $validador = Validator::make(
            ['alumno_id' => $alumno->id, 'equipo_id' => $equipo->id],
            ['equipo_id' => [new SemestreSuficienteParaEquipo]],
        );

        $this->assertTrue($validador->fails());
        $this->assertStringContainsString('6.º semestre', $validador->errors()->first('equipo_id'));
    }

    #[Test]
    public function la_regla_de_semestre_acepta_al_alumno_que_si_alcanza(): void
    {
        $equipo = Equipo::factory()->restringidoDesde(4)->create();
        $alumno = Alumno::factory()->enSemestre(9)->create();

        $validador = Validator::make(
            ['alumno_id' => $alumno->id, 'equipo_id' => $equipo->id],
            ['equipo_id' => [new SemestreSuficienteParaEquipo]],
        );

        $this->assertFalse($validador->fails());
    }

    #[Test]
    public function el_equipo_sin_restriccion_lo_puede_llevar_cualquier_alumno(): void
    {
        $equipo = Equipo::factory()->sinRestriccion()->create();
        $alumno = Alumno::factory()->enSemestre(1)->create();

        $this->assertFalse($equipo->estaRestringido());

        $validador = Validator::make(
            ['alumno_id' => $alumno->id, 'equipo_id' => $equipo->id],
            ['equipo_id' => [new SemestreSuficienteParaEquipo]],
        );

        $this->assertFalse($validador->fails());
    }

    #[Test]
    public function la_contrasena_nunca_se_guarda_en_texto_plano(): void
    {
        $usuario = Usuario::factory()->create(['contrasena' => 'laboratorio123']);

        $this->assertNotSame('laboratorio123', $usuario->contrasena);
        $this->assertTrue(password_verify('laboratorio123', $usuario->contrasena));
    }
}
