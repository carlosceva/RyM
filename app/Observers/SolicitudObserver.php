<?php

namespace App\Observers;

use App\Models\Solicitud;
use App\Models\User;
use App\Notifications\SolicitudCreada;
use Illuminate\Support\Facades\Log;

class SolicitudObserver
{
    /**
     * Handle the Solicitud "created" event.
     */
    public function created(Solicitud $solicitud): void
    {
        \Log::info('Observer creado ejecutado', ['id' => $solicitud->id]);

        $mapaTiposPermisos = [
            'precio_especial' => 'Precio_especial',
            'Anulacion de Venta' => 'Anulacion',
            'Devolucion de Venta' => 'Devolucion',
            'Baja de Mercaderia' => 'Baja',
            'Muestra de Mercaderia' => 'Muestra',
            'Sobregiro de Venta' => 'Sobregiro',
        ];

        $tipo = $solicitud->tipo;
        $permisoBase = $mapaTiposPermisos[$tipo] ?? null;

        if (!$permisoBase) {
            \Log::warning('Tipo de solicitud no mapeado', ['tipo' => $tipo]);
            return;
        }

        $permiso = "{$permisoBase}_aprobar";

        $usuarios = \App\Models\User::permission($permiso)->get();

        \Log::info("Usuarios con permiso {$permiso}", ['count' => $usuarios->count()]);

        foreach ($usuarios as $user) {
            \Log::info('Notificando a usuario', ['id' => $user->id, 'name' => $user->name]);
            $user->notify(new \App\Notifications\SolicitudCreada($solicitud));
        }
    }

    /**
     * Handle the Solicitud "updated" event.
     */
    public function updated(Solicitud $solicitud): void
    {
        //
    }

    /**
     * Handle the Solicitud "deleted" event.
     */
    public function deleted(Solicitud $solicitud): void
    {
        //
    }

    /**
     * Handle the Solicitud "restored" event.
     */
    public function restored(Solicitud $solicitud): void
    {
        //
    }

    /**
     * Handle the Solicitud "force deleted" event.
     */
    public function forceDeleted(Solicitud $solicitud): void
    {
        //
    }
}
