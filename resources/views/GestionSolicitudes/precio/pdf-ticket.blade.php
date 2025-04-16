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
        <div class="card-header 
            @if($solicitud->estado === 'aprobada') bg-success 
            @elseif($solicitud->estado === 'rechazada') bg-danger 
            @else bg-warning 
            @endif">
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

            @if($solicitud->precioEspecial)
            <div class="mt-3">
                <p><strong>Cliente:</strong> {{ $solicitud->precioEspecial->cliente ?? 'Sin cliente' }}</p>
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
                            $productos = explode(',', $solicitud->precioEspecial->detalle_productos);
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
            @endif

            <div class="mt-3">
                <p><strong>Autorizado por:</strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</p>
                <p><strong>Estado:</strong> {{ ucfirst($solicitud->estado) }}</p>
                <p><strong>Fecha Autorización:</strong> {{ $solicitud->fecha_autorizacion ?? 'N/D' }}</p>
            </div>

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
