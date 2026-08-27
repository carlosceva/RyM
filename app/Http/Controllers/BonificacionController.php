<?php

namespace App\Http\Controllers;

use App\Models\Bonificacion;
use App\Models\Solicitud;
use App\Models\SolicitudEjecutada;
use App\Services\NotificadorSolicitudService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BonificacionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Administrador o usuarios con permisos de gestión pueden ver todas
        // las solicitudes de Bonificaciones.
        if (
            $user->hasRole('Administrador') ||
            $user->can('Bonificacion_aprobar') ||
            $user->can('Bonificacion_reprobar') ||
            $user->can('Bonificacion_ejecutar')
        ) {

            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->whereHas('bonificacion', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with([
                    'usuario',
                    'autorizador',
                    'ejecucion.usuario',
                    'bonificacion' => function ($query) {
                        $query->where('estado', '!=', 'inactivo');
                    },
                ])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();

        } else {

            // Para usuarios normales, mostrar únicamente
            // las solicitudes creadas por ellos mismos.
            $solicitudes = Solicitud::where('estado', '!=', 'inactivo')
                ->where('id_usuario', $user->id)
                ->whereHas('bonificacion', function ($query) {
                    $query->where('estado', '!=', 'inactivo');
                })
                ->with([
                    'usuario',
                    'autorizador',
                    'ejecucion.usuario',
                    'bonificacion' => function ($query) {
                        $query->where('estado', '!=', 'inactivo');
                    },
                ])
                ->orderBy('fecha_solicitud', 'desc')
                ->get();
        }

        return view(
            'GestionSolicitudes.bonificacion.index',
            compact('solicitudes')
        );
    }

    public function store(Request $request, NotificadorSolicitudService $notificador)
    {
        $request->validate([
            'nota_venta' => 'required|integer|min:0',
            'beneficiario' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'monto' => 'required|numeric|min:0',
            'glosa' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $solicitud = Solicitud::create([
                'id_usuario' => Auth::id(),
                'tipo' => 'Bonificaciones',
                'fecha_solicitud' => now(),
                'estado' => 'pendiente',
                'glosa' => $request->glosa,
            ]);

            Bonificacion::create([
                'id_solicitud' => $solicitud->id,
                'nota_venta' => $request->nota_venta,
                'beneficiario' => $request->beneficiario,
                'telefono' => $request->telefono,
                'monto' => $request->monto,
            ]);

            // Bonificaciones siempre inicia pendiente y pasa por aprobación/rechazo.
            $notificador->notificar($solicitud, 'crear');

            DB::commit();

            return redirect()->route('Bonificaciones.index')
                ->with('success', 'Solicitud de Bonificaciones creada.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()->route('Bonificaciones.index')
                ->with('error', 'Hubo un problema al crear la solicitud de Bonificaciones: ' . $e->getMessage());
        }
    }

    public function aprobar_o_rechazar(Request $request, NotificadorSolicitudService $notificador)
    {
        $request->validate([
            'solicitud_id' => 'required|exists:solicitudes,id',
            'accion' => 'required|in:aprobar,rechazar',
            'observacion' => 'nullable|string',
        ]);

        $solicitud = Solicitud::findOrFail($request->solicitud_id);

        if ($solicitud->tipo !== 'Bonificaciones' || $solicitud->estado === 'inactivo') {
            abort(404);
        }

        if ($solicitud->estado !== 'pendiente') {
            return back()->with('error', 'Solo las solicitudes pendientes pueden aprobarse o rechazarse.');
        }

        if ($request->accion === 'aprobar') {
            abort_unless(Auth::user()->can('Bonificacion_aprobar'), 403);
        } else {
            abort_unless(Auth::user()->can('Bonificacion_reprobar'), 403);
        }

        $solicitud->id_autorizador = Auth::id();
        $solicitud->fecha_autorizacion = now();
        $solicitud->estado = $request->accion === 'aprobar' ? 'aprobada' : 'rechazada';

        if ($request->filled('observacion')) {
            $solicitud->observacion = $request->observacion;
        }

        $notificador->notificar(
            $solicitud,
            $request->accion === 'aprobar' ? 'aprobar' : 'reprobar'
        );

        $solicitud->save();

        return redirect()->route('Bonificaciones.index')
            ->with('success', 'La solicitud ha sido ' . $solicitud->estado . ' correctamente.');
    }

    public function ejecutar($id, NotificadorSolicitudService $notificador)
    {
        abort_unless(Auth::user()->can('Bonificacion_ejecutar'), 403);

        $solicitud = Solicitud::findOrFail($id);

        if ($solicitud->tipo !== 'Bonificaciones' || $solicitud->estado === 'inactivo') {
            abort(404);
        }

        if ($solicitud->estado !== 'aprobada') {
            return back()->with('error', 'Solo las solicitudes aprobadas pueden ser ejecutadas.');
        }

        if ($solicitud->ejecucion) {
            return back()->with('error', 'Esta solicitud ya fue ejecutada.');
        }

        DB::beginTransaction();

        try {
            SolicitudEjecutada::create([
                'solicitud_id' => $solicitud->id,
                'ejecutado_por' => Auth::id(),
                'fecha_ejecucion' => now(),
            ]);

            $solicitud->estado = 'ejecutada';
            $solicitud->save();

            $notificador->notificar($solicitud, 'ejecutar');

            DB::commit();

            return back()->with('success', 'Solicitud ejecutada exitosamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'No se pudo ejecutar la solicitud: ' . $e->getMessage());
        }
    }

    public function descargarPDF($id)
    {
        $solicitud = Solicitud::with([
            'usuario',
            'autorizador',
            'ejecucion.usuario',
            'bonificacion',
        ])->findOrFail($id);

        $this->validarAcceso($solicitud);

        $pdf = Pdf::loadView('GestionSolicitudes.bonificacion.pdf-ticket', compact('solicitud'));

        return $pdf->download("ticket_bonificacion_{$solicitud->id_visible}.pdf");
    }

    public function descargarExcel($id)
    {
        $solicitud = Solicitud::with([
            'usuario',
            'autorizador',
            'ejecucion.usuario',
            'bonificacion',
        ])->findOrFail($id);

        $this->validarAcceso($solicitud);

        $filename = "ticket_bonificacion_{$solicitud->id_visible}.csv";

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($solicitud) {
            $output = fopen('php://output', 'w');

            fputcsv($output, [
                'ID', 'Fecha solicitud', 'Tipo', 'Estado', 'Solicitante', 'Glosa',
                'Autorizador', 'Fecha autorizacion', 'Observacion'
            ]);

            fputcsv($output, [
                $solicitud->id_visible,
                $solicitud->fecha_solicitud,
                $solicitud->tipo,
                $solicitud->estado,
                $solicitud->usuario->name ?? 'ND',
                $solicitud->glosa ?? 'Sin glosa',
                $solicitud->autorizador->name ?? 'Sin autorizar',
                $solicitud->fecha_autorizacion ?? 'ND',
                $solicitud->observacion ?? 'Sin observacion',
            ]);

            fputcsv($output, []);

            if ($solicitud->bonificacion) {
                fputcsv($output, ['Nota de venta', $solicitud->bonificacion->nota_venta ?? 'N/D']);
                fputcsv($output, ['Beneficiario', $solicitud->bonificacion->beneficiario ?? 'Sin beneficiario']);
                fputcsv($output, ['Telefono', $solicitud->bonificacion->telefono ?? 'N/D']);
                fputcsv($output, ['Monto Bs.', $solicitud->bonificacion->monto ?? '0.00']);
            }

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Solicitud $solicitud)
    {
        $this->validarAcceso($solicitud);

        $solicitud->load([
            'usuario',
            'autorizador',
            'ejecucion.usuario',
            'bonificacion',
        ]);

        return view('GestionSolicitudes.bonificacion.show', compact('solicitud'));
    }

    private function validarAcceso(Solicitud $solicitud): void
    {
        if ($solicitud->tipo !== 'Bonificaciones' || $solicitud->estado === 'inactivo') {
            abort(404);
        }

        $user = Auth::user();

        if ($solicitud->id_usuario == $user->id) {
            return;
        }

        abort_unless($user->hasAnyPermission([
            'Bonificacion_aprobar',
            'Bonificacion_reprobar',
            'Bonificacion_ejecutar',
        ]), 403);
    }

    public function destroy($id)
    {
        $solicitud = Solicitud::findOrFail($id);

        // Cambiar estado de la solicitud
        $solicitud->estado = 'inactivo';
        $solicitud->save();

        // Cambiar estado del precio especial si existe
        $b = $solicitud->bonificacion;
        if ($b) {
            $b->estado = 'inactivo';
            $b->save();
        }

        return redirect()->route('Bonificaciones.index')
            ->with('success', 'Solicitud anulada correctamente.');
    }
}
