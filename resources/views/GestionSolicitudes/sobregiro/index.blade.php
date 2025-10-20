
@extends('dashboard')

@section('title', 'Sobregiro de Venta')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-copy mr-1"></i>
        <span>Solicitud de Sobregiro de Venta</span>
    </h1>
    
    @can('Sobregiro_crear')
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
            
            <table class="table table-hover table-bordered" id="solicitud_sobregiro">
                <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th class="d-none">Tipo</th>                  <!-- oculto -->
                      <th>Fecha</th>
                      <th class="d-none">Solicitante</th>                  <!-- oculto -->
                      <th>Cliente</th>
                      <th>Importe (Bs)</th>
                      <th class="d-none">Glosa</th>                  <!-- oculto -->
                      <th>Estado</th>
                      <th class="d-none">Autorizador</th>                  <!-- oculto -->
                      <th class="d-none">Fecha autorizacion</th>                  <!-- oculto -->
                      <th class="d-none">Ejecutado por</th>                  <!-- oculto -->
                      <th class="d-none">Fecha ejecucion</th>                  <!-- oculto -->
                      <th>Observacion</th>
                      <th>Codigo</th>
                      <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                  @foreach($solicitudes as $solicitud)
                    @php
                        $estado = Str::lower(trim($solicitud->estado));
                        $claseFila = '';

                        if ($estado === 'aprobada') {
                            $claseFila = 'table-primary';
                        } elseif ($estado === 'rechazada') {
                            $claseFila = 'table-danger';
                        }elseif ($estado === 'ejecutada') {
                            $claseFila = 'table-success';
                        }elseif ($estado === 'pendiente') {
                            $claseFila = 'table-warning';
                        }
                    @endphp

                    <tr class="{{ $claseFila }}">
                        <td>{{ $solicitud->id }}</td>
                        <td class="d-none">{{ ucfirst($solicitud->tipo) }}</td>      <!-- oculto -->
                        <td>{{ \Carbon\Carbon::parse($solicitud->fecha_solicitud)->format('Y-m-d') }}</td>
                        <td class="d-none">{{ $solicitud->usuario->name ?? 'N/D' }}</td>      <!-- oculto -->
                        <td>{{ $solicitud->sobregiro?->cliente ?? 'No asignado' }}</td>
                        <td>{{ $solicitud->sobregiro?->importe ? number_format($solicitud->sobregiro->importe, 2, ',', '.') : '0,00' }}</td>
                        <td class="d-none">{{ $solicitud->glosa ?? 'Sin glosa' }}</td>      <!-- oculto -->
                        <td>{{ ucfirst($estado) }}</td>
                        <td class="d-none">{{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</td>      <!-- oculto -->
                        <td>{{ $solicitud->observacion ?? 'Sin observación' }}</td>
                        <td>{{ $solicitud->sobregiro?->cod_sobregiro ?? 'N/D' }}</td>
                        <td>
                            <div class="d-flex flex-column flex-sm-row justify-content-center align-items-center">
                                
                                @if($solicitud->estado == 'pendiente')
                                            @can('Sobregiro_aprobar')
                                            <!-- Aprobar con modal -->
                                            <button class="btn btn-sm btn-success mb-2 mb-sm-0 me-sm-2 d-flex align-items-center justify-content-center"
                                                    style="width: 36px; height: 36px;"
                                                    data-bs-toggle="modal" data-bs-target="#observacionModal"
                                                    title="Aprobar"
                                                    onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }})">
                                                <i class="fa fa-check"></i>
                                            </button>
                                            @endcan
                                            @can('Sobregiro_reprobar')
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
                                @can('Sobregiro_confirmar')
                                    @if ($solicitud->estado === 'aprobada' && !$solicitud->ejecucion)
                                      <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalConfirmar{{ $solicitud->id }}">
                                          Confirmar
                                      </button>
                                    @endif
                                @endcan
                                @can('Sobregiro_ejecutar')
                                    @if ($solicitud->estado === 'confirmada' && !$solicitud->ejecucion)
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
                    @include('GestionSolicitudes.sobregiro.detalle_solicitud', ['solicitud' => $solicitud])
                    <!-- Aquí agregamos el seguimiento de la solicitud -->
                    <hr>
                    <h5 class="mt-4">Seguimiento de Solicitud</h5>
                    <!-- Seguimiento usando Livewire (o solo vista Blade) -->
                    <livewire:seguimiento-solicitud :solicitudId="$solicitud->id" />
                </div>
            </div>
        </div>
    </div>

    @include('GestionSolicitudes.sobregiro.modal_ejecutar', ['solicitud' => $solicitud])
    @include('GestionSolicitudes.sobregiro.modal_confirmar', ['solicitud' => $solicitud])

    @endforeach
 
@include('GestionSolicitudes.sobregiro.create')

<!-- Modal para Agregar Observación -->
    @include('GestionSolicitudes.sobregiro.modal_observacion')

    @include('GestionSolicitudes.sobregiro.script_observacion')

@endsection