<!-- Ticket -->
<div class="col">
    <div class="card shadow-sm h-100 ">
        <!-- Header -->
        <div class="card-header @if($solicitud->estado === 'aprobada') 
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
                    <p class="mb-1"><strong>Solicitante:</strong></p>
                    <p class="mb-2">{{ $solicitud->usuario->name ?? 'N/D' }}</p>

                    <p><strong>Motivo:</strong></p>
                    <div class="border p-2 rounded bg-light small">
                        {{ $solicitud->glosa ?? 'Sin glosa' }}
                    </div>
                </div>

                <!-- Columna derecha -->
                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    @if($solicitud->precioEspecial)
                    <p class="mb-2"><strong>{{ $solicitud->precioEspecial->cliente ?? 'Sin cliente' }}</strong></p>
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
                                @if(!empty($solicitud->precioEspecial->detalle_productos))
                                    @php $productos = explode(',', $solicitud->precioEspecial->detalle_productos); @endphp
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
                        <span><strong>{{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</strong></span>
                        <span class="badge bg-{{ $solicitud->estado === 'aprobada' ? 'success' : ($solicitud->estado === 'rechazada' ? 'danger' : 'warning') }}">
                            {{ ucfirst($solicitud->estado) }}
                        </span>
                        <span>{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                    </div>
                </div>
            </div>

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
            <a href="{{ route('precioEspecial.descargar.pdf', $solicitud->id) }}" class="btn btn-sm btn-outline-danger me-2" target="_blank">
                <i class="fa fa-file-pdf"></i> PDF
            </a>

            <a href="{{ route('precioEspecial.descargar.excel', $solicitud->id) }}" class="btn btn-sm btn-outline-success" target="_blank">
                <i class="fa fa-file-excel"></i> Excel
            </a>
        </div>
                    
    </div>
</div>
                