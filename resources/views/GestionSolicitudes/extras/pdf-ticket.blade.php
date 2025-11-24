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
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .d-flex { display: flex; justify-content: space-between; }
        .inline-info { display: flex; justify-content: space-between; width: 100%; }
    </style>
</head>
<body>
    <div class="card">
        @php 
            $clase_color = '';
            $clase_color_ejecucion = $solicitud->ejecucion ? 'bg-success' : 'bg-warning';

            if($solicitud->estado === 'aprobada') {
                $clase_color = 'bg-primary'; 
            } elseif($solicitud->estado === 'rechazada') { 
                $clase_color = 'bg-danger';
            } elseif($solicitud->estado === 'ejecutada') { 
                $clase_color = 'bg-success'; 
            } elseif($solicitud->estado === 'confirmada'){
                $clase_color = 'bg-primary';
            }else{ 
                $clase_color = 'bg-warning'; 
            }
        @endphp

        <!-- Encabezado -->
        <div class="card-header {{ $clase_color }}">
            <div class="d-flex">
                <span>#{{ $solicitud->id }}</span>
                <span> - Fecha: {{ $solicitud->fecha_solicitud }}</span>
            </div>
            <div class="text-center mt-2">
                <strong>Solicitud de Extras</strong>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="card-body mt-2">
            <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
            <p><strong>Tipo de Extra:</strong> {{ $solicitud->extra->tipo_extra ?? 'N/D' }}</p>

            <p class="mb-1"><strong>Glosa:</strong></p>
            <div class="border p-2 rounded bg-light small">
                {{ $solicitud->glosa ?? 'Sin glosa' }}
            </div>

            <!-- Autorización -->
            <div class="row mt-3">
                <div class="col-12 border-top pt-2">
                    <div class="d-flex justify-content-between flex-wrap small">
                        <span><strong>
                            @if($solicitud->estado !== 'rechazada')
                                Autorizado por:
                            @else
                                Rechazado por:
                            @endif
                        </strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</span>
                        <span class="badge {{ $clase_color }}">
                            @if( $solicitud->estado == 'pendiente')
                                    Pendiente
                            @elseif($solicitud->estado == 'ejecutada' || $solicitud->estado == 'confirmada' || $solicitud->estado == 'aprobada')
                                    Aprobada
                            @elseif( $solicitud->estado == 'rechazada')
                                    Rechazada
                            @endif
                        </span>
                        <span>{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                    </div>
                </div>
            </div>

            @if($solicitud->estado !=='rechazada')
                <!-- Confirmacion -->
                <div class="row mt-3">
                    <div class="col-12 border-top pt-2">
                        <div class="d-flex justify-content-between flex-wrap small">
                            <span><strong>Registrada por:</strong> {{ $solicitud->extra->confirmador->name ?? 'Sin registrar' }}</span>
                            <span class="badge {{ $clase_color }}">
                                
                                @if( $solicitud->estado == 'pendiente' || $solicitud->estado == 'aprobada')
                                    Pendiente
                                @elseif($solicitud->estado == 'ejecutada' || $solicitud->estado == 'confirmada')
                                    Registrada
                                @endif
                            </span>
                            <span>{{ $solicitud->extra->fecha_confirmacion ?? 'N/D' }}</span>
                        </div>
                        
                    </div>
                </div>

                <!-- Ejecución -->
                <div class="row mt-2">
                    <div class="col-12 border-top pt-2">
                        <div class="d-flex justify-content-between flex-wrap small">
                            <span><strong>Ejecutado por:</strong> {{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</span>
                            <span class="badge {{ $clase_color_ejecucion }}">
                                {{ $solicitud->ejecucion ? 'Ejecutada' : 'Pendiente' }}
                            </span>
                            <span>{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Observación -->
            <div class="mt-3">
                <p><strong>Observación:</strong></p>
                <div class="border p-2 rounded bg-light small">
                    {{ $solicitud->observacion ?? 'Sin observación' }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
