<?php

namespace App\Http\Controllers;

use App\Models\Extras;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudEjecutada;
use App\Services\NotificadorSolicitudService;

class ExtrasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Condición: permisos que permiten ver todas las solicitudes
        $permisosGerenciales = [
            'Extra_aprobar',
            'Extra_reprobar',
            'Extra_confirmar',
            'Extra_ejecutar'
        ];

        $tienePermisoCompleto = 
            $user->hasRole('Administrador') ||
            $user->hasAnyPermission($permisosGerenciales);

        if ($tienePermisoCompleto) {
            // Puede ver todo
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('extra')
                ->with(['usuario', 'extra'])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        } else {
            // Solo ve lo que él haya creado
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('extra')
                ->with(['usuario', 'extra'])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        }

        return view('GestionSolicitudes.extras.index', compact('solicitudes', 'user'));
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
    public function store(Request $request, NotificadorSolicitudService $notificador) 
    {
        // Validación de los campos
        $request->validate([
            'tipo' => 'required|string',
            'tipo_extra' => 'required|string',
            'glosa' => 'nullable|string',
        ]);

        //dd($request->all());

        DB::beginTransaction();

        try {

            // Crear la solicitud
            $solicitud = Solicitud::create([
                'id_usuario' => auth()->user()->id,
                'tipo' => $request->tipo,
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'glosa' => $request->glosa,
            ]);

            // Insertar en solicitud_cambios_mercaderia
            Extras::create([
                'id_solicitud' => $solicitud->id,
                'tipo_extra' => $request->tipo_extra,
                'fecha_solicitud' => now(),
            ]);

            // Notificación
            $notificador->notificar($solicitud, 'crear');

            DB::commit();

            return redirect()->route('Extras.index')->with('success', 'Solicitud extra creada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('Extras.index')->with('error', 'Hubo un problema al crear la solicitud: ' . $e->getMessage());
        }
    }

    public function aprobar_o_rechazar(Request $request, NotificadorSolicitudService $notificador)
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

            $notificador->notificar(solicitud: $solicitud, etapa: 'aprobar');

        } elseif ($request->accion === 'rechazar') {
            $solicitud->estado = 'rechazada';

            $notificador->notificar(solicitud: $solicitud,etapa: 'reprobar');
        }
    
        // Si se proporciona una observación, la guardamos
        if ($request->observacion) {
            $solicitud->observacion = $request->observacion;
        }
    
        // Guardamos los cambios en la base de datos
        $solicitud->save();
    
        // Redirigimos al usuario con un mensaje de éxito
        return redirect()->route('Extras.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id, NotificadorSolicitudService $notificador)
    {
        
        $solicitud = Solicitud::findOrFail($id);
    
        // Solo puede ejecutarse si está aprobada y aún no ha sido ejecutada
        if ($solicitud->estado !== 'confirmada') {
            return back()->with('error', 'Solo las solicitudes confirmadas pueden ser ejecutadas.');
        }
    
        if ($solicitud->ejecucion) {
            return back()->with('error', 'Esta solicitud ya fue ejecutada.');
        }
    
        // Registrar ejecución
        SolicitudEjecutada::create([
            'solicitud_id' => $solicitud->id,
            'ejecutado_por' => Auth::id(),
            'fecha_ejecucion' => now(),
        ]);

        // Cambiar el estado de la solicitud
        $solicitud->estado = 'ejecutada';
        $solicitud->save();

        $notificador->notificar($solicitud, 'ejecutar');
    
        return back()->with('success', 'Solicitud ejecutada exitosamente.');
    }

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['usuario', 'extra', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.extras.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

    public function confirmar($id, NotificadorSolicitudService $notificador, Request $request)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Verificar si la solicitud ya está confirmada o no está pendiente
        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo las solicitudes aprobadas pueden ser confirmadas.');
        }

        // Registrar la confirmación
        $solicitud->estado = 'confirmada';
        $solicitud->extra->id_confirmador = Auth::id();  // Guardar el ID del autorizador
        $solicitud->extra->fecha_confirmacion = now();   // Fecha de autorización
        $solicitud->extra->save();
        
        // Guardar la solicitud
        $solicitud->save();

        $notificador->notificar($solicitud, 'confirmar');

        return back()->with('success', 'Solicitud confirmada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        // Validar que sea del tipo correcto y que esté activo
        if ($solicitud->tipo !== 'Extras' || $solicitud->estado === 'inactivo') {
            abort(404);
        }

        // Cargar relaciones necesarias
        $solicitud->load(['usuario', 'autorizador', 'ejecucion', 'extra']);

        return view('GestionSolicitudes.extras.show', compact('solicitud'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Extras $extras)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Extras $extras)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Cambiar estado de la solicitud
        $solicitud->estado = 'inactivo';
        $solicitud->save();

        // Cambiar estado del precio especial si existe
        $precio = $solicitud->extra;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('Extras.index')->with('success', 'Solicitud anulada correctamente.');
    }
}
