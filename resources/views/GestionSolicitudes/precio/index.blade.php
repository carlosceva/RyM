
@extends('dashboard')

@section('title', 'Precio especial de venta')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-copy mr-1"></i>
        <span>Solicitud de Precio Especial</span>
    </h1>
    
    @can('Precio_especial_crear')
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
            <table class="table table-hover table-bordered" id="solicitud_precio">
                <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Cliente</th>
                      <th>Productos</th>
                      <th>Fecha</th>
                      <th>Estado</th>
                      <th>Observacion</th>
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
                        }
                    @endphp

                    <tr class="{{ $claseFila }}">
                        <td>{{ $solicitud->id }}</td>
                        <td>{{ $solicitud->precioEspecial?->cliente ?? 'No asignado' }}</td>
                        <td>{{ $solicitud->precioEspecial?->detalle_productos ?? 'Sin detalle de productos' }}</td>
                        <td>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($estado) }}</td>
                        <td>{{ $solicitud->observacion ?? 'Sin observación' }}</td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center">
                                
                                @if($solicitud->estado == 'pendiente')
                                            @can('Precio_especial_aprobar')
                                            <!-- Aprobar con modal -->
                                            <button class="btn btn-sm btn-success mb-2 mb-sm-0 me-sm-2 d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px;"
                                                    data-bs-toggle="modal" data-bs-target="#observacionModal"
                                                    title="Aprobar"
                                                    onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }})">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            @endcan
                                            @can('Precio_especial_reprobar')
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí va TODO el contenido de tu ticket -->
                    @include('GestionSolicitudes.precio.detalle_solicitud', ['solicitud' => $solicitud])
                </div>
            </div>
        </div>
    </div>
    @endforeach
 
    <!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Precio Especial</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('PrecioEspecial.store') }}" method="POST">
          @csrf
          <!-- Tipo de Solicitud -->
          <input type="hidden" name="tipo" value="precio_especial">

          <!-- Usuario que solicita (Oculto porque es el usuario autenticado) -->
        <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">

          <!-- Fecha de solicitud (Generada automáticamente) -->
        <div class="mb-3">
            <label for="fecha_solicitud" class="form-label">Fecha de Solicitud</label>
            <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
        </div>

          <!-- Estado (Se define automáticamente como pendiente) -->
        <input type="hidden" name="estado" value="pendiente">

          <!-- Glosa (Descripción o motivo de la solicitud) -->
        <div class="mb-3">
            <label for="glosa" class="form-label">Motivo de la solicitud</label>
            <textarea class="form-control" id="glosa" name="glosa" rows="3" required></textarea>
        </div>

          <!-- Cliente -->
        <div class="mb-3">
            <label for="cliente" class="form-label">Cliente</label>
            <input type="text" class="form-control" id="cliente" name="cliente">
            
        </div>

          <!-- Detalle Productos (puede ser un campo JSON o similar) -->
          <div class="mb-3">
            <label for="detalle_productos" class="form-label">Detalle de Productos</label>
            <textarea name="detalle_productos" id="detalle_productos" class="form-control" rows="3" required></textarea>
          </div>

          <div class="modal-footer">
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
        <form id="formObservacion" action="{{ route('precioespecial.aprobar_o_rechazar') }}" method="POST">
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
    function setAccionAndSolicitudId(accion, solicitudId) {
        // Asigna la acción al campo oculto 'accion'
        document.getElementById('accion').value = accion;
        // Asigna la ID de la solicitud al campo oculto 'solicitud_id'
        document.getElementById('solicitud_id').value = solicitudId;
    }
</script>

@endsection