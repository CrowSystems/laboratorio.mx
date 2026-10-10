<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\EstadoEquipo;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Models\Usuario;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Catálogo de equipo y los permisos de cada rol.
 *
 * Lo que de verdad se prueba aquí es la autorización: que el auxiliar no pueda
 * tocar el catálogo aunque mande la petición a mano, sin pasar por la pantalla.
 */
class CatalogoDeEquipoTest extends TestCase
{
    /**
     * Datos válidos para dar de alta un equipo.
     *
     * Tenerlos en un método evita repetir siete campos en cada prueba: cada
     * prueba solo dice qué cambia respecto a lo válido.
     *
     * @param  array<string, mixed>  $cambios
     * @return array<string, mixed>
     */
    protected function datosDeEquipo(array $cambios = []): array
    {
        return array_merge([
            'numero_inventario' => 'LAB-7001',
            'tipo' => 'Laptop',
            'marca' => 'Lenovo',
            'modelo' => 'ThinkPad E14',
            'descripcion' => 'Ryzen 5, 8 GB de RAM. Incluye cargador.',
            'estado' => EstadoEquipo::Disponible->value,
            'semestre_minimo' => 1,
        ], $cambios);
    }

    #[Test]
    public function el_encargado_puede_registrar_equipo(): void
    {
        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('equipos.store'), $this->datosDeEquipo())
            ->assertRedirect();

        $this->assertDatabaseHas('equipos', [
            'numero_inventario' => 'LAB-7001',
            'marca' => 'Lenovo',
            'estado' => EstadoEquipo::Disponible->value,
        ]);
    }

    #[Test]
    public function el_auxiliar_no_puede_registrar_equipo(): void
    {
        // El middleware 'rol:admin' de la ruta corta la petición con un 403.
        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->post(route('equipos.store'), $this->datosDeEquipo())
            ->assertForbidden();

        $this->assertDatabaseCount('equipos', 0);
    }

    #[Test]
    public function el_auxiliar_no_puede_abrir_el_formulario_de_alta(): void
    {
        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->get(route('equipos.create'))
            ->assertForbidden();
    }

    #[Test]
    public function el_auxiliar_no_puede_editar_ni_dar_de_baja_equipo(): void
    {
        $equipo = Equipo::factory()->create(['numero_inventario' => 'LAB-7002']);
        $auxiliar = Usuario::factory()->auxiliar()->create();

        $this->actingAs($auxiliar)
            ->put(route('equipos.update', $equipo), $this->datosDeEquipo(['numero_inventario' => 'LAB-7002']))
            ->assertForbidden();

        $this->actingAs($auxiliar)
            ->delete(route('equipos.destroy', $equipo))
            ->assertForbidden();

        $this->assertDatabaseHas('equipos', ['id' => $equipo->id, 'numero_inventario' => 'LAB-7002']);
    }

    #[Test]
    public function el_auxiliar_si_puede_consultar_el_catalogo(): void
    {
        Equipo::factory()->create(['numero_inventario' => 'LAB-7003']);

        $this->actingAs(Usuario::factory()->auxiliar()->create())
            ->get(route('equipos.index'))
            ->assertOk()
            ->assertSee('LAB-7003')
            // El botón de alta no se le muestra, porque @can consulta la Policy.
            ->assertDontSee('Registrar equipo', escape: false);
    }

    #[Test]
    public function el_numero_de_inventario_no_se_puede_repetir(): void
    {
        Equipo::factory()->create(['numero_inventario' => 'LAB-7004']);

        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('equipos.store'), $this->datosDeEquipo(['numero_inventario' => 'LAB-7004']))
            ->assertSessionHasErrors('numero_inventario');

        $this->assertDatabaseCount('equipos', 1);
    }

    #[Test]
    public function el_numero_de_inventario_se_guarda_en_mayusculas(): void
    {
        $this->actingAs(Usuario::factory()->admin()->create())
            ->post(route('equipos.store'), $this->datosDeEquipo(['numero_inventario' => ' lab-7005 ']));

        $this->assertDatabaseHas('equipos', ['numero_inventario' => 'LAB-7005']);
    }

    #[Test]
    public function el_catalogo_se_puede_filtrar_por_estado(): void
    {
        Equipo::factory()->create(['numero_inventario' => 'LAB-8001', 'estado' => EstadoEquipo::Disponible]);
        Equipo::factory()->create(['numero_inventario' => 'LAB-8002', 'estado' => EstadoEquipo::EnReparacion]);

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('equipos.index', ['estado' => 'en_reparacion']))
            ->assertOk()
            ->assertSee('LAB-8002')
            ->assertDontSee('LAB-8001');
    }

    #[Test]
    public function el_catalogo_se_puede_buscar_por_marca(): void
    {
        Equipo::factory()->create(['numero_inventario' => 'LAB-8003', 'marca' => 'Rigol']);
        Equipo::factory()->create(['numero_inventario' => 'LAB-8004', 'marca' => 'Epson']);

        $this->actingAs(Usuario::factory()->admin()->create())
            ->get(route('equipos.index', ['q' => 'Rigol']))
            ->assertOk()
            ->assertSee('LAB-8003')
            ->assertDontSee('LAB-8004');
    }

    #[Test]
    public function un_equipo_con_historial_se_marca_de_baja_en_lugar_de_borrarse(): void
    {
        $equipo = Equipo::factory()->create(['numero_inventario' => 'LAB-8005']);

        Prestamo::factory()
            ->devuelto()
            ->create(['equipo_id' => $equipo->id]);

        $this->actingAs(Usuario::factory()->admin()->create())
            ->delete(route('equipos.destroy', $equipo))
            ->assertRedirect(route('equipos.index'));

        // El registro sigue existiendo, solo cambió de estado: así no se pierde
        // el historial de quién usó el equipo.
        $this->assertDatabaseHas('equipos', [
            'id' => $equipo->id,
            'estado' => EstadoEquipo::Baja->value,
        ]);
    }
}
