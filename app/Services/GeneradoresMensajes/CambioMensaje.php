<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class CambioMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('cambio.show', ['solicitud' => $solicitud->id]);

        return match ($etapa) {
            'crear' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Cambio Fisico de Mercaderia',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                    $link
                ],
            ],
            'aprobar' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Cambio Fisico de Mercaderia',
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
                    'Cambio Fisico de Mercaderia',
                    $solicitud->id,
                    $link
                ],
            ],
            'ejecutar' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Cambio Fisico de Mercaderia',
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