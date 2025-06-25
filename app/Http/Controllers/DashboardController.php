<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
// Definir los tipos de solicitud y sus rutas
        $tiposConRutas = [
            'precio_especial' => 'PrecioEspecial.index',
            'Devolucion de Venta' => 'Devolucion.index',
            'Anulacion de Venta' => 'Anulacion.index',
            'Sobregiro de Venta' => 'Sobregiro.index',
            'Muestra de Mercaderia' => 'Muestra.index',
            'Baja de Mercaderia' => 'Baja.index',
        ];

        // Mapa de íconos HTML por tipo
        $iconos = [
            'precio_especial' => '<i class="far fa-file-alt mr-2"></i>',
            'Devolucion de Venta' => '<i class="fas fa-undo mr-2"></i>',
            'Anulacion de Venta' => '<i class="far fa-times-circle mr-2"></i>',
            'Sobregiro de Venta' => '<i class="far fa-arrow-alt-circle-up mr-2"></i>',
            'Muestra de Mercaderia' => '<i class="far fa-file-alt mr-2"></i>',
            'Baja de Mercaderia' => '<i class="far fa-trash-alt mr-2"></i>',
        ];

        $tarjetas = [];

        foreach ($tiposConRutas as $tipo => $ruta) {
            $pendientes = Solicitud::where('tipo', $tipo)
                ->where('estado', 'pendiente')
                ->count();

            $porEjecutar = Solicitud::where('tipo', $tipo)
                ->where('estado', 'aprobada')
                ->whereDoesntHave('ejecucion')
                ->count();

            $tarjetas[] = [
                'tipo' => $tipo,
                'titulo' => ucfirst(str_replace('_', ' ', $tipo)),
                'total' => $pendientes + $porEjecutar,
                'pendientes' => $pendientes,
                'por_ejecutar' => $porEjecutar,
                'ruta' => $ruta,
                'icono' => $iconos[$tipo] ?? '<i class="far fa-bell mr-2"></i>',
            ];
        }

        return view('HojaEnBlanco', compact('tarjetas'));
    }
}
