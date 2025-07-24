<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Solicitud;
use App\Policies\SolicitudPolicy;
use Illuminate\Support\Facades\View;
use App\Observers\SolicitudObserver;
use App\Notifications\SolicitudCreada;
use App\Models\User;
use App\Services\Contracts\WhatsAppServiceInterface;
use App\Services\TwilioWhatsAppService;
use App\Services\FakeWhatsAppService;
use App\Services\GupshupWhatsAppService;
use App\Models\NotificacionLocal;
use Illuminate\Support\Facades\Auth;

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
        $this->app->bind(WhatsAppServiceInterface::class, function () {
            if (app()->environment('production')) {
                return new TwilioWhatsAppService();
            }
            return new TwilioWhatsAppService();
        });
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
                'precio_especial',
                'Devolucion de Venta',
                'Anulacion de Venta',
                'Sobregiro de Venta',
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

                // Contar las solicitudes por ejecutar
                if ($tipo == 'Sobregiro de Venta') {
                    // Sobregiro de Venta tiene estado 'confirmada' y no debe estar ejecutada
                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'confirmada')
                        ->whereDoesntHave('ejecucion')
                        ->count();
                } elseif ($tipo == 'Devolucion de Venta' || $tipo == 'Anulacion de Venta') {
                    // Devolución y Anulación deben tener 'tiene_pago' no nulo y no estar ejecutadas
                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'aprobada') // Usamos 'pendiente' ya que es el estado inicial
                        ->whereHas($tipo == 'Devolucion de Venta' ? 'devolucion' : 'anulacion', function($query) {
                            $query->whereNotNull('tiene_pago'); // Aseguramos que 'tiene_pago' no sea null
                        })
                        ->whereDoesntHave('ejecucion')
                        ->count();
                } else {
                    // Para los demás tipos, por ejecutar se considera 'aprobada' y no ejecutada
                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'aprobada')
                        ->whereDoesntHave('ejecucion')
                        ->count();
                }

                $solicitudesPendientes[$tipo] = $pendientes;
                $solicitudesPorEjecutar[$tipo] = $porEjecutar;
            }

            $totalPendientes = array_sum($solicitudesPendientes);
            $totalPorEjecutar = array_sum($solicitudesPorEjecutar);

            $user = Auth::user();

            $notificacionesLocales = collect();
            if ($user) {
                $notificacionesLocales = NotificacionLocal::where('user_id', $user->id)
                    ->where('estado', 'noread')
                    ->latest()
                    ->take(5)
                    ->get();
            }

            $view->with(compact(
                'solicitudesPendientes',
                'solicitudesPorEjecutar',
                'totalPendientes',
                'totalPorEjecutar',
                'tiposConRutas',
                'notificacionesLocales',
            ));
        });

        Solicitud::observe(SolicitudObserver::class);
    }
}
