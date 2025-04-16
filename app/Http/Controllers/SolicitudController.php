<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $solicitudes = Solicitud::with([
            'usuario',
            'autorizador',
            'precioEspecial' // Relación con solicitud_precio_especial
        ])
        ->orderBy('fecha_solicitud', 'desc')
        ->get();

        return view('GestionSolicitudes.general.index', compact('solicitudes'));
    }

    public function aprobar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estado = 'aprobada';
        $solicitud->id_autorizador = Auth::id();
        $solicitud->fecha_autorizacion = now();
        $solicitud->save();

        return back()->with('success', 'Solicitud aprobada correctamente.');
    }

    public function rechazar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estado = 'rechazada';
        $solicitud->id_autorizador = Auth::id();
        $solicitud->fecha_autorizacion = now();
        $solicitud->save();

        return back()->with('success', 'Solicitud rechazada correctamente.');
    }

    public function aprobar_o_rechazar(Request $request)
    {
        // Validamos la solicitud
        $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'accion' => 'required|in:aprobar,rechazar',
            'observacion' => 'nullable|string',
        ]);
    
        // Obtenemos la solicitud
        $solicitud = Solicitud::findOrFail($request->solicitud_id);
    
        // Asignamos el autorizador y la fecha de autorización
        $solicitud->id_autorizador = Auth::id();
        $solicitud->fecha_autorizacion = now();
    
        // Actualizamos el estado dependiendo de la acción
        if ($request->accion === 'aprobar') {
            $solicitud->estado = 'aprobada';
        } elseif ($request->accion === 'rechazar') {
            $solicitud->estado = 'rechazada';
        }
    
        // Si se proporciona una observación, la guardamos
        if ($request->observacion) {
            $solicitud->observacion = $request->observacion;
        }
    
        // Guardamos los cambios en la base de datos
        $solicitud->save();
    
        // Redirigimos al usuario con un mensaje de éxito
        return redirect()->route('general.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }
    
    




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Solicitud $solicitud)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Solicitud $solicitud)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Solicitud $solicitud)
    {
        //
    }
}
