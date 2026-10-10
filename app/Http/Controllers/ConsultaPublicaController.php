<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ConsultarAdeudosRequest;
use App\Models\Alumno;
use Illuminate\View\View;

/**
 * Consulta pública por matrícula, pensada para el celular del alumno.
 *
 * No pide login: el alumno escribe su matrícula y ve qué debe. Por eso la
 * respuesta es deliberadamente escasa: nombre, carrera y sus propios préstamos
 * abiertos. Nada de correos ni de datos de otros alumnos.
 */
class ConsultaPublicaController extends Controller
{
    /**
     * Pantalla con la caja para escribir la matrícula.
     */
    public function index(): View
    {
        return view('consulta.index');
    }

    /**
     * Resultado de la consulta.
     */
    public function buscar(ConsultarAdeudosRequest $request): View
    {
        $alumno = Alumno::query()
            ->where('matricula', $request->matricula())
            // Se precargan los préstamos abiertos con su equipo: una consulta
            // para los préstamos y una para los equipos, no una por fila.
            ->with(['prestamosAbiertos.equipo'])
            ->first();

        return view('consulta.resultado', [
            'matricula' => $request->matricula(),
            'alumno' => $alumno,
            // Si el alumno no existe, se manda una colección vacía para que la
            // vista no tenga que preguntar por null en dos lugares.
            'pendientes' => $alumno?->prestamosAbiertos->sortBy('fecha_compromiso') ?? collect(),
        ]);
    }
}
