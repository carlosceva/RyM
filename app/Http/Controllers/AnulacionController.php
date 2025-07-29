<?php

namespace App\Http\Controllers;

use App\Models\Anulacion;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Devolucion;
use App\Models\User;
use App\Models\Almacen;
use App\Services\WhatsAppService;
use App\Services\NotificadorSolicitudService;

class AnulacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $almacenes = Almacen::where('estado', 'a')->get();

        if ($user->hasRole('Supra Administrador')) {
            $solicitudes = Solicitud::whereHas('anulacion')
                ->with(['usuario', 'anulacion'])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } elseif ($user->can('Anulacion_entrega') && $user->esEncargadoDeAlmacen()) {
            // ✅ Almacenero encargado: solo solicitudes de su(s) almacén(es)
            $idsAlmacenes = $user->almacenesEncargados->pluck('id')->toArray();

            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('anulacion', function ($query) use ($idsAlmacenes) {
                    $query->where('estado', '!=', 'inactivo')
                        ->whereIn('id_almacen', $idsAlmacenes);
                })
                ->with(['usuario', 'anulacion' => function ($query) use ($idsAlmacenes) {
                    $query->where('estado', '!=', 'inactivo')
                        ->whereIn('id_almacen', $idsAlmacenes);
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } elseif (
            $user->hasRole('Administrador') ||
            $user->can('Anulacion_aprobar') ||
            $user->can('Anulacion_reprobar') ||
            $user->can('Anulacion_pago') ||
            // ✅ Permitimos Anulacion_entrega si no es un almacenero sin almacén
            ($user->can('Anulacion_entrega') && !$user->esEncargadoDeAlmacen())
        ) {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('anulacion', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'anulacion' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } else {
            // Usuario común: solo sus solicitudes activas
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('anulacion', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'anulacion' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'asc')
                ->get();
        }

        return view('GestionSolicitudes.anulacion.index', compact('solicitudes', 'almacenes'));
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
            
            $anulacion = Anulacion::create([
                'id_solicitud' => $solicitud->id,
                'nota_venta' => $request->nota_venta,
                'motivo' => $request->motivo,
                'tiene_pago' => $request->tiene_pago,
                'obs_pago' => $request->obs_pago,
                'id_almacen' => $request->id_almacen,
            ]);

            $notificador->notificar($solicitud, 'crear');
    
            DB::commit();
    
            return redirect()->route('Anulacion.index')->with('success', 'Solicitud de Anulacion creada.');
    
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Hubo un error al procesar la solicitud. Intenta nuevamente.');
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
        }
    
        // Si se proporciona una observación, la guardamos
        if ($request->observacion) {
            $solicitud->observacion = $request->observacion;
        }
    
        // Guardamos los cambios en la base de datos
        $solicitud->save();
    
        // Redirigimos al usuario con un mensaje de éxito
        return redirect()->route('Anulacion.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id, NotificadorSolicitudService $notificador)
    {
        $solicitud = Solicitud::findOrFail($id);
    
        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo las solicitudes aprobadas pueden ser ejecutadas.');
        }
    
        $anulacion = $solicitud->anulacion;
    
        if (!$anulacion) {
            return back()->with('error', 'No se encontró la información de anulación asociada a esta solicitud.');
        }

         // ⚠️ Manejo seguro de valores booleanos, compatible con MySQL y PostgreSQL
        $tienePago = filter_var($anulacion->tiene_pago, FILTER_VALIDATE_BOOLEAN);
        $tieneEntrega = filter_var($anulacion->tiene_entrega, FILTER_VALIDATE_BOOLEAN);

        if (is_null($anulacion->entrega_fisica)) {
            $entregaFisica = null;
        } else {
            $entregaFisica = filter_var($anulacion->entrega_fisica, FILTER_VALIDATE_BOOLEAN);
        }

        // ✅ Evaluación segura de anulación
        $esAnulacion = !$tienePago && !$tieneEntrega && ($entregaFisica === false || is_null($entregaFisica));

        $usuarioSolicitante = $solicitud->usuario;
    
        if ($esAnulacion) {
            // ✅ Caso: anulación directa
            SolicitudEjecutada::create([
                'solicitud_id' => $solicitud->id,
                'ejecutado_por' => Auth::id(),
                'fecha_ejecucion' => now(),
            ]);
    
            $solicitud->estado = 'ejecutada';
            $solicitud->save();

            $notificador->notificar($solicitud, 'ejecutar_anulacion');
    
            return back()->with('success', 'Solicitud de anulación ejecutada correctamente.');
        }
    
        // ✅ Caso: generar devolución
        DB::beginTransaction();
    
        try {
            $solicitud->estado = 'convertida';
            $solicitud->observacion = 'Se procedió con una solicitud de devolución.';
            $solicitud->save();
    
            $nuevaSolicitud = Solicitud::create([
                'id_usuario' => $solicitud->id_usuario,
                'tipo' => 'Devolucion de Venta',
                'fecha_solicitud' => now(),
                'estado' => 'aprobada',
                'id_autorizador' => $solicitud->id_autorizador,
                'fecha_autorizacion' => $solicitud->fecha_autorizacion,
                'observacion' => 'Generada automáticamente desde solicitud de anulación #' . $solicitud->id,
            ]);
    
            Devolucion::create([
                'id_solicitud' => $nuevaSolicitud->id,
                'nota_venta' => $anulacion->nota_venta,
                'motivo' => 'Conversión automática desde solicitud de anulación',
                'cliente' => '',
                'almacen' => 'No definido',
                'detalle_productos' => '',
                'tiene_pago' => $anulacion->tiene_pago,
                'tiene_entrega' => $anulacion->tiene_entrega,
                'entrega_fisica' => $anulacion->entrega_fisica,
                'obs_pago' => $anulacion->obs_pago,
                'almacen' => $anulacion->almacen->id,
            ]);
    
            DB::commit();

            // ✅ Notificar al solicitante
            $notificador->notificar($solicitud, 'ejecutar_devolucion');
            $notificador->notificar($solicitud, 'crear_devolucion');
    
            return redirect()->route('Devolucion.index')
                ->with('success', 'La anulación fue convertida en una solicitud de devolución.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar la devolución: ' . $e->getMessage());
        }
    }   

    public function verificarEntregaFisica(Request $request, $id, NotificadorSolicitudService $notificador)
    {
        $request->validate([
            'entrega' => 'required|boolean',
        ]);
    
        $solicitud = Solicitud::findOrFail($id);
    
        if (!$solicitud->anulacion) {
            return redirect()->back()->with('error', 'No se encontró el registro de anulación.');
        }
    
        $solicitud->anulacion->entrega_fisica = $request->entrega;
        $solicitud->anulacion->save();

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
    
        if (!$solicitud->anulacion) {
            return redirect()->back()->with('error', 'No se encontró el registro de anulación.');
        }
    
        $solicitud->anulacion->tiene_entrega = $request->entrega;
        $solicitud->anulacion->save();

        // ✅ Notificar solo si se seleccionó "NO tiene entrega"
        if (!$request->entrega) {
            
            $notificador->notificar($solicitud, 'verificar_entrega');
        }
    
        return redirect()->back()->with('success', 'Verificación de entrega registrada correctamente.');
    }     

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['usuario', 'anulacion', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.anulacion.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_anulacion_{$solicitud->id}.pdf");
    }

    public function descargarExcel($id)
    {
        $solicitud = Solicitud::with(['usuario', 'autorizador', 'anulacion', 'ejecucion.usuario'])->findOrFail($id);

        $filename = "ticket_anulacion_{$solicitud->id}.csv";

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($solicitud) {
            $output = fopen('php://output', 'w');

            // Encabezado de columnas
            fputcsv($output, [
                'ID',
                'Fecha solicitud',
                'Tipo',
                'Estado',
                'Solicitante',
                'Nota_venta',
                'motivo',
                'Glosa',
                'Autorizador',
                'Fecha autorizacion',
                'Ejecutado por',
                'Fecha ejecucion',
                'Observacion'
            ]);

            // Datos
            fputcsv($output, [
                $solicitud->id,
                $solicitud->fecha_solicitud,
                strtolower($solicitud->tipo),
                strtolower($solicitud->estado),
                $solicitud->usuario->name ?? 'ND',
                $solicitud->anulacion->nota_venta ?? 'N/D',
                $solicitud->anulacion->motivo ?? 'Sin motivo',
                $solicitud->glosa ?? 'Sin glosa',
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
    public function show(Anulacion $anulacion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Anulacion $anulacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Anulacion $anulacion)
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
        $precio = $solicitud->anulacion;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('Anulacion.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }
}
