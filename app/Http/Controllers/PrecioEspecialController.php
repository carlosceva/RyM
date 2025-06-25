<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudPrecioEspecial;
use App\Models\SolicitudEjecutada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\WhatsAppService;

class PrecioEspecialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
    
        if ($user->hasRole('Administrador') || $user->can('Precio_especial_aprobar') || $user->can('Precio_especial_reprobar')) {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('precioEspecial', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'precioEspecial' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();        
        } else {
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('precioEspecial', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'precioEspecial' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'asc')
                ->get();        
        }
    
        return view('GestionSolicitudes.precio.index', compact('solicitudes'));
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
            
            $solicitudPrecioEspecial = SolicitudPrecioEspecial::create([
                'id_solicitud' => $solicitud->id,
                'cliente' => $request->cliente,
                'detalle_productos' => $request->detalle_productos,
            ]);
            
            $usuariosResponsables = User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'Precio_especial_aprobar');
            })->get();

            $phoneNumbers = $usuariosResponsables->map(function ($user) {
                return [
                    'telefono' => '+591' . str_pad($user->telefono, 8, '0', STR_PAD_LEFT),
                    'api_key' => $user->key, 
                ];
            });

            $phoneNumbers = $phoneNumbers->toArray();
            
            $message = "Se ha creado una nueva solicitud de *Precio especial* y está esperando aprobación.\n" .
            "N° de solicitud: " . $solicitud->id . "\n" .
            "Fecha: " . $solicitud->fecha_solicitud->format('d/m/Y H:i') . "\n" .
            "Solicitado por: " . auth()->user()->name . ".";

            $responses = $whatsapp->sendWithAPIKey($phoneNumbers, $message);

            DB::commit();

            return redirect()->route('PrecioEspecial.index')->with('success', 'Solicitud de precio especial creada.');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al procesar la solicitud. Intenta nuevamente.');
        }
    }

    // public function aprobar_o_rechazar(Request $request, WhatsAppService $whatsapp)
    // {
    //     // Validamos la solicitud
    //     $request->validate([
    //         'solicitud_id' => 'required|exists:solicitudes,id',
    //         'accion' => 'required|in:aprobar,rechazar',
    //         'observacion' => 'nullable|string',
    //     ]);
    
    //     // Obtenemos la solicitud
    //     $solicitud = Solicitud::findOrFail($request->solicitud_id);
    
    //     // Asignamos el autorizador y la fecha de autorización
    //     $solicitud->id_autorizador = Auth::id();
    //     $solicitud->fecha_autorizacion = now();
    
    //     // Actualizamos el estado dependiendo de la acción
    //     if ($request->accion === 'aprobar') {
    //         $solicitud->estado = 'aprobada';

    //         $usuarioSolicitante = $solicitud->usuario;

    //         if ($usuarioSolicitante && $usuarioSolicitante->telefono && $usuarioSolicitante->key) {
    //             $numero = '+591' . str_pad($usuarioSolicitante->telefono, 8, '0', STR_PAD_LEFT);
    //             $apiKey = $usuarioSolicitante->key;

    //             $mensaje = "✅ Su solicitud de *Precio especial* ha sido *aprobada*.\n" .
    //                 "N° de solicitud: {$solicitud->id}\n" .
    //                 "Fecha: " . now()->format('d/m/Y H:i') . "\n" .
    //                 "Aprobado por: " . auth()->user()->name . ".";

    //             // Formato esperado por el método `sendWithAPIKey()`
    //             $destinatario = [[
    //                 'telefono' => $numero,
    //                 'api_key' => $apiKey
    //             ]];

    //             $whatsapp->sendWithAPIKey($destinatario, $mensaje);
    //         }

    //     } elseif ($request->accion === 'rechazar') {
    //         $solicitud->estado = 'rechazada';
    //     }
    
    //     // Si se proporciona una observación, la guardamos
    //     if ($request->observacion) {
    //         $solicitud->observacion = $request->observacion;
    //     }
    
    //     // Guardamos los cambios en la base de datos
    //     $solicitud->save();
    
    //     // Redirigimos al usuario con un mensaje de éxito
    //     return redirect()->route('PrecioEspecial.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    // }

    public function aprobar_o_rechazar(Request $request, WhatsAppService $whatsapp)
    {
        $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'accion' => 'required|in:aprobar,rechazar',
            'observacion' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::findOrFail($request->solicitud_id);

            // Cargamos la relación PrecioEspecial
            $precioEspecial = $solicitud->precioEspecial;

            // Asignamos datos de autorización
            $solicitud->id_autorizador = Auth::id();
            $solicitud->fecha_autorizacion = now();

            if ($request->accion === 'aprobar') {
                $solicitud->estado = 'aprobada';

                $usuariosResponsables = User::whereHas('roles.permissions', function ($query) {
                    $query->where('name', 'Precio_especial_ejecutar');
                })->get();

                $phoneNumbers = $usuariosResponsables->map(function ($user) {
                    return [
                        'telefono' => '+591' . str_pad($user->telefono, 8, '0', STR_PAD_LEFT),
                        'api_key' => $user->key,
                    ];
                })->toArray();

                $message = "Se ha aprobado una solicitud de *Precio especial de venta* y está esperando su ejecución.\n" .
                    "N° de solicitud: " . $solicitud->id . "\n" .
                    "Fecha de autorización: " . $solicitud->fecha_autorizacion->format('d/m/Y H:i') . "\n" .
                    "Autorizado por: " . $solicitud->autorizador->name . ".";

                $responses = $whatsapp->sendWithAPIKey($phoneNumbers, $message);
            } elseif ($request->accion === 'rechazar') {
                $solicitud->estado = 'rechazada';
            }

            // Guardamos la observación si se proporcionó
            if ($request->observacion) {
                $solicitud->observacion = $request->observacion;
            }

            // ✅ Guardamos los productos editados en la relación PrecioEspecial
            if ($precioEspecial && $request->filled('detalle_productos_editado')) {
                $precioEspecial->detalle_productos = $request->detalle_productos_editado;
                $precioEspecial->save();
            }

            $solicitud->save();

            DB::commit();

            return redirect()->route('PrecioEspecial.index')
                ->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al procesar la solicitud. Intenta nuevamente.');
        }
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
    
            $mensaje = "📦 Su solicitud de *Precio especial de venta* ha sido *ejecutada*.\n" .
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
        $solicitud = Solicitud::with(['usuario', 'precioEspecial', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.precio.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

    public function descargarExcel($id)
    {
        $solicitud = Solicitud::with(['usuario', 'precioEspecial', 'autorizador'])->findOrFail($id);

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

            // Primera fila con las etiquetas (de forma horizontal)
            fputcsv($output, ['ID', 'Fecha solicitud', 'Tipo', 'Estado', 'Solicitante', 'Glosa', 'Autorizador', 'Fecha autorizacion', 'Observacion']);

            // Segunda fila con los valores correspondientes
            fputcsv($output, [
                $solicitud->id,
                $solicitud->fecha_solicitud,
                strtolower($solicitud->tipo),
                strtolower($solicitud->estado),
                $solicitud->usuario->name ?? 'ND',
                $solicitud->glosa ?? 'Sin glosa',
                $solicitud->autorizador->name ?? 'Sin autorizar',
                $solicitud->fecha_autorizacion ?? 'ND',
                $solicitud->observacion ?? 'Sin observacion'
            ]);

            // Espacio entre las secciones
            fputcsv($output, []);

            // Si hay productos
            if ($solicitud->precioEspecial) {
                fputcsv($output, ['Cliente', $solicitud->precioEspecial->cliente ?? 'Sin cliente']);
                fputcsv($output, []);

                // Encabezados de la tabla de productos
                fputcsv($output, ['#', 'Producto', 'Cantidad']);

                if (!empty($solicitud->precioEspecial->detalle_productos)) {
                    $productos = explode(',', $solicitud->precioEspecial->detalle_productos);
                    foreach ($productos as $index => $item) {
                        [$producto, $cantidad] = explode('-', $item) + [null, null];
                        fputcsv($output, [
                            $index + 1,
                            trim($producto),
                            trim($cantidad)
                        ]);
                    }
                } else {
                    fputcsv($output, ['-', 'No hay productos registrados', '-']);
                }
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
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
    public function destroy($id)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Cambiar estado de la solicitud
        $solicitud->estado = 'inactivo';
        $solicitud->save();

        // Cambiar estado del precio especial si existe
        $precio = $solicitud->precioEspecial;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('PrecioEspecial.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }

}
