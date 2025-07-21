<?php

namespace App\Http\Controllers;

use App\Models\Sobregiro;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\WhatsAppService;
use App\Services\NotificadorSolicitudService;
use App\Services\Contracts\WhatsAppServiceInterface;

class SobregiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador') || $user->can('Sobregiro_aprobar') || $user->can('Sobregiro_reprobar') || $user->can('Sobregiro_ejecutar') || $user->can('Sobregiro_confirmar')) {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('sobregiro', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'sobregiro' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();        
        } else {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('sobregiro', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'sobregiro' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'asc')
                ->get();        
        }
        
        return view('GestionSolicitudes.sobregiro.index', compact('solicitudes'));
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
        // Validación de los datos
        $request->validate([
            'tipo' => 'required|string',
            'glosa' => 'nullable|string',
            'importe' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/', // Asegurándote que importe sea un número válido
            'cliente' => 'required|string',  // También puedes validar cliente según sea necesario
        ]);
    
        // Iniciar una transacción
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
    
            // Crear el sobregiro
            $solicitudSobregiro = Sobregiro::create([
                'id_solicitud' => $solicitud->id,
                'cliente' => $request->cliente,
                'importe' => $request->importe, // Asegúrate de que importe sea un número válido
            ]);
    
            $notificador->notificar($solicitud, 'crear');

            DB::commit();
    
            return redirect()->route('Sobregiro.index')->with('success', 'Solicitud de Sobregiro creada.');
    
        } catch (\Exception $e) {
            // Si algo falla, revertimos la transacción
            DB::rollBack();
    
            // Puedes manejar el error y devolverlo
            return redirect()->route('Sobregiro.index')->with('error', 'Hubo un problema al crear la solicitud de sobregiro: ' . $e->getMessage());
        }
    }

    public function aprobar_o_rechazar(Request $request, WhatsAppServiceInterface $whatsapp, NotificadorSolicitudService $notificador)
    {
        // Validación
        $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'accion' => 'required|in:aprobar,rechazar',
            'observacion' => 'nullable|string',
        ]);

        $solicitud = Solicitud::findOrFail($request->solicitud_id);
        $solicitud->id_autorizador = Auth::id();
        $solicitud->fecha_autorizacion = now();

        if ($request->accion === 'aprobar') {
            $solicitud->estado = 'aprobada';

            $notificador->notificar(
                    solicitud: $solicitud,
                    etapa: 'aprobar'
                );

        } elseif ($request->accion === 'rechazar') {
            $solicitud->estado = 'rechazada';
        }

        // Guardar observación si existe
        if ($request->observacion) {
            $solicitud->observacion = $request->observacion;
        }

        $solicitud->save();

        // Enviar mensaje al solicitante con la observación
        // $usuarioSolicitante = $solicitud->usuario;

        // if ($usuarioSolicitante && $usuarioSolicitante->telefono && $usuarioSolicitante->key) {
        //     $numero = '+591' . str_pad($usuarioSolicitante->telefono, 8, '0', STR_PAD_LEFT);
        //     $apiKey = $usuarioSolicitante->key;

        //     $estadoTexto = $solicitud->estado === 'aprobada' ? 'aprobada' : 'rechazada';
        //     $fechaTexto = now()->format('d/m/Y H:i');
        //     $usuarioEjecutor = auth()->user()->name;
        //     $observacionTexto = $request->observacion ? "\nObservación: " . $request->observacion : '';

        //     $mensaje = "📦 Su solicitud de *Sobregiro de Venta* ha sido *{$estadoTexto}*.\n" .
        //             "N° de solicitud: {$solicitud->id}\n" .
        //             "Fecha: {$fechaTexto}\n" .
        //             "Aprobado por: {$usuarioEjecutor}." .
        //             $observacionTexto;

        //     $destinatario = [[
        //         'telefono' => $numero,
        //         'api_key' => $apiKey
        //     ]];

        //     $whatsapp->sendWithAPIKey($destinatario, $mensaje);
        // }

        return redirect()->route('Sobregiro.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
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
        $solicitud->sobregiro->id_confirmador = Auth::id();  // Guardar el ID del autorizador
        $solicitud->sobregiro->fecha_confirmacion = now();   // Fecha de autorización
        $solicitud->sobregiro->cod_sobregiro = $request->cod_sobregiro;
        $solicitud->sobregiro->save();
        
        // Guardar la solicitud
        $solicitud->save();

        $notificador->notificar($solicitud, 'confirmar');

        return back()->with('success', 'Solicitud confirmada exitosamente.');
    }

    public function ejecutar($id, NotificadorSolicitudService $notificador)
    {
        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->estado !== 'confirmada') {
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

        $solicitud->estado = 'ejecutada';
        $solicitud->save();

        $notificador->notificar($solicitud, 'ejecutar');

        return back()->with('success', 'Solicitud ejecutada exitosamente.');
    }

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['usuario', 'sobregiro', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.sobregiro.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

    public function descargarExcel($id)
    {
        $solicitud = Solicitud::with(['usuario', 'autorizador', 'sobregiro', 'ejecucion.usuario'])->findOrFail($id);

        $filename = "ticket_solicitud_{$solicitud->id}.csv";

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
                'Importe',
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
                $solicitud->sobregiro->importe ?? 'N/D',
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
    public function show(Sobregiro $sobregiro)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sobregiro $sobregiro)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sobregiro $sobregiro)
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
        $precio = $solicitud->sobregiro;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('Sobregiro.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }
}
