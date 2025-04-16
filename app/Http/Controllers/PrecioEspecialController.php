<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use App\Models\SolicitudPrecioEspecial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cliente;
use Barryvdh\DomPDF\Facade\Pdf;

class PrecioEspecialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Administrador') || $user->can('Precio_especial_aprobar') || $user->can('Precio_especial_reprobar')) {
            $solicitudes = Solicitud::with('usuario', 'precioEspecial')
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        } else {
            $solicitudes = Solicitud::where('id_usuario', $user->id)
                ->with('usuario', 'precioEspecial')
                ->orderBy('fecha_solicitud', 'asc')
                ->get();
        }
        
        // Ya no necesitas $clientes si no usas la tabla clientes
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
    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'glosa' => 'nullable|string',
            // Otros campos de validación
        ]);

        $solicitud = Solicitud::create([
            'id_usuario' => auth()->user()->id,
            'tipo' => $request->tipo,
            'fecha_solicitud' => now(),
            'estado' => 'pendiente',
            'glosa' => $request->glosa,
        ]);

        // Crear la solicitud de precio especial
        $solicitudPrecioEspecial = SolicitudPrecioEspecial::create([
            'id_solicitud' => $solicitud->id,
            'cliente' => $request->cliente, // Asegúrate de tener el cliente
            'detalle_productos' => $request->detalle_productos, // Suponiendo que el detalle es un array
        ]);

        return redirect()->route('PrecioEspecial.index')->with('success', 'Solicitud de precio especial creada.');
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
        return redirect()->route('PrecioEspecial.index')->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with(['usuario', 'precioEspecial', 'autorizador'])->findOrFail($id);

        // Retorna el mismo contenido que ves en la tarjeta, pero en una vista PDF
        $pdf = Pdf::loadView('GestionSolicitudes.precio.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_solicitud_{$solicitud->id}.pdf");
    }

//     public function descargarExcel($id)
// {
//     $solicitud = Solicitud::with(['usuario', 'precioEspecial', 'autorizador'])->findOrFail($id);

//     $filename = "ticket_solicitud_{$solicitud->id}.csv"; // o .xlsx si usas Excel

//     $headers = [
//         'Content-type' => 'text/csv',
//         'Content-Disposition' => "attachment; filename={$filename}",
//         'Pragma' => 'no-cache',
//         'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
//         'Expires' => '0',
//     ];

//     $callback = function () use ($solicitud) {
//         $output = fopen('php://output', 'w');

//         // Encabezado: Titulos de las columnas
//         fputcsv($output, ['ID', 'Fecha solicitud', 'Tipo', 'Estado', 'Solicitante', 'Glosa', 'Autorizador', 'Fecha autorización', 'Observación']);

//         // Filas con los valores correspondientes
//         fputcsv($output, [
//             $solicitud->id,
//             $solicitud->fecha_solicitud,
//             strtolower($solicitud->tipo),
//             strtolower($solicitud->estado),
//             $solicitud->usuario->name ?? 'ND',
//             $solicitud->glosa ?? 'Sin glosa',
//             $solicitud->autorizador->name ?? 'Sin autorizar',
//             $solicitud->fecha_autorizacion ?? 'ND',
//             $solicitud->observacion ?? 'Sin observación'
//         ]);

//         fclose($output);
//     };

//     return response()->stream($callback, 200, $headers);
// }

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

    // Lógica para aprobar/rechazar las solicitudes
    public function autorizar(Solicitud $solicitud)
    {
        $this->authorize('approve-solicitud', Solicitud::class); // Middleware para asegurarse de que solo los administradores lo hagan

        $solicitud->update([
            'estado' => 'aprobada',
            'id_autorizador' => auth()->user()->id,
            'fecha_autorizacion' => now(),
        ]);

        return redirect()->route('solicitudes.index')->with('success', 'Solicitud aprobada.');
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
    public function destroy(Solicitud $solicitud)
    {
        //
    }
}
