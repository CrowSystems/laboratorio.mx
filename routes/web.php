<?php

declare(strict_types=1);

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\ConsultaPublicaController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\PrestamoController;
use App\Http\Controllers\PrestamoVencidoController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\TableroController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas del sistema de préstamo de equipo
|--------------------------------------------------------------------------
|
| Están ordenadas en tres bloques, de menos a más privilegio:
|   1. Público: la consulta por matrícula y el formulario de acceso.
|   2. Autenticado: préstamos, devoluciones, vencidos y el tablero.
|      Cualquiera de los dos roles entra.
|   3. Solo admin: el alta, la edición y la baja del catálogo y del padrón.
|
| Todas las rutas llevan nombre (->name(...)) para que las vistas nunca
| escriban una URL a mano: route('equipos.index') sigue funcionando aunque
| mañana cambiemos '/equipos' por '/inventario'.
|
*/

// ---------------------------------------------------------------------------
// 1. Público — sin sesión. Es lo que el alumno abre en su celular.
// ---------------------------------------------------------------------------
Route::get('/', [ConsultaPublicaController::class, 'index'])->name('consulta.index');
Route::post('/consulta', [ConsultaPublicaController::class, 'buscar'])->name('consulta.buscar');

// Acceso para el personal del laboratorio.
Route::middleware('guest')->group(function (): void {
    Route::get('/entrar', [SesionController::class, 'create'])->name('sesion.create');
    Route::post('/entrar', [SesionController::class, 'store'])->name('sesion.store');
});

Route::post('/salir', [SesionController::class, 'destroy'])
    ->middleware('auth')
    ->name('sesion.destroy');

// ---------------------------------------------------------------------------
// 2. Autenticado — los dos roles. Es la operación diaria del mostrador.
// ---------------------------------------------------------------------------
Route::middleware('auth')->group(function (): void {
    Route::get('/tablero', TableroController::class)->name('tablero');

    Route::get('/vencidos', PrestamoVencidoController::class)->name('vencidos.index');

    // Préstamos: solo index, create, store y show. No hay edit ni destroy,
    // porque un préstamo no se corrige ni se borra: se devuelve.
    Route::resource('prestamos', PrestamoController::class)
        ->only(['index', 'create', 'store', 'show'])
        ->parameters(['prestamos' => 'prestamo']);

    // La devolución es una acción anidada bajo el préstamo que cierra.
    Route::prefix('prestamos/{prestamo}')->name('devoluciones.')->group(function (): void {
        Route::get('/devolver', [DevolucionController::class, 'create'])->name('create');
        Route::post('/devolver', [DevolucionController::class, 'store'])->name('store');
    });

    // -----------------------------------------------------------------------
    // 3. Catálogo y padrón: se consultan con cualquiera de los dos roles,
    //    pero solo el encargado puede escribir.
    //
    //    middlewareFor() aplica 'rol:admin' únicamente a las cinco acciones de
    //    escritura, dejando index y show abiertos al auxiliar. Así el recurso
    //    se declara UNA vez y no hay riesgo de que /equipos/create se confunda
    //    con /equipos/{equipo} por el orden en que se registraron las rutas.
    //
    //    El middleware corta el acceso de un golpe; las Policies (EquipoPolicy,
    //    AlumnoPolicy) lo vuelven a verificar registro por registro. Son dos
    //    candados independientes, no uno repetido.
    // -----------------------------------------------------------------------
    $accionesDeEscritura = ['create', 'store', 'edit', 'update', 'destroy'];

    Route::resource('equipos', EquipoController::class)
        ->middlewareFor($accionesDeEscritura, 'rol:admin');

    Route::resource('alumnos', AlumnoController::class)
        ->middlewareFor($accionesDeEscritura, 'rol:admin');
});
