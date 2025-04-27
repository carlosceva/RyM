<!-- Ticket -->
<div class="col">
    <div class="card shadow-sm h-100 ">
        <!-- Header -->
        <div class="card-header @if($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') 
                bg-success 
            @elseif($solicitud->estado === 'rechazada') 
                bg-danger 
            @endif">
            <!-- Fila 1: ID y Fecha -->
            <div class="d-flex justify-content-between">
                <span>#{{ $solicitud->id }}</span>
                <span>{{ $solicitud->fecha_solicitud }}</span>
            </div>
            <!-- Fila 2: Tipo de solicitud centrado -->
            <div class="text-center mt-1">
                <strong>{{ ucfirst($solicitud->tipo) }}</strong>
            </div>
        </div>

        <!-- Cuerpo -->
        <div class="card-body">
            <div class="row">
                <!-- Columna izquierda -->
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>

                    <p><strong>Motivo:</strong></p>
                    <div class="border p-2 rounded bg-light small">
                        {{ $solicitud->glosa ?? 'Sin glosa' }}
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    @if($solicitud->bajaMercaderia)
                    <p class="mb-2"><strong>{{ $solicitud->bajaMercaderia->almacen ?? 'Sin almacen' }}</strong></p>
                    <div class="border p-2 rounded bg-light small">
                        <table class="table table-borderless table-sm mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Producto</th>
                                    <th scope="col">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($solicitud->bajaMercaderia->detalle_productos))
                                    @php $productos = explode(',', $solicitud->bajaMercaderia->detalle_productos); @endphp
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
                                @else
                                    <tr>
                                        <td colspan="3">No hay productos registrados.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>

            </div>

            <!-- Autorización -->
            <div class="row mt-3">
                <div class="col-12 border-top pt-2">
                    <div class="d-flex justify-content-between flex-wrap small">
                        <span><strong>Autorizado por:</strong> {{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</span>
                        <span class="badge bg-{{ ($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') ? 'success' : ($solicitud->estado === 'rechazada' ? 'danger' : 'warning') }}">
                            {{ ucfirst($solicitud->estado) }}
                        </span>
                        <span>{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                    </div>
                    
                </div>
            </div>

            <!-- Ejecución -->
            @if($solicitud->estado !=='rechazada')
            <div class="row mt-2">
                <div class="col-12 border-top pt-2">
                    <div class="d-flex justify-content-between flex-wrap small">
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
                    
                    <div class="border p-2 rounded bg-light small">
                        {{ $solicitud->observacion ?? 'Sin observación' }}
                    </div>
                </div>
            </div>
        </div>
            
        <!-- Footer con acciones -->
        <div class="card-footer text-end">
            <!-- Enlace para descargar el archivo PDF -->
            <a href="{{ route('baja.descargar.pdf', $solicitud->id) }}" class="btn btn-sm btn-outline-danger me-2" target="_blank">
                <i class="fa fa-file-pdf"></i> PDF
            </a>

            <!-- Enlace para descargar el archivo Excel -->
            <a href="{{ route('baja.descargar.excel', $solicitud->id) }}" class="btn btn-sm btn-outline-success" target="_blank">
                <i class="fa fa-file-excel"></i> Excel
            </a>

            <!-- Enlace para descargar los archivos adjuntos -->
            @if($solicitud->adjuntos->count() > 0)
                @foreach ($solicitud->adjuntos as $adjunto)
                <a href="{{ asset('storage/' . $adjunto->archivo) }}" class="btn btn-primary" target="_blank">
                    <i class="fa fa-download"></i> Descargar {{ basename($adjunto->archivo) }}
                </a>
                @endforeach
            @else
                <span class="text-muted ms-2">No hay archivos adjuntos.</span>
            @endif
        </div>

                    
    </div>
</div>
                