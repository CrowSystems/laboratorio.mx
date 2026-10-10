<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Prestamo;
use Illuminate\View\View;

/**
 * Listado de préstamos vencidos, el más atrasado primero.
 *
 * Los días de atraso NO se calculan en la vista: cada préstamo los expone en
 * su atributo $prestamo->dias_atraso (ver App\Models\Prestamo). La vista solo
 * los imprime.
 */
class PrestamoVencidoController extends Controller
{
    public function __invoke(): View
    {
        $this->authorize('viewAny', Prestamo::class);

        $vencidos = Prestamo::query()
            ->vencidos()
            ->conRelaciones()
            ->orderBy('fecha_compromiso')
            ->paginate(15);

        return view('vencidos.index', [
            'vencidos' => $vencidos,
            // Para el encabezado: cuántos alumnos distintos traen adeudo.
            'alumnosConAdeudo' => Prestamo::query()
                ->vencidos()
                ->distinct()
                ->count('alumno_id'),
        ]);
    }
}
