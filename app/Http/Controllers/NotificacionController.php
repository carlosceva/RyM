<?php

namespace App\Http\Controllers;

use App\Models\NotificacionLocal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function index(Request $request)
    {
        $usuario = auth()->user();

        // Total de notificaciones no leídas y leídas
        $totalNoLeidas = $usuario->notificacionesLocalesNoLeidas()->count();
        $totalLeidas = $usuario->notificacionesLocalesLeidas()->count();

        // Configuración de la paginación
        $perPage = 10; // Número de notificaciones por página
        $page = $request->input('page', 1); // Página actual, por defecto es 1

        // Calcular el offset
        $offset = ($page - 1) * $perPage;

        // Obtener las notificaciones con offset y limit
        $noLeidas = $usuario->notificacionesLocalesNoLeidas()
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();

        $leidas = $usuario->notificacionesLocalesLeidas()
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($perPage)
            ->get();


        // Número total de páginas
        $totalPagesNoLeidas = ceil($totalNoLeidas / $perPage);
        $totalPagesLeidas = ceil($totalLeidas / $perPage);

        return view('notificaciones.index', compact('noLeidas', 'leidas', 'totalPagesNoLeidas', 'totalPagesLeidas', 'page'));
    }


    public function marcarLeidaYRedirigir($id)
    {
        $notificacion = NotificacionLocal::findOrFail($id);

        if ($notificacion->user_id !== auth()->id()) {
            abort(403);
        }

        $notificacion->update(['estado' => 'read']);

        $solicitud = $notificacion->solicitud;

        // Mapeo tipo => nombre de ruta
        $rutas = [
            'precio_especial' => 'PrecioEspecial.index',
            'Devolucion de Venta' => 'Devolucion.index',
            'Anulacion de Venta' => 'Anulacion.index',
            'Sobregiro de Venta' => 'Sobregiro.index',
            'Muestra de Mercaderia' => 'Muestra.index',
            'Baja de Mercaderia' => 'Baja.index',
        ];

        $tipo = $solicitud->tipo;

        if (!isset($rutas[$tipo])) {
            return redirect()->route('notificaciones.index')->with('error', 'Tipo de solicitud no reconocido.');
        }

        return redirect()->route($rutas[$tipo]);
    }

}
