
@extends('dashboard')

@section('title', 'Anulacion de Venta')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-copy mr-1"></i>
        <span>Solicitud de Anulacion de Venta</span>
    </h1>
    
    @can('Anulacion_crear')
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
            
            <table class="table table-hover table-bordered" id="solicitud_anulacion">
                <thead class="table-dark">
                    <tr>
                      <th>#</th>
                      <th class="d-none">Tipo</th>                  <!-- oculto -->
                      <th>Fecha</th>
                      <th class="d-none">Solicitante</th>                  <!-- oculto -->
                      <th># Nota</th>
                      <th>Motivo</th>
                      <th class="d-none">Glosa</th>                  <!-- oculto -->
                      <th>Estado</th>
                      <th class="d-none">Autorizador</th>                  <!-- oculto -->
                      <th class="d-none">Fecha autorizado</th>                  <!-- oculto -->
                      <th class="d-none">Ejecutado por</th>                  <!-- oculto -->
                      <th class="d-none">Fecha ejecucion</th>                  <!-- oculto -->
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
                        <td>{{ $solicitud->anulacion->nota_venta }}</td>
                        <td>{{ $solicitud->anulacion->motivo }}</td> 
                        <td class="d-none">{{ $solicitud->glosa ?? 'Sin glosa' }}</td>      <!-- oculto -->
                        <td>{{ ucfirst($estado) }}</td>
                        <td class="d-none">{{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->ejecucion->usuario->name ?? 'Sin ejecutar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->ejecucion->fecha_ejecucion ?? 'N/D' }}</td>      <!-- oculto -->
                        <td>{{ $solicitud->observacion ?? 'Sin observación' }}</td>
                        <td>
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
                                                    <button class="btn btn-outline-secondary btn-sm" disabled>
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
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm modal-md modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTicketLabel{{ $solicitud->id }}">Detalle de Solicitud #{{ $solicitud->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí va TODO el contenido de tu ticket -->
                    @include('GestionSolicitudes.anulacion.detalle_solicitud', ['solicitud' => $solicitud])

                    <!-- Aquí agregamos el seguimiento de la solicitud -->
                    <hr>
                    <h5 class="mt-4">Seguimiento de Solicitud</h5>
                    <!-- Seguimiento usando Livewire (o solo vista Blade) -->
                    <livewire:seguimiento-solicitud :solicitudId="$solicitud->id" />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para verificar entrega solicitud -->
    @include('GestionSolicitudes.anulacion.modal_entrega', ['solicitud' => $solicitud])

    <!-- Modal para registrar entrega fisica -->
    @include('GestionSolicitudes.anulacion.modal_entrega_fisica', ['solicitud' => $solicitud])                   

    <!-- Modal para ejecutar solicitud -->
    @include('GestionSolicitudes.anulacion.modal_ejecutar', ['solicitud' => $solicitud])

    @endforeach
 
    @include('GestionSolicitudes.anulacion.create')

     <!-- Modal para Agregar Observación -->
    @include('GestionSolicitudes.anulacion.modal_observacion')
    @include('GestionSolicitudes.anulacion.script_observacion')

@endsection