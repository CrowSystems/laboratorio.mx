<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\GuardarAlumnoRequest;
use App\Models\Alumno;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Padrón de alumnos del laboratorio.
 */
class AlumnoController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Alumno::class);

        $busqueda = $request->string('q')->trim()->toString();
        $soloAdeudos = $request->boolean('adeudos');

        $alumnos = Alumno::query()
            ->buscar($busqueda)
            ->when($soloAdeudos, fn ($query) => $query->conAdeudo())
            // Los dos contadores los calcula la base de datos. Sin esto, la
            // vista tendría que preguntar por cada alumno: el problema N+1.
            ->withCount([
                'prestamosAbiertos as abiertos_count',
                'prestamosVencidos as vencidos_count',
            ])
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('alumnos.index', [
            'alumnos' => $alumnos,
            'busqueda' => $busqueda,
            'soloAdeudos' => $soloAdeudos,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Alumno::class);

        return view('alumnos.create');
    }

    public function store(GuardarAlumnoRequest $request): RedirectResponse
    {
        $alumno = Alumno::create($request->validated());

        return to_route('alumnos.show', $alumno)
            ->with('exito', "Se registró a {$alumno->nombre} con matrícula {$alumno->matricula}.");
    }

    public function show(Alumno $alumno): View
    {
        $this->authorize('view', $alumno);

        return view('alumnos.show', [
            'alumno' => $alumno,
            'historial' => $alumno->prestamos()
                ->conRelaciones()
                ->paginate(8),
        ]);
    }

    public function edit(Alumno $alumno): View
    {
        $this->authorize('update', $alumno);

        return view('alumnos.edit', ['alumno' => $alumno]);
    }

    public function update(GuardarAlumnoRequest $request, Alumno $alumno): RedirectResponse
    {
        $alumno->update($request->validated());

        return to_route('alumnos.show', $alumno)
            ->with('exito', "Se actualizaron los datos de {$alumno->nombre}.");
    }

    public function destroy(Alumno $alumno): RedirectResponse
    {
        $this->authorize('delete', $alumno);

        // AlumnoPolicy::delete() ya negó el permiso si el alumno tiene
        // historial, así que aquí solo queda borrar.
        $nombre = $alumno->nombre;
        $alumno->delete();

        return to_route('alumnos.index')
            ->with('exito', "Se eliminó el registro de {$nombre}.");
    }
}
