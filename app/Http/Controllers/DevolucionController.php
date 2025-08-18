<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use App\Models\Anulacion;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Almacen;
use App\Services\WhatsAppService;
use App\Services\NotificadorSolicitudService;

class DevolucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $almacenes = Almacen::where('estado', 'a')->get();

        if ($user->hasRole('Supra Administrador')) {
            $solicitudes = Solicitud::whereHas('devolucion')
                ->with(['usuario', 'devolucion'])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } elseif ($user->can('Devolucion_entrega') && $user->esEncargadoDeAlmacen()) {
            // ✅ Almacenero encargado: solo solicitudes de su(s) almacén(es)
            $idsAlmacenes = $user->almacenesEncargados->pluck('id')->toArray();

            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('devolucion', function ($query) use ($idsAlmacenes) {
                    $query->where('estado', '!=', 'inactivo')
                        ->whereIn('almacen', $idsAlmacenes);
                })
                ->with(['usuario', 'devolucion' => function ($query) use ($idsAlmacenes) {
                    $query->where('estado', '!=', 'inactivo')
                        ->whereIn('almacen', $idsAlmacenes);
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } elseif (
            $user->hasRole('Administrador') ||
            $user->can('Devolucion_aprobar') ||
            $user->can('Devolucion_reprobar') ||
            $user->can('Devolucion_pago') ||
            // ✅ Aquí permitimos Devolucion_entrega solo si **NO** es un almacenero sin almacén
            ($user->can('Devolucion_entrega') && !$user->esEncargadoDeAlmacen())
        ) {
            // Ej: auxiliares que tienen el permiso pero no son encargados
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('devolucion', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'devolucion' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } else {
            // Usuario común solo ve sus propias solicitudes activas
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('devolucion', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'devolucion' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'asc')
                ->get();
        }

        return view('GestionSolicitudes.devolucion.index', compact('solicitudes', 'almacenes'));
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
        $request->validate([
            'tipo' => 'required|string',
            'glosa' => 'nullable|string',
            'nota_venta' => 'required|string', 
            'motivo' => 'required|string',  
            'tiene_pago' => 'required|boolean',
            'obs_pago' => 'nullable|string',
        ]);
        
        DB::beginTransaction();
    
        try {
    
            $solicitud = Solicitud::create([
                'id_usuario' => auth()->user()->id,
                'tipo' => $request->tipo,
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'glosa' => $request->glosa,
            ]);
    
            $devolucion = Devolucion::create([
                'id_solicitud' => $solicitud->id,
                'nota_venta' => $request->nota_venta,
                'motivo' => $request->motivo,
                'cliente' => $request->cliente,
                'almacen' => $request->almacen,
                'detalle_productos' => $request->detalle_productos,
                'tiene_pago' => $request->tiene_pago,
                'obs_pago' => $request->obs_pago,
            ]);
            
            $notificador->notificar($solicitud, 'crear');

            DB::commit();

            return redirect()->route('Devolucion.index')->with('success', 'Solicitud de Devolucion creada.');
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return redirect()->route('Devolucion.index')->with('error', 'Hubo un problema al crear la solicitud de Devolucion: ' . $e->getMessage());
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

            $notificador->notificar($solicitud, 'aprobar');

        } elseif ($request->accion === 'rechazar') {
            $solicitud->estado = 'rechazada';

            $notificador->notificar(
                    solicitud: $solicitud,
                    etapa: 'reprobar'
                );
        }
    
        // Si se proporciona una observación, la guardamos
        if ($request->observacion) {
            $solicitud->observacion = $request->observacion;
        }
    
        // Guardamos los cambios en la base de datos
        $solicitud->save();
    
        // Redirigimos al usuario con un mensaje de éxito
        return redirect()->route('Devolucion.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id, NotificadorSolicitudService $notificador)
    {
        $solicitud = Solicitud::findOrFail($id);
    
        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo las solicitudes aprobadas pueden ser ejecutadas.');
        }
    
        $devolucion = $solicitud->devolucion;

        if (!$devolucion) {
            return back()->with('error', 'No se encontró la información de devolucion asociada a esta solicitud.');
        }

        // ⚠️ Manejo seguro de valores booleanos, compatible con MySQL y PostgreSQL
        $tienePago = filter_var($devolucion->tiene_pago, FILTER_VALIDATE_BOOLEAN);
        $tieneEntrega = filter_var($devolucion->tiene_entrega, FILTER_VALIDATE_BOOLEAN);

        if (is_null($devolucion->entrega_fisica)) {
            $entregaFisica = null;
        } else {
            $entregaFisica = filter_var($devolucion->entrega_fisica, FILTER_VALIDATE_BOOLEAN);
        }

        // ✅ Evaluación segura de anulación
        $esAnulacion = !$tienePago && !$tieneEntrega && ($entregaFisica === false || is_null($entregaFisica));

        $usuarioSolicitante = $solicitud->usuario;
    
        // Caso 1: No hay entrega ni pago → convertir en anulación
        if ($esAnulacion) {
            DB::beginTransaction();
    
            try {
                // Marcar solicitud original como convertida
                $solicitud->estado = 'convertida';
                $solicitud->observacion = 'Se procedió con una solicitud de anulación.';
                $solicitud->save();
    
                // Crear nueva solicitud tipo anulación
                $nuevaSolicitud = Solicitud::create([
                    'id_usuario' => $solicitud->id_usuario,
                    'tipo' => 'Anulacion de Venta',
                    'fecha_solicitud' => now(),
                    'estado' => 'aprobada',
                    'id_autorizador' => $solicitud->id_autorizador,
                    'fecha_autorizacion' => $solicitud->fecha_autorizacion,
                    'observacion' => 'Generada automáticamente desde solicitud de devolución #' . $solicitud->id,
                ]);
    
                // Crear la relación en tabla `solicitud_anulacion`
                Anulacion::create([
                    'id_solicitud' => $nuevaSolicitud->id,
                    'nota_venta' => $devolucion->nota_venta,
                    'motivo' => $devolucion->motivo,
                    'tiene_pago' => $devolucion->tiene_pago,
                    'tiene_entrega' => $devolucion->tiene_entrega,
                    'entrega_fisica' => $devolucion->entrega_fisica,
                    'obs_pago' => $devolucion->obs_pago,
                    'id_almacen' => $devolucion->almacen,
                ]);
    
                DB::commit();

                $notificador->notificar($solicitud, 'ejecutar_anulacion');
                $notificador->notificar($solicitud, 'crear_anulacion');
    
                return redirect()->route('Anulacion.index')
                    ->with('success', 'La devolución fue convertida en una solicitud de anulación.');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error al generar la anulación: ' . $e->getMessage());
            }
        }
    
        // Caso 2: Hay entrega o pago → continuar como devolución ejecutada
        SolicitudEjecutada::create([
            'solicitud_id' => $solicitud->id,
            'ejecutado_por' => Auth::id(),
            'fecha_ejecucion' => now(),
        ]);
    
        $solicitud->estado = 'ejecutada';
        $solicitud->save();

        $notificador->notificar($solicitud, 'ejecutar_devolucion');
    
        return back()->with('success', 'Solicitud de devolución ejecutada correctamente.');
    }    

    public function verificarEntregaFisica(Request $request, $id, NotificadorSolicitudService $notificador)
    {
        $request->validate([
            'entrega' => 'required|boolean',
        ]);
    
        $solicitud = Solicitud::findOrFail($id);
    
        if (!$solicitud->devolucion) {
            return redirect()->back()->with('error', 'No se encontró el registro de devolucion.');
        }
    
        $solicitud->devolucion->entrega_fisica = $request->entrega;
        $solicitud->devolucion->save();

         // ✅ Notificar solo si se seleccionó "NO tiene entrega"
         if (!$request->entrega) {
            $notificador->notificar($solicitud, 'verificar_entrega_fisica');
        }
    
        return redirect()->back()->with('success', 'Verificación de Entrega registrada correctamente.');
    }
    
    public function verificarEntrega(Request $request, $id, NotificadorSolicitudService $notificador)
    {
        $request->validate([
            'entrega' => 'required|boolean',
        ]);
    
        $solicitud = Solicitud::findOrFail($id);
    
        if (!$solicitud->devolucion) {
            return redirect()->back()->with('error', 'No se encontró el registro de devolucion.');
        }
    
        $solicitud->devolucion->tiene_entrega = $request->entrega;
        $solicitud->devolucion->save();

        // ✅ Notificar solo si se seleccionó "NO tiene entrega"
        if (!$request->entrega) {
            
            $notificador->notificar($solicitud, 'verificar_entrega');
        }
    
        return redirect()->back()->with('success', 'Verificación de entrega registrada correctamente.');
    }    

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['usuario', 'devolucion', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.devolucion.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_devolucion_{$solicitud->id}.pdf");
    }

    public function descargarExcel($id)
    {
        $solicitud = Solicitud::with(['usuario', 'autorizador', 'ejecucion.usuario', 'devolucion'])->findOrFail($id);
    
        $filename = "ticket_devolucion_{$solicitud->id}.csv";
    
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];
    
        $callback = function () use ($solicitud) {
            $output = fopen('php://output', 'w');
    
            // Encabezado
            fputcsv($output, [
                'ID',
                'Fecha_solicitud',
                'Tipo',
                'Estado',
                'Solicitante',
                'Nota_venta',
                'Almacen',
                'Motivo',
                'Glosa',
                'detalle_productos',
                'Requiere_abono',
                'Tiene_entrega',
                'Autorizador',
                'Fecha_autorizacion',
                'Ejecutado_por',
                'Fecha_ejecucion',
                'Observacion'
            ]);
    
            // Datos
            fputcsv($output, [
                $solicitud->id,
                $solicitud->fecha_solicitud,
                strtolower($solicitud->tipo),
                strtolower($solicitud->estado),
                $solicitud->usuario->name ?? 'ND',
                $solicitud->devolucion->nota_venta ?? 'N/D',
                $solicitud->devolucion->almacen ?? 'N/D',
                $solicitud->devolucion->motivo ?? 'Sin motivo',
                $solicitud->glosa ?? 'Sin glosa',
                $solicitud->devolucion->detalle_productos,
                $solicitud->devolucion->requiere_abono ? 'Si' : 'No',
                $solicitud->devolucion->tiene_entrega ? 'Si' : 'No',
                $solicitud->autorizador->name ?? 'Sin autorizar',
                $solicitud->fecha_autorizacion ?? 'N/D',
                $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar',
                $solicitud->ejecucion->fecha_ejecucion ?? 'N/D',
                $solicitud->observacion ?? 'Sin observacion'
            ]);
    
            fclose($output);
        };
    
        return response()->stream($callback, 200, $headers);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Devolucion $devolucion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Devolucion $devolucion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Devolucion $devolucion)
    {
        //
    }

    public function actualizarAlmacen(Request $request, $id)
    {
        $request->validate([
            'almacen_id' => 'required|exists:almacen,id',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        $solicitud->devolucion->almacen = $request->almacen_id;
        $solicitud->devolucion->save();

        return redirect()->back()->with('success', 'Almacén asignado correctamente.');
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
        $precio = $solicitud->devolucion;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('Devolucion.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }
}
