<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Clase base de todas las pruebas.
 *
 * RefreshDatabase está aquí, y no en cada archivo de prueba, para que ninguna
 * prueba se olvide de incluirlo. Lo que hace: corre las migraciones en una base
 * SQLite en memoria (ver phpunit.xml) y envuelve cada prueba en una transacción
 * que se deshace al terminar. Resultado: cada prueba arranca con la base vacía
 * y el orden en que corren no importa.
 *
 * Importante: las pruebas NUNCA tocan la base de desarrollo. Todo pasa en
 * memoria y desaparece al terminar.
 */
abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;
}
