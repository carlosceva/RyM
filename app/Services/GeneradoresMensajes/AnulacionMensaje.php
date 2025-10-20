<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class AnulacionMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('anulacion.show', ['solicitud' => $solicitud->id]);

        return match ($etapa) {
            'crear' => [
                'template' => 'enlace_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Anulación',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                    $link
                ],
            ],
            'crear_devolucion' => [
                'template' => 'enlace_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Devolución',
                    'aprobación',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                    $link
                ],
            ],
            'aprobar' => [
                'template' => 'enlace_solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Anulación',
                    'confirmación',
                    $solicitud->id,
                    $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->autorizador->name,
                    $link
                ],
            ],
            'reprobar' => [
                'template' => 'enlace_solicitud_reprobada',
                'params' => [
                    'Anulacion de Venta',
                    $solicitud->id,
                    $link
                ],
            ],
            'ejecutar_anulacion' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Anulación',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                    $link
                ],
            ],
            'ejecutar_devolucion' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Devolucion',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                    $link
                ],
            ],
            'verificar_entrega' => [
                'template' => 'enlace_verificar_entrega',
                'params' => [
                    'Anulación',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    $link
                ],
            ],
            'verificar_entrega_fisica' => [
                'template' => 'enlace_verificar_entrega_fisica',
                'params' => [
                    'Anulación',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    $link
                ],
            ],
            default => [
                'template' => 'mensaje_generico',
                'params' => [$solicitud->id, $etapa],
            ],
        };
    }
}
