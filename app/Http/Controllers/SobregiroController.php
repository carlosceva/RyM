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

class SobregiroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador') || $user->can('Sobregiro_aprobar') || $user->can('Sobregiro_reprobar') || $user->can('Sobregiro_ejecutar')) {
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
    public function store(Request $request, WhatsAppService $whatsapp)
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
    
            $usuariosResponsables = User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'Sobregiro_aprobar');
            })->get();

            $phoneNumbers = $usuariosResponsables->map(function ($user) {
                return [
                    'telefono' => '+591' . str_pad($user->telefono, 8, '0', STR_PAD_LEFT),
                    'api_key' => $user->key, 
                ];
            });

            $phoneNumbers = $phoneNumbers->toArray();

            $message = "Se ha creado una nueva solicitud de *Sobregiro de venta* y está esperando aprobación.\n" .
            "N° de solicitud: " . $solicitud->id . "\n" .
            "Fecha: " . $solicitud->fecha_solicitud->format('d/m/Y H:i') . "\n" .
            "Solicitado por: " . auth()->user()->name . ".";

            $responses = $whatsapp->sendWithAPIKey($phoneNumbers, $message);

            DB::commit();
    
            return redirect()->route('Sobregiro.index')->with('success', 'Solicitud de Sobregiro creada.');
    
        } catch (\Exception $e) {
            // Si algo falla, revertimos la transacción
            DB::rollBack();
    
            // Puedes manejar el error y devolverlo
            return redirect()->route('Sobregiro.index')->with('error', 'Hubo un problema al crear la solicitud de sobregiro: ' . $e->getMessage());
        }
    }

    public function aprobar_o_rechazar(Request $request, WhatsAppService $whatsapp)
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

            $usuariosResponsables = User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'Sobregiro_ejecutar');
            })->get();

            $phoneNumbers = $usuariosResponsables->map(function ($user) {
                return [
                    'telefono' => '+591' . str_pad($user->telefono, 8, '0', STR_PAD_LEFT),
                    'api_key' => $user->key, 
                ];
            });

            $phoneNumbers = $phoneNumbers->toArray();

            $message = "Se ha aprobado una solicitud de *Sobregiro de Venta* y está esperando su ejecucion.\n" .
            "N° de solicitud: " . $solicitud->id . "\n" .
            "Fecha de autorizacion: " . $solicitud->fecha_autorizacion->format('d/m/Y H:i') . "\n" .
            "Autorizado por: " . $solicitud->autorizador->name . ".";

            $responses = $whatsapp->sendWithAPIKey($phoneNumbers, $message);

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
        return redirect()->route('Sobregiro.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id, WhatsAppService $whatsapp)
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

        $usuarioSolicitante = $solicitud->usuario;

        if ($usuarioSolicitante && $usuarioSolicitante->telefono && $usuarioSolicitante->key) {
            $numero = '+591' . str_pad($usuarioSolicitante->telefono, 8, '0', STR_PAD_LEFT);
            $apiKey = $usuarioSolicitante->key;
    
            $mensaje = "📦 Su solicitud de *Sobregiro de Venta* ha sido *ejecutada*.\n" .
                       "N° de solicitud: {$solicitud->id}\n" .
                       "Fecha de ejecución: " . now()->format('d/m/Y H:i') . "\n" .
                       "Ejecutado por: " . auth()->user()->name . ".";
    
            $destinatario = [[
                'telefono' => $numero,
                'api_key' => $apiKey
            ]];
    
            $whatsapp->sendWithAPIKey($destinatario, $mensaje);
        }
    
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
