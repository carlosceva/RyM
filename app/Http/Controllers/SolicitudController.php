<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudPrecioEspecial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;

class SolicitudController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador')) {
            $solicitudes = Solicitud::with('usuario', 'precioEspecial.cliente')
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        } else {
            $solicitudes = Solicitud::where('id_usuario', $user->id)
                ->with('usuario', 'precioEspecial.cliente')
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        }
        
        // Verificar si hay datos
        if ($solicitudes->isEmpty()) {
            $solicitudes = [];
        }
        $clientes = Cliente::all();

        return view('GestionSolicitudes.general.index', compact('solicitudes','clientes'));
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
        $request->validate([
            'tipo' => 'required|string',
            'glosa' => 'nullable|string',
            // Otros campos de validación
        ]);

        $solicitud = Solicitud::create([
            'id_usuario' => auth()->user()->id,
            'tipo' => $request->tipo,
            'fecha_solicitud' => now(),
            'estado' => 'pendiente',
            'glosa' => $request->glosa,
        ]);

        // Crear la solicitud de precio especial
        $solicitudPrecioEspecial = SolicitudPrecioEspecial::create([
            'id_solicitud' => $solicitud->id,
            'id_cliente' => $request->id_cliente, // Asegúrate de tener el cliente
            'detalle_productos' => json_encode($request->detalle_productos), // Suponiendo que el detalle es un array
        ]);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud de precio especial creada.');
    }

    // Lógica para aprobar/rechazar las solicitudes
    public function autorizar(Solicitud $solicitud)
    {
        $this->authorize('approve-solicitud', Solicitud::class); // Middleware para asegurarse de que solo los administradores lo hagan

        $solicitud->update([
            'estado' => 'aprobada',
            'id_autorizador' => auth()->user()->id,
            'fecha_autorizacion' => now(),
        ]);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud aprobada.');
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
