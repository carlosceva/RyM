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
            <div class="row p-2">
                <div class="d-flex align-items-center">
                    <strong class="me-2 col-md-2">Solicitante:</strong>
                    <div class="col-md-10 border p-2 rounded bg-light small flex-grow-1">
                        {{ $solicitud->usuario->name ?? 'N/D' }}
                    </div>
                </div>
            </div>

            <div class="row  p-2 ">
                <div class="col-6 d-flex align-items-center">
                    <strong class="me-2 col-md-4"># Nota:</strong>
                    <div class="col-md-8 border p-2 rounded bg-light small flex-grow-1">
                        {{ $solicitud->devolucion->nota_venta ?? 'N/D'}}
                    </div>
                </div>

                <div class="col-6 d-flex align-items-center">
                    <strong class="me-2 col-md-4">Almacen:</strong>
                    <div class="col-md-8 border p-2 rounded bg-light small flex-grow-1">
                        @if(is_numeric($solicitud->devolucion->almacen)) 
                            {{ \App\Models\Almacen::find($solicitud->devolucion->almacen)->nombre ?? 'No definido' }}
                        @else
                            {{ $solicitud->devolucion->almacen ?? 'No definido' }}
                        @endif
                    </div>
                </div>
            </div>

            <div class="row p-2">
                <div class="col-12 col-md-4">
                    <p class="mb-1">
                        <strong>Tiene Pago:</strong>
                        @if($solicitud->devolucion)
                            {{ is_null($solicitud->devolucion->tiene_pago) ? '---' : ($solicitud->devolucion->tiene_pago ? 'Sí' : 'No') }}
                        @else
                            ---
                        @endif
                    </p>
                </div>

                <div class="col-12 col-md-4">
                    <p class="mb-1">
                        <strong>Tiene despacho:</strong>
                        @if($solicitud->devolucion)
                            {{ is_null($solicitud->devolucion->tiene_entrega) ? '---' : ($solicitud->devolucion->tiene_entrega ? 'Sí' : 'No') }}
                        @else
                            ---
                        @endif
                    </p>
                </div>

                <div class="col-12 col-md-4">
                    <p class="mb-1">
                        <strong>Entrega física:</strong>
                        @if($solicitud->devolucion)
                            {{ is_null($solicitud->devolucion->entrega_fisica) ? '---' : ($solicitud->devolucion->entrega_fisica ? 'Sí' : 'No') }}
                        @else
                            ---
                        @endif
                    </p>
                </div>

            </div>
            <!-- Mostrar Observación de Pago solo si existe -->
            @if (!empty($solicitud->devolucion->obs_pago))
                <div class="col-12">
                    <p class="mb-1">
                        <strong>Observación del Pago:</strong>
                        {{ $solicitud->devolucion->obs_pago }}
                    </p>
                </div>
            @endif

            <div class="row p-2">
                <div class="d-flex align-items-center">
                    <strong class="me-2 col-md-2">Motivo:</strong>
                    <div class="col-md-10 border p-2 rounded bg-light small flex-grow-1">
                        {{ $solicitud->devolucion->motivo ?? 'N/D'}}
                    </div>
                </div>
            </div>

            <div class="row p-2">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <strong class="me-2 col-md-2">Glosa:</strong>
                        <div class="col-md-10 border p-2 rounded bg-light small flex-grow-1">
                            {{ $solicitud->glosa ?? 'Sin glosa' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- detalle productos -->
            <div class="col-12 mt-3 mt-md-0">
                @if($solicitud->devolucion)
                <strong>Detalle de productos:</strong>
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
                            @if(!empty($solicitud->devolucion->detalle_productos))
                                @php $productos = explode(',', $solicitud->devolucion->detalle_productos); @endphp
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
            @if($solicitud->estado !== 'rechazada' && $solicitud->estado !== 'convertida')
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
                @can('Devolucion_borrar')
                <!-- Columna izquierda -->
                <div class="col d-flex align-items-center">
                    <form action="{{ route('Devolucion.destroy', $solicitud->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas anular esta solicitud?');" class="m-0">
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
                    <a href="{{ route('devolucion.descargar.pdf', $solicitud->id) }}" class="btn btn-sm btn-pdf me-2" target="_blank">
                        <i class="fa fa-file-pdf"></i> PDF
                    </a>
                    <!--
                    <a href="{{ route('devolucion.descargar.excel', $solicitud->id) }}" class="btn btn-sm btn-excel" target="_blank">
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
                