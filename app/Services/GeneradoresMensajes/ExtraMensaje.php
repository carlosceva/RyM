<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class ExtraMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('extra.show', ['solicitud' => $solicitud->id]);

        return match ($etapa) {
            'crear' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Extras',
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
                    'Extras',
                    'Registracion',
                    $solicitud->id_visible,
                    $solicitud->fecha_autorizacion->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->autorizador->name,
                    $link
                ],
            ],
            'reprobar' => [
                'template' => 'enlace_solicitud_reprobada',
                'params' => [
                    'Extras',
                    $solicitud->id_visible,
                    $link
                ],
            ],
            'confirmar' => [
                'template' => 'enlace_extras_registrar',
                'params' => [
                    $solicitud->id_visible,
                    $solicitud->extra->fecha_confirmacion->format('d/m/Y H:i'),
                    $solicitud->extra->confirmador->name,
                    $link
                ],
            ],
            'ejecutar' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Extras',
                    $solicitud->id_visible,
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