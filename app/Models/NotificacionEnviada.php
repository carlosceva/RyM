<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionEnviada extends Model
{
    protected $fillable = ['solicitud_id', 'etapa', 'user_id'];

    protected $table = 'notificaciones_enviadas';
}
