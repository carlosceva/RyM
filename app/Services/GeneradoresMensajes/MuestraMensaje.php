<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class MuestraMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        return match ($etapa) {
            'crear' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'creado',
                    'Muestra de Mercaderia',
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
                    'Muestra de Mercaderia',
                    'ejecución',
                    $solicitud->id,
                    $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->autorizador->name,
                ],
            ],
            'reprobar' => [
                'template' => 'solicitud_reprobada',
                'params' => [
                    'Muestra de Mercaderia',
                    $solicitud->id,
                ],
            ],
            'ejecutar' => [
                'template' => 'solicitud_ejecutar',
                'params' => [
                    'Muestra de Mercaderia',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                ],
            ],
            default => [
                'template' => 'mensaje_generico',
                'params' => [$solicitud->id, $etapa],
            ],
        };
    }
}