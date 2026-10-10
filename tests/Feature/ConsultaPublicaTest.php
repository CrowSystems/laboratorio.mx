<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * La consulta por matrícula que el alumno abre en su celular, sin login.
 */
class ConsultaPublicaTest extends TestCase
{
    #[Test]
    public function la_pantalla_de_consulta_se_abre_sin_iniciar_sesion(): void
    {
        $this->get(route('consulta.index'))
            ->assertOk()
            ->assertSee('¿Qué equipo tengo pendiente?', escape: false);
    }

    #[Test]
    public function la_consulta_muestra_el_equipo_que_el_alumno_no_ha_regresado(): void
    {
        $alumno = Alumno::factory()->create([
            'matricula' => 'SC202401',
            'nombre' => 'Diana Laura Moreno Vázquez',
        ]);

        $equipo = Equipo::factory()->prestado()->create([
            'numero_inventario' => 'LAB-1001',
            'tipo' => 'Laptop',
            'marca' => 'Dell',
            'modelo' => 'Latitude 5450',
        ]);

        Prestamo::factory()
            ->deAlumno($alumno)
            ->create(['equipo_id' => $equipo->id]);

        $this->post(route('consulta.buscar'), ['matricula' => 'SC202401'])
            ->assertOk()
            ->assertSee('Diana Laura Moreno Vázquez', escape: false)
            ->assertSee('LAB-1001')
            ->assertSee('Latitude 5450');
    }

    #[Test]
    public function la_consulta_avisa_cuando_el_alumno_no_debe_nada(): void
    {
        Alumno::factory()->create(['matricula' => 'SC202407']);

        $this->post(route('consulta.buscar'), ['matricula' => 'SC202407'])
            ->assertOk()
            ->assertSee('No tienes nada pendiente', escape: false);
    }

    #[Test]
    public function la_consulta_de_una_matricula_inexistente_no_truena(): void
    {
        $this->post(route('consulta.buscar'), ['matricula' => 'ZZ999999'])
            ->assertOk()
            ->assertSee('No encontramos la matrícula ZZ999999', escape: false);
    }

    #[Test]
    public function la_consulta_valida_el_formato_de_la_matricula(): void
    {
        $this->post(route('consulta.buscar'), ['matricula' => '123'])
            ->assertSessionHasErrors('matricula');
    }

    #[Test]
    public function la_matricula_se_acepta_en_minusculas_porque_se_escribe_en_el_celular(): void
    {
        Alumno::factory()->create(['matricula' => 'SC202401', 'nombre' => 'Emiliano Zúñiga Robles']);

        // prepareForValidation() del Form Request la pasa a mayúsculas.
        $this->post(route('consulta.buscar'), ['matricula' => '  sc202401 '])
            ->assertOk()
            ->assertSee('Emiliano Zúñiga Robles', escape: false);
    }

    #[Test]
    public function la_consulta_no_revela_los_prestamos_de_otros_alumnos(): void
    {
        $consultante = Alumno::factory()->create(['matricula' => 'SC202401']);
        $otro = Alumno::factory()->create(['matricula' => 'SC202402', 'nombre' => 'Fernanda Guadalupe Ríos']);

        $equipoDelOtro = Equipo::factory()->prestado()->create(['numero_inventario' => 'LAB-9999']);

        Prestamo::factory()
            ->deAlumno($otro)
            ->create(['equipo_id' => $equipoDelOtro->id]);

        $this->post(route('consulta.buscar'), ['matricula' => $consultante->matricula])
            ->assertOk()
            ->assertDontSee('LAB-9999')
            ->assertDontSee('Fernanda Guadalupe Ríos', escape: false);
    }

    #[Test]
    public function la_consulta_separa_lo_vencido_de_lo_que_esta_a_tiempo(): void
    {
        $alumno = Alumno::factory()->create(['matricula' => 'SC202402']);

        Prestamo::factory()
            ->deAlumno($alumno)
            ->vencido(diasDeAtraso: 7)
            ->create();

        $this->post(route('consulta.buscar'), ['matricula' => 'SC202402'])
            ->assertOk()
            ->assertSee('Entrega vencida', escape: false)
            ->assertSee('7 días', escape: false);
    }
}
