<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Solicitud #{{ $solicitud->id_visible }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .card { border: 1px solid #ccc; border-radius: 5px; padding: 20px; margin-bottom: 20px; }
        .card-header { padding: 10px; color: white; font-weight: bold; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-primary { background-color: #0d6efd; color: black; }
        .border { border: 1px solid #ccc; }
        .rounded { border-radius: 5px; }
        .small { font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; }
        .text-center { text-align: center; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .d-flex { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="card">
        @php 
            $clase_color = '';
            $clase_color_ejecucion = $solicitud->ejecucion ? 'bg-success' : 'bg-warning';

            if($solicitud->estado === 'aprobada') {
                $clase_color = 'bg-primary'; 
            }elseif($solicitud->estado === 'rechazada'){ 
                $clase_color = 'bg-danger';
            }elseif($solicitud->estado === 'ejecutada'){ 
                $clase_color = 'bg-success'; 
            }elseif($solicitud->estado === 'pendiente'){ 
                $clase_color = 'bg-warning'; 
            }
        @endphp

        <div class="card-header {{ $clase_color }}">
            <div class="d-flex">
                <span>#{{ $solicitud->id_visible }}</span>
                <span>Fecha: {{ $solicitud->fecha_solicitud }}</span>
            </div>
            <div class="text-center mt-2">
                <strong>{{ ucfirst($solicitud->tipo) }}</strong>
            </div>
        </div>

        <div class="card-body mt-2">
            <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>

            @if($solicitud->bonificacion)
            <table class="table mt-3">
                <tbody>
                    <tr>
                        <th>Nota de venta</th>
                        <td>{{ $solicitud->bonificacion->nota_venta ?? 'N/D' }}</td>
                    </tr>
                    <tr>
                        <th>Beneficiario</th>
                        <td>{{ $solicitud->bonificacion->beneficiario ?? 'Sin beneficiario' }}</td>
                    </tr>
                    <tr>
                        <th>Teléfono</th>
                        <td>{{ $solicitud->bonificacion->telefono ?? 'N/D' }}</td>
                    </tr>
                    <tr>
                        <th>Monto Bs.</th>
                        <td>{{ number_format((float) ($solicitud->bonificacion->monto ?? 0), 2, '.', ',') }}</td>
                    </tr>
                </tbody>
            </table>
            @endif

            <div class="mt-3">
                <p class="mb-1"><strong>Glosa:</strong></p>
                <div class="border rounded small" style="padding: 8px;">
                    {{ $solicitud->glosa ?? 'Sin glosa' }}
                </div>
            </div>

            <div class="mt-3" style="border-top: 1px solid #ccc; padding-top: 8px;">
                <div class="d-flex small">
                    <span><strong>Autorizado por:</strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</span>
                    <span>{{ ucfirst($solicitud->estado) }}</span>
                    <span>{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                </div>
            </div>

            @if($solicitud->estado !== 'rechazada')
            <div class="mt-2" style="border-top: 1px solid #ccc; padding-top: 8px;">
                <div class="d-flex small">
                    <span><strong>Ejecutado por:</strong> {{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</span>
                    <span>{{ $solicitud->ejecucion ? 'Ejecutada' : 'Pendiente' }}</span>
                    <span>{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</span>
                </div>
            </div>
            @endif

            <div class="mt-2">
                <p class="mb-1"><strong>Observación:</strong></p>
                <div class="border rounded small" style="padding: 8px;">
                    {{ $solicitud->observacion ?? 'Sin observación' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
