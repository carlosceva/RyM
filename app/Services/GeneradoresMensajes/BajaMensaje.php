<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class BajaMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        return match ($etapa) {
            'crear' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'creado',
                    'Ajuste de Inventario',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                ],
            ],
            'confirmar' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'confirmado',
                    'Ajuste de Inventario',
                    'aprobación',
                    $solicitud->id,
                    $solicitud->bajaMercaderia->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->bajaMercaderia->autorizador->name,
                ],
            ],
            'aprobar' => [
                'template' => 'solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Ajuste de Inventario',
                    'ejecución',
                    $solicitud->id,
                    $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->autorizador->name,
                ],
            ],
            'ejecutar' => [
                'template' => 'solicitud_ejecutar',
                'params' => [
                    'Ajuste de Inventario',
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