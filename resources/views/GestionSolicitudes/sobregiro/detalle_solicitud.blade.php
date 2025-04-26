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
            <div class="row  p-2 ">
                <div class="col-12 col-md-6 ">
                    <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
                </div>
            </div>

            <div class="row  p-2 ">
                <div class="col-12 col-md-6">
                    <p class="mb-1"><strong>Cliente:</strong> {{ $solicitud->sobregiro->cliente ?? 'No asignado' }}</p>
                </div>

                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <p class="mb-2"><strong>Importe: </strong>{{ $solicitud->sobregiro->importe ?? '0.0' }}</p>
                </div>
            </div>

            <div class="row p-2">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <strong class="me-2">Motivo:</strong>
                        <div class="border p-2 rounded bg-light small flex-grow-1">
                            {{ $solicitud->glosa ?? 'Sin glosa' }}
                        </div>
                    </div>
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
             @if($solicitud->estado ==='ejecutada')
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
            
        <style>
            /* Estilo para el botón Excel (estado normal) */
            .btn-excel {
                background-color: white !important;  /* Fondo blanco */
                color: #198754 !important;           /* Texto verde */
                border: 1px solid #198754 !important;/* Borde verde */
            }

            /* Estilo cuando pasa el mouse (hover) */
            .btn-excel:hover {
                background-color: #145a32 !important; /* Verde más oscuro */
                color: white !important;              /* Texto blanco */
            }

            /* Estilo para el icono del botón Excel (verde por defecto) */
            .btn-excel i {
                color: #198754 !important; /* Icono verde */
            }

            /* Cuando pasa el mouse, el icono se pone blanco */
            .btn-excel:hover i {
                color: white !important; /* Icono blanco en hover */
            }

            /* Fondo de los botones en el footer dependiendo del estado */
            .footer-aprobada {
                background-color: #198754 !important; /* Verde para aprobado */
            }

            .footer-rechazada {
                background-color: #dc3545 !important; /* Rojo para rechazada */
            }
        </style>


        <!-- Footer con acciones -->
        <div class="card-footer text-end 
            @if($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') 
                footer-aprobada 
            @elseif($solicitud->estado === 'rechazada') 
                footer-rechazada 
            @endif">
            
            <a href="{{ route('sobregiro.descargar.pdf', $solicitud->id) }}" 
            class="btn btn-sm btn-outline-danger me-2 bg-danger" 
            target="_blank">
                <i class="fa fa-file-pdf"></i> PDF
            </a>

            <a href="{{ route('sobregiro.descargar.excel', $solicitud->id) }}" 
            class="btn btn-sm btn-excel" 
            target="_blank">
                <i class="fa fa-file-excel me-1"></i> Excel
            </a>
        </div>
        <!-- Botón "Cerrar" para el modal -->
         <div class="card-footer text-end">
            <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">
                Cerrar
            </button>
        </div>
    </div>
</div>
                