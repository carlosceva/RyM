<?php

namespace App\Http\Controllers;

use App\Models\BajaMercaderia;
use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Adjuntos;

class BajaMercaderiaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
    
        if ($user->hasRole('Administrador') || $user->can('Baja_aprobar') || $user->can('Baja_reprobar')) {
            $solicitudes = Solicitud::whereHas('bajaMercaderia')
                ->with('usuario', 'bajaMercaderia', 'adjuntos') // Cargamos los adjuntos también
                ->orderBy('fecha_solicitud', 'desc')
                ->get();        
        } else {
            $solicitudes = Solicitud::where('id_usuario', $user->id)
                ->whereHas('bajaMercaderia')
                ->with('usuario', 'bajaMercaderia', 'adjuntos') // Cargamos los adjuntos también
                ->orderBy('fecha_solicitud', 'asc')
                ->get();        
        }
    
        return view('GestionSolicitudes.baja.index', compact('solicitudes'));
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

        // Validación de los campos
        $request->validate([
            'tipo' => 'required|string',
            'glosa' => 'nullable|string',
            //'archivo' => 'nullable|file|mimes:xlsx,xls,csv,pdf,docx,jpg,png|max:2048', // Validación del archivo
        ]);
    
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
    
        // Crear la baja de mercadería
        $solicitudBajaMercaderia = BajaMercaderia::create([
            'id_solicitud' => $solicitud->id,
            'almacen' => $request->almacen, 
            'detalle_productos' => $request->detalle_productos,
            'motivo' => $request->motivo,
        ]);
    
        return redirect()->route('Baja.index')->with('success', 'Solicitud de Baja de Mercaderia creada.');
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
        return redirect()->route('Baja.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
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
    public function destroy(BajaMercaderia $bajaMercaderia)
    {
        //
    }
}
