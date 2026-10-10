<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CondicionDevolucion;
use App\Http\Requests\RegistrarDevolucionRequest;
use App\Models\Prestamo;
use App\Services\ServicioDePrestamos;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Recepción del equipo que regresa.
 *
 * Está en su propio controlador, y no como un método más de PrestamoController,
 * porque devolver es una acción distinta de crear o editar un préstamo: tiene
 * su propio formulario, su propia validación y su propio permiso.
 */
class DevolucionController extends Controller
{
    public function __construct(
        protected ServicioDePrestamos $servicio,
    ) {}

    public function create(Prestamo $prestamo): View
    {
        $this->authorize('devolver', $prestamo);

        $prestamo->load(['equipo', 'alumno']);

        return view('devoluciones.create', [
            'prestamo' => $prestamo,
            'condiciones' => CondicionDevolucion::paraSelect(),
        ]);
    }

    public function store(RegistrarDevolucionRequest $request, Prestamo $prestamo): RedirectResponse
    {
        $condicion = $request->condicion();

        // Se precarga el equipo porque el mensaje de éxito usa su número de
        // inventario, y el proyecto tiene prohibida la carga diferida.
        $prestamo->load('equipo');

        $this->servicio->devolver(
            prestamo: $prestamo,
            condicion: $condicion,
            notas: $request->input('notas'),
        );

        $destino = $condicion->estadoResultante();

        return to_route('prestamos.show', $prestamo)->with(
            'exito',
            "Se recibió el equipo {$prestamo->equipo->numero_inventario}. Quedó en estado \"{$destino->etiqueta()}\"."
        );
    }
}
