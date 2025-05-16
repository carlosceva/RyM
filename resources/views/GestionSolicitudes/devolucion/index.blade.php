
@extends('dashboard')

@section('title', 'Devolucion de Venta')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-copy mr-1"></i>
        <span>Solicitud de Devolucion de Venta</span>
    </h1>
    
    @can('Devolucion_crear')
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-toggle="modal" data-target="#modalNuevaSolicitud" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; Agregar</a>
        </div> 
    </div>
    @endcan            
</div>
    
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <div class="card table-responsive">
        <div class="card-body">
            <div class="mb-3 d-flex align-items-center gap-3">
                <label for="fechaInicio" class="mb-0">Desde:</label>
                <input type="date" id="fechaInicio" class="form-control" style="max-width: 200px;">
                
                <label for="fechaFin" class="mb-0 ms-3">Hasta:</label>
                <input type="date" id="fechaFin" class="form-control" style="max-width: 200px;">
            </div>
            <table class="table table-hover table-bordered" id="solicitud_devolucion">
                <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th class="d-none">Tipo</th>                  <!-- oculto -->
                      <th>Fecha</th>
                      <th class="d-none">Solicitante</th>                  <!-- oculto -->
                      <th>Nota</th>
                      <th>Almacen</th>
                      <th>Motivo</th>
                      <th class="d-none">Glosa</th>                  <!-- oculto -->
                      <th class="d-none">Pago</th>                  <!-- oculto -->
                      <th class="d-none">Entrega</th>                  <!-- oculto -->
                      <th>Productos</th>
                      <th>Estado</th>
                      <th class="d-none">Autorizador</th>                  <!-- oculto -->
                      <th class="d-none">Fecha autorizado</th>                  <!-- oculto -->
                      <th class="d-none">Ejecutado por</th>                  <!-- oculto -->
                      <th class="d-none">Fecha ejecucion</th>                  <!-- oculto -->
                      <th class="d-none">Observacion</th>                  <!-- oculto -->
                      <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                  @foreach($solicitudes as $solicitud)
                    @php
                        $estado = Str::lower(trim($solicitud->estado));
                        $claseFila = '';

                        if ($estado === 'aprobada') {
                            $claseFila = 'table-success';
                        } elseif ($estado === 'rechazada') {
                            $claseFila = 'table-danger';
                        }elseif ($estado === 'ejecutada') {
                            $claseFila = 'table-success';
                        }
                    @endphp

                    <tr class="{{ $claseFila }}">
                        <td>{{ $solicitud->id }}</td>
                        <td class="d-none">{{ ucfirst($solicitud->tipo) }}</td>      <!-- oculto -->
                        <td>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('Y-m-d') }}</td>
                        <td class="d-none">{{ $solicitud->usuario->name ?? 'N/D' }}</td>      <!-- oculto -->
                        <td>{{ $solicitud->devolucion->nota_venta }}</td>
                        <td>{{ $solicitud->devolucion->almacen }}</td>
                        <td>{{ $solicitud->devolucion->motivo }}</td>
                        <td class="d-none">{{ $solicitud->glosa ?? 'Sin glosa' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->devolucion->requiere_abono ? 'Sí' : 'No' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->devolucion->tiene_entrega ? 'Sí' : 'No' }}</td>       <!-- oculto -->
                        <td>{{ $solicitud->devolucion->detalle_productos }}</td>
                        <td>{{ ucfirst($estado) }}</td>
                        <td class="d-none">{{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->observacion ?? 'Sin observación' }}</td>       <!-- oculto -->
                        <td>
                            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center">
                                
                                @if($solicitud->estado == 'pendiente')
                                            @can('Devolucion_aprobar')
                                            <!-- Aprobar con modal -->
                                            <button class="btn btn-sm btn-success mb-2 mb-sm-0 me-sm-2 d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px;"
                                                    data-bs-toggle="modal" data-bs-target="#observacionModal"
                                                    title="Aprobar"
                                                    onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }})">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            @endcan
                                            @can('Devolucion_reprobar')
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
                                    @can('Devolucion_ejecutar')
                                        @if($solicitud->devolucion->tiene_pago !== null && $solicitud->devolucion->tiene_entrega !== null)
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                                Ejecutar
                                            </button>
                                            &nbsp;
                                        @endif
                                    @endcan
                                    @can('Devolucion_entrega')
                                        @if($solicitud->devolucion->tiene_entrega === null)
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEntrega{{ $solicitud->id }}">
                                                Verificar Entrega
                                            </button>
                                            &nbsp;
                                        @endif
                                    @endcan
                                    @can('Devolucion_pago')
                                        @if($solicitud->devolucion->tiene_pago === null)
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalPago{{ $solicitud->id }}">
                                                Verificar Pago
                                            </button>
                                            &nbsp;
                                        @endif
                                    @endcan
                                @endif

                              <!-- Botón para ver detalles en formato ticket -->
                              <button class="btn btn-sm btn-primary d-flex align-items-center justify-content-center"
                                      style="width: 36px; height: 36px;"
                                      data-bs-toggle="modal" data-bs-target="#modalTicket{{ $solicitud->id }}"
                                      title="Ver detalles">
                                  <i class="fa fa-list-ul"></i>
                              </button>
                            </div>
                        </td>
                    </tr>
                  @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @foreach($solicitudes as $solicitud)
    <!-- Modal Ticket -->
    <div class="modal fade" id="modalTicket{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalTicketLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTicketLabel{{ $solicitud->id }}">Detalle de Solicitud #{{ $solicitud->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí va TODO el contenido de tu ticket -->
                    @include('GestionSolicitudes.devolucion.detalle_solicitud', ['solicitud' => $solicitud])
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para verificar pago solicitud -->
    <div class="modal fade" id="modalPago{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Verificar Pago</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('solicitud.devolucion.verificarPago', $solicitud->id) }}">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">¿Tiene pago registrado?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pago" id="pagoSi{{ $solicitud->id }}" value="1">
                                <label class="form-check-label" for="pagoSi{{ $solicitud->id }}">Sí</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="pago" id="pagoNo{{ $solicitud->id }}" value="0">
                                <label class="form-check-label" for="pagoNo{{ $solicitud->id }}">No</label>
                            </div>
                        </div>

                        <div id="resultado{{ $solicitud->id }}" class="alert d-none fw-bold text-center"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirmar verificación</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para verificar entrega solicitud -->
    <div class="modal fade" id="modalEntrega{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Verificar Entrega</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('solicitud.devolucion.verificarEntrega', $solicitud->id) }}">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">¿Tiene entrega registrada en sistema?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="entrega" id="entregaSi{{ $solicitud->id }}" value="1">
                                <label class="form-check-label" for="entregaSi{{ $solicitud->id }}">Sí</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="entrega" id="entregaNo{{ $solicitud->id }}" value="0">
                                <label class="form-check-label" for="entregaNo{{ $solicitud->id }}">No</label>
                            </div>
                        </div>

                        <div id="resultado{{ $solicitud->id }}" class="alert d-none fw-bold text-center"></div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirmar verificación</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para ejecutar solicitud -->
    <div class="modal fade" id="modalEjecutar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <form action="{{ route('devolucion.ejecutar', $solicitud->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Confirmar Ejecución</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        @php
                            $devolucion = $solicitud->devolucion;
                            $tienePago = $devolucion?->tiene_pago;
                            $tieneEntrega = $devolucion?->tiene_entrega;
                        @endphp

                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tiene Pago:
                                <span class="fw-bold {{ $tienePago ? 'text-success' : 'text-danger' }}">
                                    {{ $tienePago ? 'Sí' : 'No' }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Tiene entrega:
                                <span class="fw-bold {{ $tieneEntrega ? 'text-success' : 'text-danger' }}">
                                    {{ $tieneEntrega ? 'Sí' : 'No' }}
                                </span>
                            </li>
                        </ul>

                        @if (!$tienePago && !$tieneEntrega)
                            <div class="alert alert-danger fw-bold text-center">
                                Se procederá como <u>anulación</u>.
                            </div>
                        @else
                            <div class="alert alert-warning fw-bold text-center">
                                Se procederá como <u>devolución</u>.
                            </div>
                        @endif

                        <p class="text-center mt-2">¿Está seguro de ejecutar esta acción?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Ejecutar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
 
    @include('GestionSolicitudes.devolucion.create')

    <!-- Modal para Agregar Observación -->
    <div class="modal fade" id="observacionModal" tabindex="-1" aria-labelledby="observacionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="observacionModalLabel">Agregar Observación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formObservacion" action="{{ route('devolucion.aprobar_o_rechazar') }}" method="POST">
                @csrf
                <!-- Campo oculto para la solicitud_id -->
                <input type="hidden" name="solicitud_id" id="solicitud_id" value="">
                <input type="hidden" name="accion" id="accion" value="">

                <div class="mb-3">
                    <label for="observacion" class="form-label">Observación</label>
                    <textarea name="observacion" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary ms-2">Aceptar</button>
                </div>
                </form>
            </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.querySelector('form');
        form.addEventListener('submit', function () {
            form.querySelector('button[type=submit]').disabled = true;
        });
    </script>

    <script>
        function setAccionAndSolicitudId(accion, solicitudId) {
            // Asigna la acción al campo oculto 'accion'
            document.getElementById('accion').value = accion;
            // Asigna la ID de la solicitud al campo oculto 'solicitud_id'
            document.getElementById('solicitud_id').value = solicitudId;
        }
    </script>

@endsection