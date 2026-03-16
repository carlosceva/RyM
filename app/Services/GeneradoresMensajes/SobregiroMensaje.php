<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class SobregiroMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('sobregiro.show', ['solicitud' => $solicitud->id]);

        return match ($etapa) {
            'crear' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Sobregiro de Venta',
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
                    'Sobregiro de Venta',
                    'confirmación',
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
                    'Sobregiro de Venta',
                    $solicitud->id_visible,
                    $link
                ],
            ],
            'confirmar' => [
                'template' => 'enlace_sobregiro_confirmar',
                'params' => [
                    $solicitud->id_visible,
                    $solicitud->sobregiro->fecha_confirmacion->format('d/m/Y H:i'),
                    $solicitud->sobregiro->confirmador->name,
                    $solicitud->sobregiro->cod_sobregiro,
                    $link
                ],
            ],
            'ejecutar' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Sobregiro de Venta',
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