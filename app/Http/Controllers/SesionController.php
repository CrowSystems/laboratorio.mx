<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\IniciarSesionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Entrar y salir del sistema.
 *
 * El trabajo de autenticar está en IniciarSesionRequest::autenticar(), que es
 * donde viven la validación, el límite de intentos y la regeneración de la
 * sesión. Aquí solo se decide a dónde llevar al usuario después.
 */
class SesionController extends Controller
{
    public function create(): View
    {
        return view('sesion.create');
    }

    public function store(IniciarSesionRequest $request): RedirectResponse
    {
        $request->autenticar();

        // intended() regresa al usuario a la página que quería ver antes de que
        // el middleware 'auth' lo mandara al login.
        return redirect()->intended(route('tablero'))->with(
            'exito', '¡Bienvenido, '.$request->user()->nombre.'!'
        );
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        // Estos dos pasos son obligatorios al cerrar sesión: invalidar borra los
        // datos de la sesión anterior y regenerar el token evita que un
        // formulario viejo siga siendo válido.
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('consulta.index')->with('exito', 'Cerraste sesión correctamente.');
    }
}
