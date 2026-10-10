<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\RolUsuario;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Deja pasar solo a los usuarios con uno de los roles indicados.
 *
 * Se usa en las rutas así:
 *   Route::delete('equipos/{equipo}', ...)->middleware('rol:admin');
 *
 * ¿Middleware o Policy? Las dos cosas, y no es redundancia:
 *   - El middleware corta grupos completos de rutas por rol. Es una reja en la
 *     puerta: barato y fácil de leer en routes/web.php.
 *   - La Policy decide caso por caso, mirando el registro concreto (por ejemplo,
 *     "no borres un equipo que está prestado"). Es la revisión del expediente.
 * El alias 'rol' se registra en bootstrap/app.php.
 */
class GarantizarRol
{
    /**
     * @param  Closure(Request): Response  $next
     * @param  string  ...$roles  valores del enum RolUsuario, p. ej. 'admin'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if ($usuario === null) {
            abort(401);
        }

        $permitidos = array_map(
            fn (string $rol): RolUsuario => RolUsuario::from($rol),
            $roles,
        );

        abort_unless(
            in_array($usuario->rol, $permitidos, strict: true),
            403,
            'Tu rol de '.$usuario->rol->etiqueta().' no tiene acceso a esta sección.',
        );

        return $next($request);
    }
}
