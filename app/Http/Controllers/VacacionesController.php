<?php

namespace App\Http\Controllers;

use App\Models\Vacaciones;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudEjecutada;
use App\Services\NotificadorSolicitudService;

class VacacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Si es administrador o tiene permisos de gerencia, ve TODO
        if (
            $user->hasRole('Administrador') ||
            $user->can('Vacaciones_aprobar') ||
            $user->can('Vacaciones_reprobar') ||
            $user->can('Vacaciones_ejecutar')
        ) {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('vacacion')
                ->with('usuario', 'vacacion')
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        } 
        else {
            // Cualquier otro usuario solo ve sus propias solicitudes
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('vacacion')
                ->with('usuario', 'vacacion')
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        }

        return view('GestionSolicitudes.vacaciones.index', compact('solicitudes'));
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
            'fecha_inicial' => 'required|date',
            'fecha_fin'     => 'required|date|after_or_equal:fecha_inicial',
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
            Vacaciones::create([
                'id_solicitud' => $solicitud->id,
                'fecha_inicial' => $request->fecha_inicial,
                'fecha_fin' => $request->fecha_fin,
                'fecha_solicitud' => now(),
            ]);

            // Notificación
            $notificador->notificar($solicitud, 'crear');

            DB::commit();

            return redirect()->route('Vacaciones.index')->with('success', 'Solicitud de Permiso/Vacacion creada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('Vacaciones.index')->with('error', 'Hubo un problema al crear la solicitud: ' . $e->getMessage());
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
        return redirect()->route('Vacaciones.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id, NotificadorSolicitudService $notificador)
    {
        $solicitud = Solicitud::findOrFail($id);
    
        // Solo puede ejecutarse si está aprobada y aún no ha sido ejecutada
        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo las solicitudes aprobadas pueden ser ejecutadas.');
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
        $solicitud = Solicitud::with(['usuario', 'vacacion', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.vacaciones.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        // Validar que sea del tipo correcto y que esté activo
        if ($solicitud->tipo !== 'Vacacion' || $solicitud->estado === 'inactivo') {
            abort(404);
        }

        // Cargar relaciones necesarias
        $solicitud->load(['usuario', 'autorizador', 'ejecucion', 'vacacion']);

        return view('GestionSolicitudes.vacaciones.show', compact('solicitud'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vacaciones $vacaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vacaciones $vacaciones)
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
        $precio = $solicitud->vacacion;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('Vacaciones.index')->with('success', 'Solicitud anulada correctamente.');
    }
}
