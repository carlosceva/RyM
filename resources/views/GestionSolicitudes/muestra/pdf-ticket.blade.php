<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Solicitud #{{ $solicitud->id }}</title>
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
        .text-end { text-align: right; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
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
                <span>#{{ $solicitud->id }}</span>
                <span> - Fecha: {{ $solicitud->fecha_solicitud }}</span>
            </div>
            <div class="text-center mt-2">
                <strong>{{ ucfirst($solicitud->tipo) }}</strong>
            </div>
        </div>

        <div class="card-body mt-2">
            <div>
                <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
                <p><strong>Motivo:</strong></p>
                <div class="border p-2 rounded bg-light small">
                    {{ $solicitud->glosa ?? 'Sin glosa' }}
                </div>
            </div>

            @if($solicitud->muestraMercaderia)
            <div class="mt-3">
                <p><strong>Cliente:</strong> {{ $solicitud->muestraMercaderia->cliente ?? 'Sin cliente' }}</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cod-SAI</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Medida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $productos = explode(',', $solicitud->muestraMercaderia->detalle_productos);
                        @endphp
                        @foreach($productos as $index => $item)
                            @php
                                $partes = explode('-', $item);
                                $codsai = trim($partes[0] ?? '');
                                $producto = trim($partes[1] ?? '');
                                $cantidad = trim($partes[2] ?? '');
                                $medida = trim($partes[3] ?? '');
                            @endphp
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $codsai }}</td>
                                <td>{{ $producto }}</td>
                                <td>{{ $cantidad }}</td>
                                <td>{{ $medida }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Autorización -->
            <div class="row mt-2">
                <div class="col-12 border-top pt-2">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap;" class="small">
                        <span><strong>Autorizado por:</strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</span>
                        <span class="badge {{ $clase_color }}">
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
                        <span class="badge {{ $clase_color_ejecucion }}">
                            {{ $solicitud->ejecucion ? 'Ejecutada' : 'Pendiente' }}
                        </span>
                        <span>{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</span>
                    </div>
                </div>
            </div>
            @endif

            <div class="mt-2">
                <p><strong>Observación:</strong></p>
                <div class="border p-2 rounded bg-light small">
                    {{ $solicitud->observacion ?? 'Sin observación' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
