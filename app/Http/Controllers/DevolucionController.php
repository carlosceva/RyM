<?php

namespace App\Http\Controllers;

use App\Models\Devolucion;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class DevolucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador') || $user->can('Devolucion_aprobar') || $user->can('Devolucion_reprobar')) {
            $solicitudes = Solicitud::whereHas('devolucion')
            ->with('usuario', 'devolucion')
            ->orderBy('fecha_solicitud', 'desc')
            ->get();        
        } else {
            $solicitudes = Solicitud::where('id_usuario', $user->id)
            ->whereHas('devolucion')
            ->with('usuario', 'devolucion')
            ->orderBy('fecha_solicitud', 'desc')
            ->get();        
        }

        return view('GestionSolicitudes.devolucion.index', compact('solicitudes'));
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
            'nota_venta' => 'required|string', 
            'motivo' => 'required|string',  
        ]);
    
        DB::beginTransaction();
    
        try {
            // Si no se selecciona, asigna `false` por defecto.
            $requiereAbono = $request->has('requiere_abono') ? true : false;
            $tieneEntrega = $request->has('tiene_entrega') ? true : false;
    
            $solicitud = Solicitud::create([
                'id_usuario' => auth()->user()->id,
                'tipo' => $request->tipo,
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'glosa' => $request->glosa,
            ]);
    
            $anulacion = Devolucion::create([
                'id_solicitud' => $solicitud->id,
                'nota_venta' => $request->nota_venta,
                'motivo' => $request->motivo,
                'cliente' => $request->cliente,
                'almacen' => $request->almacen,
                'detalle_productos' => $request->detalle_productos,
                'requiere_abono' => $requiereAbono,
                'tiene_entrega' => $tieneEntrega,
            ]);
    
            DB::commit();
    
            return redirect()->route('Devolucion.index')->with('success', 'Solicitud de Devolucion creada.');
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            return redirect()->route('Devolucion.index')->with('error', 'Hubo un problema al crear la solicitud de Devolucion: ' . $e->getMessage());
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
        return redirect()->route('Devolucion.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id)
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
    
        return back()->with('success', 'Solicitud ejecutada exitosamente.');
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Devolucion $devolucion)
    {
        //
    }
}
