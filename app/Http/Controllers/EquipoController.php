<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\EstadoEquipo;
use App\Http\Requests\GuardarEquipoRequest;
use App\Models\Equipo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Catálogo de equipo. Es un controlador de recurso (resource): sus siete
 * métodos corresponden a las siete rutas que genera Route::resource().
 *
 * Cada método empieza pidiendo permiso con $this->authorize(), que consulta
 * EquipoPolicy. Es una línea más por método, pero deja a la vista qué permiso
 * exige cada acción: nada queda escondido en el constructor.
 */
class EquipoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Equipo::class);

        // El filtro de estado llega por la URL (?estado=prestado). Se convierte
        // al enum con tryFrom(), que devuelve null si el valor es basura, en
        // lugar de reventar como lo haría from().
        $estado = EstadoEquipo::tryFrom((string) $request->query('estado'));
        $busqueda = $request->string('q')->trim()->toString();

        $equipos = Equipo::query()
            ->buscar($busqueda)
            ->when($estado, fn ($query) => $query->porEstado($estado))
            ->with('prestamoVigente.alumno')
            ->porInventario()
            ->paginate(12)
            // withQueryString() conserva ?estado= y ?q= en los enlaces de la
            // paginación. Sin esto, al pasar a la página 2 se pierde el filtro.
            ->withQueryString();

        return view('equipos.index', [
            'equipos' => $equipos,
            'estadoActivo' => $estado,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Equipo::class);

        return view('equipos.create', [
            'estados' => EstadoEquipo::paraSelect(),
        ]);
    }

    public function store(GuardarEquipoRequest $request): RedirectResponse
    {
        // validated() entrega solo los campos que pasaron por rules(). Nunca
        // se usa $request->all() para crear un modelo: así nadie puede mandar
        // un campo extra y modificar algo que no debía.
        $equipo = Equipo::create($request->validated());

        return to_route('equipos.show', $equipo)
            ->with('exito', "Se registró el equipo {$equipo->numero_inventario}.");
    }

    /**
     * Route model binding: Laravel recibe {equipo} de la URL, busca el registro
     * y lo inyecta ya hidratado. Si no existe, responde 404 por su cuenta.
     */
    public function show(Equipo $equipo): View
    {
        $this->authorize('view', $equipo);

        return view('equipos.show', [
            'equipo' => $equipo,
            'historial' => $equipo->prestamos()
                ->conRelaciones()
                ->latest('fecha_salida')
                ->paginate(8),
        ]);
    }

    public function edit(Equipo $equipo): View
    {
        $this->authorize('update', $equipo);

        return view('equipos.edit', [
            'equipo' => $equipo,
            'estados' => EstadoEquipo::paraSelect(),
        ]);
    }

    public function update(GuardarEquipoRequest $request, Equipo $equipo): RedirectResponse
    {
        $equipo->update($request->validated());

        return to_route('equipos.show', $equipo)
            ->with('exito', "Se actualizó el equipo {$equipo->numero_inventario}.");
    }

    public function destroy(Equipo $equipo): RedirectResponse
    {
        $this->authorize('delete', $equipo);

        // Un equipo con historial no se borra: se marca de baja para conservar
        // el registro de quién lo usó. Borrarlo rompería la llave foránea.
        if ($equipo->prestamos()->exists()) {
            $equipo->update(['estado' => EstadoEquipo::Baja]);

            return to_route('equipos.index')
                ->with('aviso', "El equipo {$equipo->numero_inventario} tiene historial de préstamos, así que se marcó como baja en lugar de borrarse.");
        }

        $inventario = $equipo->numero_inventario;
        $equipo->delete();

        return to_route('equipos.index')
            ->with('exito', "Se eliminó el equipo {$inventario}.");
    }
}
