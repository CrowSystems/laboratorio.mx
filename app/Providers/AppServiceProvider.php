<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Prestamo;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    /**
     * Ajustes que aplican a toda la aplicación.
     */
    public function boot(): void
    {
        // Las fechas se manejan como CarbonImmutable: una vez creadas no se
        // pueden modificar. Evita el error clásico de que $fecha->addDay()
        // cambie la variable original sin que te des cuenta.
        Date::use(CarbonImmutable::class);

        // Para que "hace 3 días" salga en español en toda la aplicación.
        Carbon::setLocale(config('app.locale'));
        CarbonImmutable::setLocale(config('app.locale'));

        // La paginación se dibuja con Tailwind, que es lo que usa el proyecto.
        Paginator::useTailwind();

        // Red de seguridad para el desarrollo, apagada en producción:
        //
        // - preventLazyLoading: si una vista accede a una relación que el
        //   controlador no precargó, el programa falla en lugar de disparar
        //   una consulta extra por cada fila (el problema N+1). Es la forma más
        //   directa de aprender a usar ->with().
        // - preventSilentlyDiscardingAttributes: si intentas guardar un campo
        //   que no está en #[Fillable], te avisa en vez de ignorarlo en silencio.
        Model::preventLazyLoading(! $this->app->isProduction());
        Model::preventSilentlyDiscardingAttributes(! $this->app->isProduction());

        $this->compartirContadorDeVencidos();
    }

    /**
     * La barra lateral muestra una insignia roja con los préstamos vencidos.
     *
     * Ese número lo necesita el layout en TODAS las pantallas, así que un
     * "view composer" se lo entrega automáticamente cada vez que la vista se
     * dibuja. La alternativa sería que los seis controladores lo pasaran a
     * mano, y se nos olvidaría en el séptimo.
     */
    protected function compartirContadorDeVencidos(): void
    {
        View::composer('components.layouts.app', function ($vista): void {
            $vista->with(
                'vencidosPendientes',
                Auth::check() ? Prestamo::query()->vencidos()->count() : 0,
            );
        });
    }
}
