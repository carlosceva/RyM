<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionLocal extends Model
{
    protected $fillable = ['user_id', 'solicitud_id', 'template', 'params', 'estado'];

    protected $table = 'notificaciones_locales';

    protected $casts = [
        'params' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class);
    }

    public function getMensajeAttribute()
    {
        switch ($this->template) {
            case 'solicitud_plantilla':
                return "Se ha {$this->params[0]} una solicitud de {$this->params[1]} y requiere {$this->params[2]}.\n" .
                       "Nro solicitud: {$this->params[3]} – Fecha: {$this->params[4]} – {$this->params[5]} Por: {$this->params[6]}";

            case 'solicitud_ejecutar':
                return "Su solicitud de {$this->params[0]} ha sido ejecutada.\n" .
                       "Nro solicitud: {$this->params[1]} – Fecha: {$this->params[2]} – Por: {$this->params[3]}";

            case 'solicitud_reprobar':
                return "Su solicitud de tipo {$this->params[0]} ha sido rechazada.\n" .
                       "Nro solicitud: {$this->params[1]}";

            case 'verificar_entrega':
                return "Se marco que no hay despacho registrado en la solicitud de {$this->params[0]}.\n" .
                        "Nro solicitud: {$this->params[1]} – Fecha: {$this->params[2]} \n" .
                        "Por favor, verifique la entrega";

            case 'verificar_entrega_fisica':
                return "Se marco que no hay entrega fisica en la solicitud de {$this->params[0]}.\n" .
                        "Nro solicitud: {$this->params[1]} – Fecha: {$this->params[2]} \n" .
                        "Puede continuar con la ejecucion";

            case 'sobregiro_confirmar':
                return "Se ha confirmado una solicitud de Sobregiro de Venta y esta esperando su ejecucion. \n" .
                        "Nro solicitud: {$this->params[0]} – Fecha: {$this->params[1]} – Por: {$this->params[2]} - COD: {$this->params[3]}";

            default:
                return "Nueva notificación";
        }
    }

    public function scopeLeidas($query)
    {
        return $query->where('estado', 'read');
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('estado', 'noread');
    }
}
