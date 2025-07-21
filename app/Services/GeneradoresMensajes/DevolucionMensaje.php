<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class DevolucionMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        return match ($etapa) {
            'crear' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'creado',
                    'Devolución',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                ],
            ],
            'crear_anulacion' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'creado',
                    'Anulación',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                ],
            ],
            'aprobar' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Devolución',
                    'confirmación',
                    $solicitud->id,
                    $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->autorizador->name,
                ],
            ],
            'ejecutar_anulacion' => [
                'template' => 'solicitud_ejecutar',
                'params' => [
                    'Anulación',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                ],
            ],
            'ejecutar_devolucion' => [
                'template' => 'solicitud_ejecutar',
                'params' => [
                    'Devolución',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                ],
            ],
            'verificar_entrega' => [
                'template' => 'verificar_entrega',
                'params' => [
                    'Devolución',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                ],
            ],
            'verificar_entrega_fisica' => [
                'template' => 'verificar_entrega_fisica',
                'params' => [
                    'Devolución',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                ],
            ],
            default => [
                'template' => 'mensaje_generico',
                'params' => [$solicitud->id, $etapa],
            ],
        };

    }
}