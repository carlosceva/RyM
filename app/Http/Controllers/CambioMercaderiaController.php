<?php

namespace App\Http\Controllers;

use App\Models\Cambio;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use App\Models\Almacen;
use App\Models\Adjuntos;
use App\Services\NotificadorSolicitudService;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\SolicitudEjecutada;

class CambioMercaderiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        // Cargar almacenes activos si los necesitas en la vista
        $almacenes = Almacen::where('estado', 'a')->get();

        /*
        |--------------------------------------------------------------------------
        | 1. GERENCIA (Pueden ver TODAS las solicitudes)
        |--------------------------------------------------------------------------
        */
        if (
            $user->hasRole('Administrador') ||
            $user->can('Cambio_aprobar') ||
            $user->can('Cambio_reprobar')
        ) {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('cambioMercaderia', fn($q) =>
                    $q->where('estado', '!=', 'inactivo')
                )
                ->with([
                    'usuario',
                    'cambioMercaderia' => fn($q) =>
                        $q->where('estado', '!=', 'inactivo')
                            ->with('almacen_nombre')
                ])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

            return view('GestionSolicitudes.cambios.index', compact('solicitudes', 'almacenes'));
        }

        /*
        |--------------------------------------------------------------------------
        | 2. ENCARGADO DE ALMACÉN
        |--------------------------------------------------------------------------
        | Solo ve solicitudes dirigidas a almacenes donde él es encargado
        |--------------------------------------------------------------------------
        */
        if ($user->esEncargadoDeAlmacen()) {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('cambioMercaderia', function ($query) use ($user) {
                    $query->where('estado', '!=', 'inactivo')
                        ->whereHas('almacen_nombre.encargado', function ($q) use ($user) {
                            $q->where('id', $user->id);
                        });
                })
                ->with([
                    'usuario',
                    'cambioMercaderia' => function ($q) use ($user) {
                        $q->where('estado', '!=', 'inactivo')
                            ->with(['almacen_nombre' => function ($q2) use ($user) {
                                $q2->whereHas('encargado', fn($q3) => $q3->where('id', $user->id));
                            }]);
                    }
                ])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

            return view('GestionSolicitudes.cambios.index', compact('solicitudes', 'almacenes'));
        }

        /*
        |--------------------------------------------------------------------------
        | 3. SOLICITANTE (Usuario normal)
        |--------------------------------------------------------------------------
        | Solo ve solicitudes que él mismo creó
        |--------------------------------------------------------------------------
        */
        $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
            ->where('id_usuario', $user->id)
            ->whereHas('cambioMercaderia', fn($q) =>
                $q->where('estado', '!=', 'inactivo')
            )
            ->with([
                'usuario',
                'cambioMercaderia' => fn($q) =>
                    $q->where('estado', '!=', 'inactivo')
                        ->with('almacen_nombre')
            ])
            ->orderBy('fecha_solicitud', 'desc')
            ->get();

        return view('GestionSolicitudes.cambios.index', compact('solicitudes', 'almacenes'));
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
            'glosa' => 'nullable|string',
            'nota_venta' => 'required|string',
            'id_almacen' => 'required|integer|exists:almacen,id',
            'detalle_productos' => 'nullable',
            'motivo' => 'nullable|string',
        ]);

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

            // Procesar archivo adjunto si existe
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
                $path = $archivo->storeAs('adjuntos', $nombreArchivo, 'public');

                Adjuntos::create([
                    'id_solicitud' => $solicitud->id,
                    'archivo' => $path,
                ]);
            }

            // Insertar en solicitud_cambios_mercaderia
            Cambio::create([
                'id_solicitud' => $solicitud->id,
                'nota_venta' => $request->nota_venta,
                'id_almacen' => $request->id_almacen,
                'detalle_productos' => $request->detalle_productos,
                'motivo' => $request->motivo,
                // estado queda por defecto en 'pendiente'
            ]);

            // Notificación
            $notificador->notificar($solicitud, 'crear');

            DB::commit();

            return redirect()->route('CambiosFisicos.index')->with('success', 'Solicitud de cambio de mercadería creada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('CambiosFisicos.index')->with('error', 'Hubo un problema al crear la solicitud: ' . $e->getMessage());
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
        return redirect()->route('CambiosFisicos.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
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
        $solicitud = Solicitud::with(['usuario', 'cambioMercaderia', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.cambios.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

    /**
     * Display the specified resource.
     */
    public function show(Solicitud $solicitud)
    {
        // Validar que sea del tipo correcto y que esté activo
        if ($solicitud->tipo !== 'Cambio fisico en Mercaderia' || $solicitud->estado === 'inactivo') {
            abort(404);
        }

        // Cargar relaciones necesarias
        $solicitud->load(['usuario', 'autorizador', 'ejecucion', 'cambioMercaderia']);

        return view('GestionSolicitudes.cambios.show', compact('solicitud'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cambio $cambio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cambio $cambio)
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
        $precio = $solicitud->cambioMercaderia;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('CambiosFisicos.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }
}
