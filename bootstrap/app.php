<?php

declare(strict_types=1);

use App\Http\Middleware\GarantizarRol;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias del middleware de roles, para poder escribir en las rutas
        // ->middleware('rol:admin') en lugar del nombre completo de la clase.
        $middleware->alias([
            'rol' => GarantizarRol::class,
        ]);

        // A dónde se manda a quien no ha iniciado sesión. Sin esta línea,
        // Laravel buscaría una ruta llamada 'login' y nuestra ruta se llama
        // 'sesion.create'.
        $middleware->redirectGuestsTo(fn () => route('sesion.create'));

        // A dónde se manda a quien YA inició sesión y vuelve a pedir el login.
        $middleware->redirectUsersTo(fn () => route('tablero'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
