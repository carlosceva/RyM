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
            'Devolucion de Venta' => 'Devolucion.index',
            'Anulacion de Venta' => 'Anulacion.index',
            'Sobregiro de Venta' => 'Sobregiro.index',
            'precio_especial' => 'PrecioEspecial.index',
            'Muestra de Mercaderia' => 'Muestra.index',
            'Baja de Mercaderia' => 'Baja.index',
            'Cambio fisico en Mercaderia' => 'CambiosFisicos.index',
            'Extras' => 'Extras.index',
            'Vacacion' => 'Vacaciones.index',
        ];

        // Mapa de íconos HTML por tipo
        $iconos = [
            'Devolucion de Venta' => '<i class="fas fa-undo mr-2"></i>',
            'Anulacion de Venta' => '<i class="far fa-times-circle mr-2"></i>',
            'Sobregiro de Venta' => '<i class="far fa-arrow-alt-circle-up mr-2"></i>',
            'precio_especial' => '<i class="far fa-file-alt mr-2"></i>',
            'Muestra de Mercaderia' => '<i class="far fa-file-alt mr-2"></i>',
            'Baja de Mercaderia' => '<i class="far fa-trash-alt mr-2"></i>',
            'Cambio fisico en Mercaderia' => '<i class="fa fa-box mr-2"></i>',
            'Extras' => '<i class="fa fa-plane mr-2"></i>',
            'Vacacion' => '<i class="fas fa-utensils mr-2"></i>',
        ];

        $tarjetas = [];

        foreach ($tiposConRutas as $tipo => $ruta) {
            $pendientes = 0;
            $porEjecutar = 0;

            switch ($tipo) {
                case 'precio_especial':
                case 'Muestra de Mercaderia':
                case 'Baja de Mercaderia':
                case 'Cambio fisico en Mercaderia':
                case 'Vacacion':
                    $pendientes = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'pendiente')
                        ->count();

                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'aprobada')
                        ->whereDoesntHave('ejecucion') 
                        ->count();
                    break;
                
                case 'Sobregiro de Venta':
                    $pendientes = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'pendiente')
                        ->count();

                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'confirmada')
                        ->whereDoesntHave('ejecucion')
                        ->count();
                    break;
                
                case 'Extras':
                    $pendientes = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'pendiente')
                        ->count();

                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'confirmada')
                        ->whereDoesntHave('ejecucion')
                        ->count();
                    break;

                case 'Devolucion de Venta':
                    $pendientes = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'pendiente')
                        ->count();

                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'aprobada')
                        ->whereHas('devolucion', function ($query) {
                            $query->whereNotNull('tiene_pago');
                        })
                        ->whereDoesntHave('ejecucion') 
                        ->count();
                    break;
                case 'Anulacion de Venta':
                     $pendientes = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'pendiente')
                        ->count();

                    $porEjecutar = Solicitud::where('tipo', $tipo)
                        ->where('estado', 'aprobada')
                        ->whereHas('anulacion', function ($query) {
                            $query->whereNotNull('tiene_pago');
                        })
                        ->whereDoesntHave('ejecucion') 
                        ->count();
                    break;

                default:
                    break;
            }

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
