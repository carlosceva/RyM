<?php

namespace App\Http\Controllers;

use App\Models\BajaMercaderia;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Adjuntos;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Almacen;
use App\Services\WhatsAppService;
use App\Services\NotificadorSolicitudService;

class BajaMercaderiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $almacenes = Almacen::where('estado', 'a')->get();

        if ($user->hasRole('Administrador') || $user->can('Baja_aprobar') || $user->can('Baja_reprobar') || $user->can('Baja_ejecutar')) {
            // Administrador o usuarios con permisos especiales
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('bajaMercaderia', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'bajaMercaderia' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        } elseif ($user->esEncargadoDeAlmacen()) {
            // Si es encargado de almacén, solo mostrar las solicitudes relacionadas con sus almacenes
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('bajaMercaderia', function ($query) use ($user) {
                    // Filtrar por almacenes donde el usuario es encargado
                    $query->whereHas('almacen_nombre', function ($query) use ($user) {
                        $query->whereHas('encargado', function ($query) use ($user) {
                            $query->where('id', $user->id);
                        });
                    });
                })
                ->with(['usuario', 'bajaMercaderia' => function ($query) use ($user) {
                    // Filtrar también en la carga de la relación bajaMercaderia
                    $query->whereHas('almacen_nombre', function ($query) use ($user) {
                        $query->whereHas('encargado', function ($query) use ($user) {
                            $query->where('id', $user->id);
                        });
                    });
                }])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        } else {
            // Para otros usuarios, solo mostrar sus propias solicitudes
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('bajaMercaderia', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with(['usuario', 'bajaMercaderia' => function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                }])
                ->orderBy('fecha_solicitud', 'asc')
                ->get();
        }

        return view('GestionSolicitudes.baja.index', compact('solicitudes', 'almacenes'));
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
            //'archivo' => 'nullable|file|mimes:xlsx,xls,csv,pdf,docx,jpg,png|max:2048', // Validación del archivo
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
        
            // Si hay un archivo adjunto, procesarlo
            if ($request->hasFile('archivo')) {
                // Obtener el archivo
                $archivo = $request->file('archivo');
        
                // Generar un nombre único para el archivo
                $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
        
                // Guardar el archivo en el almacenamiento público
                $path = $archivo->storeAs('adjuntos', $nombreArchivo, 'public');
        
                // Guardar el registro en la tabla 'adjuntos'
                Adjuntos::create([
                    'id_solicitud' => $solicitud->id,
                    'archivo' => $path,  // Guardar la ruta del archivo en la base de datos
                ]);
            }
            //dd($request);
            // Crear la baja de mercadería
            $solicitudBajaMercaderia = BajaMercaderia::create([
                'id_solicitud' => $solicitud->id,
                'id_almacen' => $request->almacen,
                'almacen' =>  $request->almacen,
                'detalle_productos' => $request->detalle_productos,
                'motivo' => $request->motivo,
                'tipo' => $request->tipo_ajuste,
            ]);
            
            $notificador->notificar($solicitud, 'crear');

            DB::commit();

            return redirect()->route('Baja.index')->with('success', 'Solicitud de Ajuste de inventario creada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('Baja.index')->with('error', 'Hubo un problema al crear la solicitud de Ajuste de inventario: ' . $e->getMessage());
        }
    }
    
    public function confirmar($id, NotificadorSolicitudService $notificador)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Verificar si la solicitud ya está confirmada o no está pendiente
        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo las solicitudes pendientes pueden ser confirmadas.');
        }

        // Registrar la confirmación
        $solicitud->estado = 'confirmada';
        $solicitud->bajaMercaderia->id_autorizador = Auth::id();  // Guardar el ID del autorizador
        $solicitud->bajaMercaderia->fecha_autorizacion = now();   // Fecha de autorización
        $solicitud->bajaMercaderia->save();
        
        // Guardar la solicitud
        $solicitud->save();

        $notificador->notificar($solicitud, 'confirmar');

        return back()->with('success', 'Solicitud confirmada exitosamente.');
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

            $notificador->notificar(
                    solicitud: $solicitud,
                    etapa: 'aprobar'
                );

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
        return redirect()->route('Baja.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
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
        $solicitud = Solicitud::with(['usuario', 'bajaMercaderia', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.baja.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

    public function descargarExcel($id)
    {
        $solicitud = Solicitud::with(['usuario', 'bajaMercaderia', 'autorizador'])->findOrFail($id);

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
            if ($solicitud->bajaMercaderia) {
                fputcsv($output, ['Almacen', $solicitud->bajaMercaderia->cliente ?? 'Sin almacen']);
                fputcsv($output, []);

                // Encabezados de la tabla de productos
                fputcsv($output, ['#', 'Producto', 'Cantidad']);

                if (!empty($solicitud->bajaMercaderia->detalle_productos)) {
                    $productos = explode(',', $solicitud->bajaMercaderia->detalle_productos);
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

    public function descargar($id)
    {
        $adjunto = Adjuntos::findOrFail($id);
        $ruta = storage_path('app/public/' . $adjunto->archivo);

        if (!file_exists($ruta)) {
            abort(404);
        }

        return response()->download($ruta);
    }

    /**
     * Display the specified resource.
     */
    public function show(BajaMercaderia $bajaMercaderia)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BajaMercaderia $bajaMercaderia)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BajaMercaderia $bajaMercaderia)
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
        $precio = $solicitud->bajaMercaderia;
        if ($precio) {
            $precio->estado = 'inactivo';
            $precio->save();
        }

        return redirect()->route('Baja.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }
}
