<!-- Ticket -->
<div class="col">
    <div class="card shadow-sm h-100 ">
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
            }elseif($solicitud->estado === 'confirmada'){ 
                    $clase_color = 'bg-primary'; 
            }
            
        @endphp
        <!-- Header -->
        <div class="card-header {{ $clase_color }}">
            <!-- Fila 1: ID y Fecha -->
            <div class="d-flex justify-content-between">
                <span>#{{ $solicitud->id }}</span>
                <span>{{ $solicitud->fecha_solicitud }}</span>
            </div>
            <!-- Fila 2: Tipo de solicitud centrado -->
            <div class="text-center mt-1">
                <strong>Solicitud de Extras</strong>
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
                    <p class="mb-1"><strong>Tipo de Extra:</strong> {{ $solicitud->extra->tipo_extra ?? 'No asignado' }}</p>
                </div>
            </div>

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
        <div class="card-footer {{ $clase_color }}">
            <div class="row">
                
                <!-- Columna izquierda -->
                <div class="col d-flex align-items-center">
                    @can('Sobregiro_borrar')
                    <form action="{{ route('Extras.destroy', $solicitud->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas anular esta solicitud?');" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-pdf me-2">
                            <i class="fa fa-trash"></i> Anular solicitud
                        </button>
                    </form>
                    @endcan
                    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center">
                            
                            @if($solicitud->estado == 'pendiente')
                                        @can('Extra_aprobar')
                                        <!-- Aprobar con modal -->
                                        <button class="btn btn-sm btn-success mb-2 mb-sm-0 me-sm-2 d-flex align-items-center justify-content-center"
                                                style="width: 36px; height: 36px;"
                                                data-bs-toggle="modal" data-bs-target="#observacionModal"
                                                title="Aprobar"
                                                onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }})">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        @endcan
                                        @can('Extra_reprobar')
                                        <!-- Rechazar con modal -->
                                        <button class="btn btn-sm btn-danger mb-2 mb-sm-0 me-sm-2 d-flex align-items-center justify-content-center"
                                                style="width: 36px; height: 36px;"
                                                data-bs-toggle="modal" data-bs-target="#observacionModal"
                                                title="Rechazar"
                                                onclick="setAccionAndSolicitudId('rechazar', {{ $solicitud->id }})">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        @endcan
                            @endif
                            @can('Extra_confirmar')
                                @if ($solicitud->estado === 'aprobada' && !$solicitud->ejecucion)
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmar{{ $solicitud->id }}">
                                        Confirmar
                                    </button>
                                @endif
                            @endcan
                            @can('Extra_ejecutar')
                                @if ($solicitud->estado === 'confirmada' && !$solicitud->ejecucion)
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                        Ejecutar
                                    </button>
                                @endif
                            @endcan
                            &nbsp;
                    </div>
                </div>
                
                <!-- Columna derecha -->
                <div class="col-auto ms-auto d-flex align-items-center">
                    <a href="{{ route('extra.descargar.pdf', $solicitud->id) }}" class="btn btn-sm btn-pdf me-2" target="_blank">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <!--<a href="{{ route('sobregiro.descargar.excel', $solicitud->id) }}" class="btn btn-sm btn-excel" target="_blank">
                        <i class="fa fa-file-excel me-1"></i> Excel
                    </a>-->
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
                