<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tipos = [
            'precio_especial',
            'Devolucion de Venta',
            'Anulacion de Venta',
            'Sobregiro de Venta',
            'Muestra de Mercaderia',
            'Baja de Mercaderia'
        ];

        $permisosPorTipo = [
            'precio_especial' => 'Precio_especial_ver',
            'Devolucion de Venta' => 'Devolucion_ver',
            'Anulacion de Venta' => 'Anulacion_ver',
            'Sobregiro de Venta' => 'Sobregiro_ver',
            'Muestra de Mercaderia' => 'Muestra_ver',
            'Baja de Mercaderia' => 'Baja_ver',
        ];

        $aliasTipos = [
            'Baja de Mercaderia' => 'Ajuste de Inv.',
        ];

        $tiposSolicitud = [];
        $pendientesPorTipo = [];
        $aprobadasPorTipo = [];
        $rechazadasPorTipo = [];
        $ejecutadasPorTipo = [];

        $userId = Auth::id();
        $verTodas = $this->tienePermisoEjecutar();

        foreach ($tipos as $tipo) {
            // Verifica si el usuario tiene permiso para ver este tipo
            $permiso = $permisosPorTipo[$tipo] ?? null;
            if ($permiso && !Auth::user()->can($permiso)) {
                continue; // Saltar este tipo si no tiene permiso
            }

            // Reemplazar nombre si existe en el alias
            $nombreAmigable = $aliasTipos[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
            $tiposSolicitud[] = $nombreAmigable;

            // Query base
            $baseQuery = Solicitud::where('tipo', $tipo);
            if (!$verTodas) {
                $baseQuery = $baseQuery->where('id_usuario', $userId);
            }

            $pendientes = (clone $baseQuery)->where('estado', 'pendiente')->count();
            $aprobadas = (clone $baseQuery)->where('estado', 'aprobada')->count();
            $rechazadas = (clone $baseQuery)->where('estado', 'rechazada')->count();
            $ejecutadas = (clone $baseQuery)->whereHas('ejecucion')->count();

            $pendientesPorTipo[] = $pendientes;
            $aprobadasPorTipo[] = $aprobadas;
            $rechazadasPorTipo[] = $rechazadas;
            $ejecutadasPorTipo[] = $ejecutadas;
        }

        return view('HojaEnBlanco', compact(
            'tiposSolicitud',
            'pendientesPorTipo',
            'aprobadasPorTipo',
            'rechazadasPorTipo',
            'ejecutadasPorTipo'
        ));
    }

    private function tienePermisoEjecutar(): bool
    {
        $permisos = [
            'Precio_especial_ejecutar',
            'Devolucion_ejecutar',
            'Anulacion_ejecutar',
            'Sobregiro_ejecutar',
            'Muestra_ejecutar',
            'Baja_ejecutar',
        ];

        foreach ($permisos as $permiso) {
            if (Auth::user()->can($permiso)) {
                return true;
            }
        }

        return false;
    }

}
