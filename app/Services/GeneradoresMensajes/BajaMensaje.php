<?php

namespace App\Services\GeneradoresMensajes;

use App\Models\Solicitud;

class BajaMensaje
{
    public static function generar(Solicitud $solicitud, string $etapa): array
    {
        $link = route('baja.show', ['solicitud' => $solicitud->id]);
        
        return match ($etapa) {
            'crear' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'creado',
                    'Ajuste de Inventario',
                    'aprobación',
                    $solicitud->id_visible,
                    $solicitud->fecha_solicitud->format('d/m/Y H:i'),
                    'Solicitado',
                    $solicitud->usuario->name,
                    $link
                ],
            ],
            'confirmar' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'confirmado',
                    'Ajuste de Inventario',
                    'aprobación',
                    $solicitud->id_visible,
                    \Carbon\Carbon::parse($solicitud->bajaMercaderia->fecha_autorizacion)->format('d/m/Y H:i'),
                    'Autorizado',
                    $solicitud->bajaMercaderia->autorizador->name,
                    $link
                ],
            ],
            'aprobar' => [
                'template' => 'nueva_solicitud_plantilla',
                'params' => [
                    'aprobado',
                    'Ajuste de Inventario',
                    'ejecución',
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
                    'Ajuste de Inventario',
                    $solicitud->id_visible,
                    $link
                ],
            ],
            'ejecutar' => [
                'template' => 'enlace_solicitud_ejecutar',
                'params' => [
                    'Ajuste de Inventario',
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