<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Solicitud;  // Asegúrate de importar el modelo correspondiente
use App\Policies\SolicitudPolicy;  // Asegúrate de tener la política creada si es necesario

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // Mapea tu modelo a la política correspondiente
        Solicitud::class => SolicitudPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Definir Gates (si usas gates directamente)
        // Gate::define('Precio_especial_ver', function ($user) {
        //     return $user->hasPermissionTo('Precio_especial_ver');
        // });

        Gate::define('ver-muestra', function ($user) {
            return $user->hasPermissionTo('Muestra_ver');
        });

        // Agrega más gates o permisos según sea necesario
    }
}
