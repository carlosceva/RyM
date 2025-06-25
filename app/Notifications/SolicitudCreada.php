<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Solicitud;

class SolicitudCreada extends Notification
{

    protected $solicitud;

    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    public function via($notifiable)
    {
        return ['database']; // Se guarda en base de datos
    }

    public function toDatabase($notifiable)
    {
        return [
            'mensaje' => 'Solicitud creada, ' . $this->solicitud->tipo .
                        ' #' . $this->solicitud->id . ', ' .
                        $this->solicitud->created_at->format('d/m/y - H:i'),
            'tipo' => $this->solicitud->tipo,
            'solicitud_id' => $this->solicitud->id,
        ];
    }

}
