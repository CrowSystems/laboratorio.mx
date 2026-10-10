<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ResumenDelTablero;
use Illuminate\View\View;

/**
 * El tablero que ven los encargados al entrar.
 *
 * Fíjate en lo corto que es: no hay una sola consulta aquí. El controlador pide
 * los datos a ResumenDelTablero y los pasa a la vista. Si mañana hay que
 * cambiar cómo se cuentan los equipos, este archivo no se toca.
 */
class TableroController extends Controller
{
    public function __construct(
        protected ResumenDelTablero $resumen,
    ) {}

    public function __invoke(): View
    {
        return view('tablero', [
            'conteos' => $this->resumen->conteosDeEquipo(),
            'vencidos' => $this->resumen->prestamosVencidos(),
            'abiertos' => $this->resumen->prestamosAbiertos(),
            'masSolicitados' => $this->resumen->equiposMasSolicitados(),
            'vencidosRecientes' => $this->resumen->vencidosMasAntiguos(),
            'movimientos' => $this->resumen->ultimosMovimientos(),
        ]);
    }
}
