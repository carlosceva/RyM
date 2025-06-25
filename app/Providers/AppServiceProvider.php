<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Solicitud;
use App\Policies\SolicitudPolicy;
use Illuminate\Support\Facades\View;
use App\Observers\SolicitudObserver;
use App\Notifications\SolicitudCreada;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        Solicitud::class => SolicitudPolicy::class,
    ];
    
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        View::composer('partials.navbar', function ($view) {
          
            $tipos = [
                'Devolucion de Venta',
                'Anulacion de Venta',
                'Sobregiro de Venta',
                'precio_especial',
                'Muestra de Mercaderia',
                'Baja de Mercaderia'
            ];

            $tiposConRutas = [
                'precio_especial' => 'PrecioEspecial.index',
                'Devolucion de Venta' => 'Devolucion.index',
                'Anulacion de Venta' => 'Anulacion.index',
                'Sobregiro de Venta' => 'Sobregiro.index',
                'Muestra de Mercaderia' => 'Muestra.index',
                'Baja de Mercaderia' => 'Baja.index',
            ];

            $solicitudesPendientes = [];
            $solicitudesPorEjecutar = [];

            foreach ($tipos as $tipo) {
                $pendientes = Solicitud::where('tipo', $tipo)
                    ->where('estado', 'pendiente')
                    ->count();

                $porEjecutar = Solicitud::where('tipo', $tipo)
                    ->where('estado', 'aprobada')
                    ->whereDoesntHave('ejecucion')
                    ->count();

                $solicitudesPendientes[$tipo] = $pendientes;
                $solicitudesPorEjecutar[$tipo] = $porEjecutar;
            }

            $totalPendientes = array_sum($solicitudesPendientes);
            $totalPorEjecutar = array_sum($solicitudesPorEjecutar);

            $view->with(compact(
                'solicitudesPendientes',
                'solicitudesPorEjecutar',
                'totalPendientes',
                'totalPorEjecutar',
                'tiposConRutas',
            ));
        });

        Solicitud::observe(SolicitudObserver::class);
    }
}
