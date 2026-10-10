<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Clase base de todos los controladores.
 *
 * Desde Laravel 11 viene vacía a propósito. Le agregamos el trait
 * AuthorizesRequests porque es el que aporta los métodos $this->authorize() y
 * $this->authorizeResource(), con los que los controladores consultan las
 * Policies de app/Policies.
 */
abstract class Controller
{
    use AuthorizesRequests;
}
