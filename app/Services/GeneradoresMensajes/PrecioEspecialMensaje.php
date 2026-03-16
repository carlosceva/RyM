<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class PrecioEspecialMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('PrecioEspecial.show', ['solicitud' => $solicitud->id]);

        return match ($etapa) {
            'crear' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Precio Especial',
                    'aprobación',
                    $solicitud->id_visible,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                    $link,
                ],
            ],
            'aprobar' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Precio Especial',
                    'ejecución',
                    $solicitud->id_visible,
                    $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->autorizador->name,
                    $link,
                ],
            ],
            'reprobar' => [
                'template' => 'enlace_solicitud_reprobada',
                'params' => [
                    'Precio Especial',
                    $solicitud->id_visible,
                    $link,
                ],
            ],
            'confirmar' => [
                'template' => 'confirmar_solicitud',
                'params' => [
                    'Precio Especial',
                    $solicitud->id_visible,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                    $link,
                ],
            ],
            'ejecutar' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Precio Especial',
                    $solicitud->id_visible,
                    now()->format('d/m/Y H:i'),
                    auth()->user()->name,
                    $link,
                ],
            ],
            default => [
                'template' => 'mensaje_generico',
                'params' => [$solicitud->id, $etapa],
            ],
        };
    }
}
