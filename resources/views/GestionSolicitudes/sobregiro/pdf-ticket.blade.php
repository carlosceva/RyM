<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket Solicitud #{{ $solicitud->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; margin: 0; padding: 20px; }
        .card { border: 1px solid #ccc; border-radius: 5px; }
        .card-header { padding: 10px; color: white; font-weight: bold; }
        .bg-success { background-color: #28a745; }
        .bg-danger { background-color: #dc3545; }
        .bg-warning { background-color: #ffc107; color: black; }
        .bg-primary { background-color: #0d6efd; color: black; }
        .card-body { padding: 20px; }
        .border { border: 1px solid #ccc; }
        .rounded { border-radius: 5px; }
        .small { font-size: 11px; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .d-flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .align-center { align-items: center; }
        .flex-wrap { flex-wrap: wrap; }
        .flex-grow { flex-grow: 1; }
        .me-2 { margin-right: 0.5rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .pt-2 { padding-top: 0.5rem; }
        .w-100 { width: 100%; }
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
        <!-- Header -->
        <div class="card-header {{ $clase_color }}">
            <div class="d-flex justify-between">
                <span>#{{ $solicitud->id }}</span>
                <span>{{ $solicitud->fecha_solicitud }}</span>
            </div>
            <div class="text-center mt-2">
                <strong>{{ ucfirst($solicitud->tipo) }}</strong>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="card-body">
            <div class="d-flex justify-between w-100 mb-2">
                <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
                @if($solicitud->sobregiro)
                    <p class="mb-1"><strong>Importe:</strong> {{ $solicitud->sobregiro?->importe ? number_format($solicitud->sobregiro->importe, 2, ',', '.') : '0,00' }}</p>
                @endif
            </div>

            <!-- Motivo -->
            <div class="d-flex align-center mb-2">
                <strong class="me-2">Motivo:</strong>
                <div class="border p-2 rounded bg-light small flex-grow">
                    {{ $solicitud->glosa ?? 'Sin glosa' }}
                </div>
            </div>

            <!-- codigo -->
            <div class="d-flex align-center mb-2">
                <strong class="me-2">Codigo de sobregiro:</strong>
                <div class="border p-2 rounded bg-light small flex-grow">
                    {{ $solicitud->sobregiro->cod_sobregiro ?? 'N/D' }}
                </div>
            </div>

            <!-- Autorización -->
            <div class="border-top pt-2 mt-3">
                <div class="d-flex justify-between flex-wrap small">
                    <span><strong>Autorizado por:</strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</span>
                    <span class="badge {{ $clase_color }}">
                            {{ ucfirst($solicitud->estado) }}
                        </span>
                    <span><strong>Fecha Autorización:</strong> {{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                </div>
            </div>

            <!-- Ejecución -->
            @if($solicitud->estado !== 'rechazada')
            <div class="border-top pt-2 mt-2">
                <div class="d-flex justify-between flex-wrap small">
                    <span><strong>Ejecutado por:</strong> {{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</span>
                    <span class="badge {{ $clase_color_ejecucion }}">
                            {{ $solicitud->ejecucion ? 'Ejecutada' : 'Pendiente' }}
                        </span>
                    <span><strong>Fecha Ejecución:</strong> {{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</span>
                </div>
            </div>
            @endif

            <!-- Observación -->
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

