<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\RegistrarPrestamoRequest;
use App\Models\Alumno;
use App\Models\Equipo;
use App\Models\Prestamo;
use App\Services\ServicioDePrestamos;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Registro de la salida de equipo.
 */
class PrestamoController extends Controller
{
    public function __construct(
        protected ServicioDePrestamos $servicio,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Prestamo::class);

        // ?filtro=abiertos | devueltos | todos
        $filtro = $request->string('filtro')->toString() ?: 'abiertos';

        $prestamos = Prestamo::query()
            ->conRelaciones()
            ->when($filtro === 'abiertos', fn ($query) => $query->abiertos())
            ->when($filtro === 'devueltos', fn ($query) => $query->devueltos())
            ->latest('fecha_salida')
            ->paginate(12)
            ->withQueryString();

        return view('prestamos.index', [
            'prestamos' => $prestamos,
            'filtro' => $filtro,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Prestamo::class);

        return view('prestamos.create', [
            // Solo se ofrece lo que se puede prestar. Esto es comodidad para
            // el auxiliar; la validación de verdad está en el Form Request.
            'equipos' => Equipo::query()->disponibles()->porInventario()->get(),
            'alumnos' => Alumno::query()->orderBy('nombre')->get(),
            'fechaSugerida' => now()->addWeekdays(3)->toDateString(),
        ]);
    }

    public function store(RegistrarPrestamoRequest $request): RedirectResponse
    {
        // El Form Request ya validó las tres reglas de negocio y la Policy.
        // Al controlador solo le queda coordinar: buscar los modelos y pedirle
        // al servicio que haga la operación.
        $equipo = Equipo::findOrFail($request->integer('equipo_id'));
        $alumno = Alumno::findOrFail($request->integer('alumno_id'));

        $prestamo = $this->servicio->prestar(
            equipo: $equipo,
            alumno: $alumno,
            autoriza: $request->user(),
            fechaCompromiso: $request->date('fecha_compromiso'),
            notas: $request->input('notas'),
        );

        return to_route('prestamos.show', $prestamo)->with(
            'exito',
            "Se entregó el equipo {$equipo->numero_inventario} a {$alumno->nombre}."
        );
    }

    public function show(Prestamo $prestamo): View
    {
        $this->authorize('view', $prestamo);

        // load() precarga las relaciones sobre un modelo que ya tenemos, para
        // que la vista no dispare consultas sueltas.
        $prestamo->load(['equipo', 'alumno', 'autorizadoPor']);

        return view('prestamos.show', ['prestamo' => $prestamo]);
    }
}
