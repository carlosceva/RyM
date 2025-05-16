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
use App\Services\WhatsAppService;

class AnulacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador') || $user->can('Anulacion_aprobar') || $user->can('Anulacion_reprobar') || $user->can('Anulacion_pago') || $user->can('Anulacion_entrega')) {
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

        return view('GestionSolicitudes.anulacion.index', compact('solicitudes'));
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
    public function store(Request $request, WhatsAppService $whatsapp)
    {
        $request->validate([
            'tipo' => 'required|string',
            'glosa' => 'nullable|string',
            'nota_venta' => 'required|string', 
            'motivo' => 'required|string',  
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
            ]);

            $usuariosResponsables = User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'Anulacion_aprobar');
            })->get();

            $phoneNumbers = $usuariosResponsables->map(function ($user) {
                return [
                    'telefono' => '+591' . str_pad($user->telefono, 8, '0', STR_PAD_LEFT),
                    'api_key' => $user->key, 
                ];
            });

            $phoneNumbers = $phoneNumbers->toArray();

            $message = "Se ha creado una nueva solicitud de *Anulacion de Venta* y está esperando aprobación.\n" .
            "Número de solicitud: " . $solicitud->id . "\n" .
            "Fecha de creación: " . $solicitud->fecha_solicitud->format('d/m/Y H:i') . "\n" .
            "Solicitado por: " . auth()->user()->name . ".";

            $responses = $whatsapp->sendWithAPIKey($phoneNumbers, $message);
    
            DB::commit();
    
            return redirect()->route('Anulacion.index')->with('success', 'Solicitud de Anulacion creada.');
    
        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Hubo un error al procesar la solicitud. Intenta nuevamente.');
        }
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
        return redirect()->route('Anulacion.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id)
    {
        $solicitud = Solicitud::findOrFail($id);
    
        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo las solicitudes aprobadas pueden ser ejecutadas.');
        }
    
        $anulacion = $solicitud->anulacion;
    
        if (!$anulacion) {
            return back()->with('error', 'No se encontró la información de anulación asociada a esta solicitud.');
        }
    
        $tieneEntrega = (bool) $anulacion->tiene_entrega;
        $tienePago = (bool) $anulacion->tiene_pago;
    
        // Caso 1: No hay entrega ni pago → ejecutar anulación
        if (!$tieneEntrega && !$tienePago) {
            SolicitudEjecutada::create([
                'solicitud_id' => $solicitud->id,
                'ejecutado_por' => Auth::id(),
                'fecha_ejecucion' => now(),
            ]);
    
            $solicitud->estado = 'ejecutada';
            $solicitud->save();
    
            return back()->with('success', 'Solicitud de anulación ejecutada correctamente.');
        }
    
        // Caso 2: Hay entrega o pago → generar solicitud de devolución
        DB::beginTransaction();
    
        try {
            // 🔄 Actualizar solicitud original
            $solicitud->estado = 'convertida';
            $solicitud->observacion = 'Se procedió con una solicitud de devolución.';
            $solicitud->save();
    
            // 🆕 Crear nueva solicitud tipo devolución
            $nuevaSolicitud = Solicitud::create([
                'id_usuario' => $solicitud->id_usuario,
                'tipo' => 'Devolucion de Venta',
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'observacion' => 'Generada automáticamente desde solicitud de anulación #' . $solicitud->id,
            ]);
    
            // 🧾 Crear el registro de devolución
            Devolucion::create([
                'id_solicitud' => $nuevaSolicitud->id,
                'nota_venta' => $anulacion->nota_venta,
                'motivo' => 'Conversión automática desde solicitud de anulación',
                'cliente' => '',
                'almacen' => '',
                'detalle_productos' => '',
                'tiene_pago' => $tienePago,
                'tiene_entrega' => $tieneEntrega,
            ]);
    
            DB::commit();
    
            return redirect()->route('Devolucion.index')
                ->with('success', 'La anulación fue convertida en una solicitud de devolución.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al generar la devolución: ' . $e->getMessage());
        }
    }
    

    public function verificarPago(Request $request, $id)
    {
        $request->validate([
            'pago' => 'required|boolean',
        ]);
    
        $solicitud = Solicitud::findOrFail($id);
    
        if (!$solicitud->anulacion) {
            return redirect()->back()->with('error', 'No se encontró el registro de anulación.');
        }
    
        $solicitud->anulacion->tiene_pago = $request->pago;
        $solicitud->anulacion->save();
    
        return redirect()->back()->with('success', 'Verificación de pago registrada correctamente.');
    }
    
    public function verificarEntrega(Request $request, $id)
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
