<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\View;

use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    protected $tiposConRutas = [
        'precio_especial' => 'PrecioEspecial.index',
        'Devolucion de Venta' => 'Devolucion.index',
        'Anulacion de Venta' => 'Anulacion.index',
        'Sobregiro de Venta' => 'Sobregiro.index',
        'Muestra de Mercaderia' => 'Muestra.index',
        'Baja de Mercaderia' => 'Baja.index',
    ];

    public function marcarComoLeidas()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    public function ver($id)
    {
        $notificacion = auth()->user()->notifications()->findOrFail($id);
        $notificacion->markAsRead();

        $data = $notificacion->data;

        $tipo = $data['tipo'] ?? null;
        $solicitudId = $data['solicitud_id'] ?? null;

        if ($tipo && $solicitudId && isset($this->tiposConRutas[$tipo])) {
            $ruta = $this->tiposConRutas[$tipo];
            return redirect()->route($ruta, ['filtro_id' => $solicitudId]);
        }

        // Si algo falla, vuelve atrás
        return redirect()->back()->with('error', 'No se pudo redirigir correctamente la notificación.');
    }

    public function obtenerNotificacionesLeidas()
    {
        // Recuperamos las notificaciones leídas
        $notificacionesLeidas = auth()->user()->readNotifications()
            ->where('type', \App\Notifications\SolicitudCreada::class) // Puedes adaptar este filtro según el tipo de notificación
            ->take(10) // Limita la cantidad de notificaciones
            ->get();

        // Formateamos las notificaciones para enviarlas en la respuesta JSON
        $notificacionesLeidasData = $notificacionesLeidas->map(function($notificacion) {
            return [
                'mensaje' => $notificacion->data['mensaje'],
                'link' => route('notificacion.ver', $notificacion->id),  // Enlace para ver la notificación
            ];
        });

        // Devolvemos las notificaciones leídas como JSON
        return response()->json(['notifications' => $notificacionesLeidasData]);
    }

}
