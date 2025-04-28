
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
            <table class="table table-hover table-bordered" id="solicitud_devolucion">
                <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Nota</th>
                      <th>Motivo</th>
                      <th>Almacen</th>
                      <th>Productos</th>
                      <th>Fecha</th>
                      <th>Estado</th>
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
                        <td>{{ $solicitud->devolucion->nota_venta }}</td>
                        <td>{{ $solicitud->devolucion->motivo }}</td>
                        <td>{{ $solicitud->devolucion->almacen }}</td>
                        <td>{{ $solicitud->devolucion->detalle_productos }}</td>
                        <td>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($estado) }}</td>
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
                                @can('Devolucion_editar')
                                @if ($solicitud->estado === 'aprobada' && !$solicitud->ejecucion)
                                      <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                          Ejecutar
                                      </button>
                                  @endif
                                @endcan
                                &nbsp;

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

    <!-- Modal para ejecutar solicitud -->
    <div class="modal fade" id="modalEjecutar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <form action="{{ route('devolucion.ejecutar', $solicitud->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-header">
                <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Confirmar Ejecución</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <p>¿Está seguro de registrar esta acción?</p>
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
 
    <!-- Vista para crear solicitud -->
    <div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
        <div class="modal-dialog"> <!-- Aumenté tamaño del modal para mejor distribución -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Devolución de Venta</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('Devolucion.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                        @csrf
                        <!-- Campos ocultos -->
                        <input type="hidden" name="tipo" value="Devolucion de Venta">
                        <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">
                        <input type="hidden" name="estado" value="pendiente">

                        <!-- Fecha de solicitud (solo visual) -->
                        <div class="row mb-2 align-items-center">
                            <label for="fecha_solicitud" class="col-md-4 col-form-label">Fecha de Solicitud</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                            </div>
                        </div>

                        <!-- Nota de venta -->
                        <div class="row mb-2 align-items-center">
                            <label for="nota_venta" class="col-md-4 col-form-label">Nota de venta</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="nota_venta" name="nota_venta" required>
                            </div>
                        </div>

                        <!-- Almacén -->
                        <div class="row mb-2 align-items-center">
                            <label for="almacen" class="col-md-4 col-form-label">Almacén</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="almacen" name="almacen" required>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="row mb-2 align-items-center">
                            <label for="motivo" class="col-md-4 col-form-label">Motivo</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="motivo" name="motivo" required>
                            </div>
                        </div>

                        <!-- Detalle Productos -->
                        <div class="row mb-2">
                            <label for="detalle_productos" class="col-md-4 col-form-label">Detalle de Productos</label>
                            <div class="col-md-8">
                                <textarea name="detalle_productos" id="detalle_productos" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Glosa -->
                        <div class="row mb-2">
                            <label for="glosa" class="col-md-4 col-form-label">Glosa</label>
                            <div class="col-md-8">
                                <textarea class="form-control" id="glosa" name="glosa" rows="2"></textarea>
                            </div>
                        </div>

                        <!-- Checkboxes: Requiere abono / Tiene entrega -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="requiere_abono" name="requiere_abono" value="1">
                                    <label class="form-check-label" for="requiere_abono">
                                        Requiere abono
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="tiene_entrega" name="tiene_entrega" value="1">
                                    <label class="form-check-label" for="tiene_entrega">
                                        Tiene entrega
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="modal-footer px-0">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Crear Solicitud</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


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