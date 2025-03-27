<?php

namespace App\Policies;

use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class SolicitudPolicy
{
    use HandlesAuthorization;

    public function approveSolicitud(User $user, Solicitud $solicitud)
    {
        return $user->hasRole('Administrador');
    }
}
