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
                    <p class="mb-1"><strong>Nota de venta:</strong> {{ $solicitud->anulacion->nota_venta }}</p>
                </div>

                <div class="col-12 col-md-6 mt-3 mt-md-0">
                    <p class="mb-2"><strong>Motivo: </strong>{{ $solicitud->anulacion->motivo }}</p>
                </div>
            </div>

            <div class="row p-2">
                <div class="col-12 col-md-4">
                    <p class="mb-1">
                        <strong>Tiene Pago:</strong>
                        {{ is_null($solicitud->anulacion->tiene_pago) ? '---' : ($solicitud->anulacion->tiene_pago ? 'Sí' : 'No') }}
                    </p>
                </div>
                <div class="col-12 col-md-4">
                    <p class="mb-1">
                        <strong>Tiene despacho:</strong>
                        {{ is_null($solicitud->anulacion->tiene_entrega) ? '---' : ($solicitud->anulacion->tiene_entrega ? 'Sí' : 'No') }}
                    </p>
                </div>
                <div class="col-12 col-md-4">
                    <p class="mb-1">
                        <strong>Entrega fisica:</strong>
                        {{ is_null($solicitud->anulacion->entrega_fisica) ? '---' : ($solicitud->anulacion->entrega_fisica ? 'Sí' : 'No') }}
                    </p>
                </div>
            </div>
            <!-- Mostrar Observación de Pago solo si existe -->
            @if (!empty($solicitud->anulacion->obs_pago))
                <div class="col-12">
                    <p class="mb-1">
                        <strong>Observación del Pago:</strong>
                        {{ $solicitud->anulacion->obs_pago }}
                    </p>
                </div>
            @endif

            <div class="row p-2">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <strong class="me-2">Glosa:</strong>
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
            
        <style>
            /* Estilo para el botón Excel (estado normal) */
            .btn-excel {
                background-color: white !important;  /* Fondo blanco */
                color: #198754 !important;           /* Texto verde */
                border: 1px solid #198754 !important;/* Borde verde */
                transition: color 0.3s ease, background-color 0.3s ease; /* Sincroniza transición */
            }

            /* Estilo cuando pasa el mouse (hover) */
            .btn-excel:hover {
                background-color: #145a32 !important; /* Verde más oscuro */
                color: white !important;              /* Texto blanco */
            }

            /* Estilo para el icono del botón Excel (verde por defecto) */
            .btn-excel i {
                color: #198754 !important; /* Icono verde */
                transition: color 0.3s ease; /* Sincroniza la transición del color del icono */
            }

            /* Cuando pasa el mouse, el icono se pone blanco */
            .btn-excel:hover i {
                color: white !important; /* Icono blanco en hover */
            }

            /* Estilo para el botón PDF (estado normal) */
            .btn-pdf {
                background-color: white !important;  /* Fondo blanco */
                color: #dc3545 !important;           /* Texto rojo */
                border: 1px solid #dc3545 !important;/* Borde rojo */
                transition: color 0.3s ease, background-color 0.3s ease; /* Sincroniza transición */
            }

            /* Estilo cuando pasa el mouse (hover) */
            .btn-pdf:hover {
                background-color: #c82333 !important; /* Rojo más oscuro */
                color: white !important;              /* Texto blanco */
            }

            /* Estilo para el icono del botón PDF (rojo por defecto) */
            .btn-pdf i {
                color: #dc3545 !important; /* Icono rojo */
                transition: color 0.3s ease; /* Sincroniza la transición del color del icono */
            }

            /* Cuando pasa el mouse, el icono se pone blanco */
            .btn-pdf:hover i {
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
        <div class="card-footer @if($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') 
                                    footer-aprobada 
                                @elseif($solicitud->estado === 'rechazada') 
                                    footer-rechazada 
                                @endif">
            <div class="row">
                @can('Anulacion_borrar')
                <!-- Columna izquierda -->
                <div class="col d-flex align-items-center">
                    <form action="{{ route('Anulacion.destroy', $solicitud->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas anular esta solicitud?');" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-pdf me-2">
                            <i class="fa fa-trash"></i> Anular solicitud
                        </button>
                    </form>
                </div>
                @endcan
                <!-- Columna derecha -->
                <div class="col-auto ms-auto d-flex align-items-center">
                    <a href="{{ route('anulacion.descargar.pdf', $solicitud->id) }}" class="btn btn-sm btn-pdf me-2" target="_blank">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('anulacion.descargar.excel', $solicitud->id) }}" class="btn btn-sm btn-excel" target="_blank">
                        <i class="fa fa-file-excel me-1"></i> Excel
                    </a>
                </div>
            </div>    
        </div>

    </div>
    <!-- Botón "Cerrar" para el modal -->
    <div class=" text-end">
        <button type="button" class="btn btn-dark" data-bs-dismiss="modal" aria-label="Close">
            Cerrar
        </button>
    </div>
</div>
                