@extends('dashboard')

@section('title', 'Precio especial de venta')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-copy mr-1"></i>
        <span>Solicitudes</span>
    </h1>
    
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-toggle="modal" data-target="#modalNuevaSolicitud" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; Agregar</a>
        </div> 
    </div>
                
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
            <table class="table table-hover table-bordered" id="solicitud">
                <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th>Cliente</th>
                      <th>Productos</th>
                      <th>Estado</th>
                      <th>Fecha Solicitud</th>
                      <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                  
                    @foreach($solicitudes as $solicitud)
                    <tr>
                        <td>{{ $solicitud->id }}</td>
                        <td>{{ $solicitud->precioEspecial?->cliente?->name ?? 'Sin cliente asignado' }}</td>
                        <td>{{ $solicitud->precioEspecial?->detalle_productos ?? 'Sin detalle de productos' }}</td>
                        <td>{{ ucfirst($solicitud->estado) }}</td>
                        <td>{{ $solicitud->fecha_solicitud }}</td>
                        <td>
                            @can('approve-solicitud')
                            @if($solicitud->estado == 'pendiente')
                                <form action="{{ route('solicitudes.aprobar', $solicitud->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">Aprobar</button>
                                </form>
                            @endif
                            @endcan
                            <div class="flex space-x-2">
                              <button class="btn btn-success">
                                  Aceptar
                              </button>

                              <!-- Botón Rechazar -->
                              <button class="btn btn-danger">
                                  Rechazar
                              </button>
                          </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
 
    <!-- Modal para Nueva Solicitud -->
    <!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Precio Especial</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('solicitudes.store') }}" method="POST">
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
            <label for="id_cliente" class="form-label">Cliente</label>
            <select class="form-control" id="id_cliente" name="id_cliente" required>
                <option value="">Seleccione un cliente</option>
                @foreach ($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->name }}</option>
                @endforeach
            </select>
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
@endsection