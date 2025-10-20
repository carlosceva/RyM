<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class MuestraMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('muestra.show', ['solicitud' => $solicitud->id]);

        return match ($etapa) {
            'crear' => [
                'template' => 'enlace_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Muestra de Mercaderia',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                    $link
                ],
            ],
            'aprobar' => [
                'template' => 'enlace_solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Muestra de Mercaderia',
                    'ejecución',
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
                    'Muestra de Mercaderia',
                    $solicitud->id,
                    $link
                ],
            ],
            'ejecutar' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Muestra de Mercaderia',
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
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