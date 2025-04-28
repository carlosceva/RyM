<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Solicitud #{{ $solicitud->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .card { border: 1px solid #ccc; border-radius: 5px; margin-bottom: 20px; }
        .card-header, .card-footer { padding: 10px; color: white; font-weight: bold; }
        .bg-success { background-color: #198754; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: black; }
        .card-body { padding: 15px; }

        .row { clear: both; width: 100%; margin-bottom: 10px; }
        .col { display: inline-block; vertical-align: top; width: 48%; margin-right: 2%; }
        .col:last-child { margin-right: 0; }
        .col-12 { width: 100%; display: block; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; }
        .border { border: 1px solid #ccc; padding: 5px; }
        .rounded { border-radius: 5px; }
        .bg-light { background-color: #f8f9fa; }
        .small { font-size: 11px; }
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 11px; display: inline-block; }

        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mb-1 { margin-bottom: 5px; }
        .pt-2 { padding-top: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <!-- Header -->
        <div class="card-header
            @if($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') bg-success
            @elseif($solicitud->estado === 'rechazada') bg-danger
            @else bg-warning
            @endif">
            <div style="display: flex; justify-content: space-between;">
                <span>#{{ $solicitud->id }}</span>
                <span>{{ $solicitud->fecha_solicitud }}</span>
            </div>
            <div class="text-center mt-1">
                <strong>{{ ucfirst($solicitud->tipo) }}</strong>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="card-body">

            <div class="row">
                <div class="col-12">
                    <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <p class="mb-1"><strong>Nota de venta:</strong> {{ $solicitud->devolucion->nota_venta }}</p>
                </div>
                <div class="col">
                    <p class="mb-1"><strong>Almacén:</strong> {{ $solicitud->devolucion->almacen }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <p class="mb-1"><strong>Requiere abono:</strong> {{ $solicitud->devolucion->requiere_abono ? 'Sí' : 'No' }}</p>
                </div>
                <div class="col">
                    <p class="mb-1"><strong>Tiene entrega:</strong> {{ $solicitud->devolucion->tiene_entrega ? 'Sí' : 'No' }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <p class="mb-1"><strong>Motivo:</strong></p>
                    <div class="border rounded bg-light small">{{ $solicitud->devolucion->motivo }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <p class="mb-1"><strong>Glosa:</strong></p>
                    <div class="border rounded bg-light small">{{ $solicitud->glosa ?? 'Sin glosa' }}</div>
                </div>
            </div>

            @if($solicitud->devolucion)
                <div class="mt-3">
                    <p><strong>Detalle de productos:</strong></p>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Producto</th>
                                <th>Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $productos = explode(',', $solicitud->devolucion->detalle_productos);
                            @endphp
                            @foreach($productos as $index => $item)
                                @php
                                    $partes = explode('-', $item);
                                    $producto = trim($partes[0] ?? '');
                                    $cantidad = trim($partes[1] ?? '');
                                @endphp
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $producto }}</td>
                                    <td>{{ $cantidad }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p><strong>Sin productos registrados.</strong></p>
            @endif

            <!-- Autorización -->
            <div class="row mt-2">
                <div class="col-12 border-top pt-2">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap;" class="small">
                        <span><strong>Autorizado por:</strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</span>
                        <span class="badge bg-{{ ($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') ? 'success' : ($solicitud->estado === 'rechazada' ? 'danger' : 'warning') }}">
                            {{ ucfirst($solicitud->estado) }}
                        </span>
                        <span>{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                    </div>
                </div>
            </div>

            <!-- Ejecución -->
            @if($solicitud->estado !== 'rechazada')
            <div class="row mt-2">
                <div class="col-12 border-top pt-2">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap;" class="small">
                        <span><strong>Ejecutado por:</strong> {{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</span>
                        <span class="badge bg-{{ $solicitud->ejecucion ? 'success' : 'secondary' }}">
                            {{ $solicitud->ejecucion ? 'Ejecutada' : 'Pendiente' }}
                        </span>
                        <span>{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Observación -->
            <div class="row mt-2">
                <div class="col-12">
                    <p class="mb-1"><strong>Observación:</strong></p>
                    <div class="border rounded bg-light small">
                        {{ $solicitud->observacion ?? 'Sin observación' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
