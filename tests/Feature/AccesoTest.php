<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Entrada y salida del sistema, y protección de las rutas privadas.
 */
class AccesoTest extends TestCase
{
    #[Test]
    public function las_rutas_privadas_redirigen_al_acceso_cuando_no_hay_sesion(): void
    {
        foreach (['/tablero', '/prestamos', '/vencidos', '/equipos', '/alumnos'] as $ruta) {
            $this->get($ruta)
                ->assertRedirect(route('sesion.create'));
        }
    }

    #[Test]
    public function el_encargado_puede_iniciar_sesion_con_sus_credenciales(): void
    {
        $encargado = Usuario::factory()->admin()->create([
            'correo' => 'encargado@cesba.edu.mx',
            'contrasena' => 'laboratorio123',
        ]);

        $respuesta = $this->post(route('sesion.store'), [
            'correo' => 'encargado@cesba.edu.mx',
            'password' => 'laboratorio123',
        ]);

        $respuesta->assertRedirect(route('tablero'));
        $this->assertAuthenticatedAs($encargado);
    }

    #[Test]
    public function una_contrasena_incorrecta_no_inicia_sesion(): void
    {
        Usuario::factory()->admin()->create([
            'correo' => 'encargado@cesba.edu.mx',
            'contrasena' => 'laboratorio123',
        ]);

        $this->post(route('sesion.store'), [
            'correo' => 'encargado@cesba.edu.mx',
            'password' => 'esta-no-es',
        ])->assertSessionHasErrors('correo');

        $this->assertGuest();
    }

    #[Test]
    public function el_usuario_autenticado_ve_el_tablero(): void
    {
        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('tablero'))
            ->assertOk()
            ->assertSee('Tablero')
            ->assertSee('Equipo total', escape: false);
    }

    #[Test]
    public function cerrar_sesion_deja_al_usuario_fuera(): void
    {
        $this->actingAs(Usuario::factory()->create())
            ->post(route('sesion.destroy'))
            ->assertRedirect(route('consulta.index'));

        $this->assertGuest();
    }

    #[Test]
    public function quien_ya_tiene_sesion_no_vuelve_a_ver_el_formulario_de_acceso(): void
    {
        // El middleware 'guest' lo manda al tablero.
        $this->actingAs(Usuario::factory()->create())
            ->get(route('sesion.create'))
            ->assertRedirect(route('tablero'));
    }
}
