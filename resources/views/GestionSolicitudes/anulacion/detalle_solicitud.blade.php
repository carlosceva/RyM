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
                <strong>{{ ucfirst($solicitud->tipo) }}</strong>
            </div>
        </div>

<!-- Cuerpo -->
<div class="card-body">
    <div class="row p-1">
        <div class="col-12 col-md-6 d-flex">
            <div class="fw-bold me-2">Solicitante:</div>
            <div>{{ $solicitud->usuario->name ?? 'N/D' }}</div>
        </div>
    </div>

    <div class="row p-1">
        <div class="col-12 col-md-6 d-flex">
            <div class="fw-bold me-2">Nota de venta:</div>
            <div>{{ $solicitud->anulacion->nota_venta }}</div>
        </div>
        <div class="col-12 col-md-6 d-flex mt-3 mt-md-0">
            <div class="fw-bold me-2">Almacen:</div>
            <div>{{ $solicitud->anulacion->almacen->nombre ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="row p-1">
        <div class="col-12 col-md-6 d-flex">
            <div class="fw-bold me-2">Motivo:</div>
            <div>{{ $solicitud->anulacion->motivo }}</div>
        </div>
    </div>

    <div class="row p-1">
        <div class="col-12 col-md-4 d-flex">
            <div class="fw-bold me-2">Tiene Pago:</div>
            <div>{{ is_null($solicitud->anulacion->tiene_pago) ? '---' : ($solicitud->anulacion->tiene_pago ? 'Sí' : 'No') }}</div>
        </div>
        <div class="col-12 col-md-4 d-flex">
            <div class="fw-bold me-2">Tiene despacho:</div>
            <div>{{ is_null($solicitud->anulacion->tiene_entrega) ? '---' : ($solicitud->anulacion->tiene_entrega ? 'Sí' : 'No') }}</div>
        </div>
        <div class="col-12 col-md-4 d-flex">
            <div class="fw-bold me-2">Entrega física:</div>
            <div>{{ is_null($solicitud->anulacion->entrega_fisica) ? '---' : ($solicitud->anulacion->entrega_fisica ? 'Sí' : 'No') }}</div>
        </div>
    </div>

    @if (!empty($solicitud->anulacion->obs_pago))
        <div class="row p-1">
            <div class="col-12 d-flex">
                <div class="fw-bold me-2">Observación del Pago:</div>
                <div>{{ $solicitud->anulacion->obs_pago }}</div>
            </div>
        </div>
    @endif

    <div class="row p-1">
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

                    <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center">
                                
                        @if($solicitud->estado == 'pendiente')
                                    @can('Anulacion_aprobar')
                                    <!-- Aprobar con modal -->
                                    <button class="btn btn-sm btn-success mb-2 mb-sm-0 me-sm-2 d-flex align-items-center justify-content-center"
                                            style="width: 36px; height: 36px;"
                                            data-bs-toggle="modal" data-bs-target="#observacionModal"
                                            title="Aprobar"
                                            onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }})">
                                        <i class="fa fa-check"></i>
                                    </button>
                                    @endcan
                                    @can('Anulacion_reprobar')
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

                        @if ($solicitud->estado === 'aprobada' && !$solicitud->ejecucion)
                            @php
                                $anulacion = $solicitud->anulacion;
                                $tienePago = $anulacion->tiene_pago;
                                $tieneEntrega = $anulacion->tiene_entrega;
                                $entregaFisica = $anulacion->entrega_fisica;
                            @endphp

                            @if ($tienePago)
                                @can('Anulacion_ejecutar')
                                    <!-- Si hay pago, se puede ejecutar directamente como devolución -->
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                        Ejecutar
                                    </button>
                                    &nbsp;
                                @endcan
                            @else
                                {{-- No hay pago --}}
                                @if (is_null($tieneEntrega))
                                    {{-- Aún no se ha verificado entrega --}}
                                    @can('Anulacion_entrega')
                                        @can('Anulacion_ejecutar')
                                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEntrega{{ $solicitud->id }}">
                                                Confirmar Despacho
                                            </button>
                                            &nbsp;
                                        @endcan
                                    @endcan
                                @elseif($tieneEntrega)
                                    {{-- Ya se confirmó que sí hubo entrega → ejecutar como devolución --}}
                                    @can('Anulacion_ejecutar')
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                            Ejecutar
                                        </button>
                                        &nbsp;
                                    @endcan
                                @else
                                    {{-- Se indicó que NO hubo entrega --}}
                                    @if (is_null($entregaFisica))
                                        {{-- Aún no confirmado por almacén --}}
                                        @can('Anulacion_entrega')
                                            @cannot('Anulacion_ejecutar')
                                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEntregaF{{ $solicitud->id }}">
                                                    Registrar Entrega
                                                </button>
                                                &nbsp;
                                            @endcannot
                                        @endcan

                                        @can('Anulacion_ejecutar')
                                            <button class="btn btn-outline-secondary btn-sm" disabled style="background-color: white;">
                                                ⏳ Esperando confirmación
                                            </button>
                                            &nbsp;
                                        @endcan
                                    @elseif($entregaFisica === false || $entregaFisica === 0)
                                        {{-- Almacén confirmó que NO hubo entrega → ejecutar como anulación --}}
                                        @can('Anulacion_ejecutar')
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                                Ejecutar
                                            </button>
                                            &nbsp;
                                        @endcan
                                    @elseif($entregaFisica === true || $entregaFisica === 1)
                                        {{-- Almacén corrigió y dijo que SÍ hubo entrega --}}
                                        @can('Anulacion_ejecutar')
                                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                                Ejecutar
                                            </button>
                                            &nbsp;
                                        @endcan
                                    @endif
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
                @endcan
                <!-- Columna derecha -->
                <div class="col-auto ms-auto d-flex align-items-center">
                    <a href="{{ route('anulacion.descargar.pdf', $solicitud->id) }}" class="btn btn-sm btn-pdf me-2" target="_blank">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <!--
                    <a href="{{ route('anulacion.descargar.excel', $solicitud->id) }}" class="btn btn-sm btn-excel" target="_blank">
                        <i class="fa fa-file-excel me-1"></i> Excel
                    </a>
                    -->
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
                