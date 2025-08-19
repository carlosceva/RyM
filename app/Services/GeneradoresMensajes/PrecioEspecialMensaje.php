<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class PrecioEspecialMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        return match ($etapa) {
            'crear' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'creado',
                    'Precio Especial',
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
                    'Precio Especial',
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
                    'Precio Especial',
                    $solicitud->id,
                ],
            ],
            'confirmar' => [
                'template' => 'solicitud_confirmar_venta',
                'params' => [
                    $solicitud->id,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                ],
            ],
            'ejecutar' => [
                'template' => 'solicitud_ejecutar',
                'params' => [
                    'Precio Especial',
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
