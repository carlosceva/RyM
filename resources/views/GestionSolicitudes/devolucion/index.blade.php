
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
                        <td>{{ $solicitud->devolucion->nota_venta ?? 'N/D' }}</td>
                        <td>
                            @if (empty($solicitud->devolucion->almacen) || $solicitud->devolucion->almacen == 'No definido')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAlmacen{{ $solicitud->id }}">
                                    Asignar
                                </button>
                            @else
                                @if(is_numeric($solicitud->devolucion->almacen)) 
                                    {{ \App\Models\Almacen::find($solicitud->devolucion->almacen)->nombre ?? 'No definido' }}
                                @else
                                    {{ $solicitud->devolucion->almacen ?? 'No definido' }}
                                @endif
                            @endif
                        </td>
                        <td>{{ $solicitud->devolucion->motivo ?? 'N/D' }}</td>
                        <td class="d-none">{{ $solicitud->glosa ?? 'Sin glosa' }}</td>      <!-- oculto -->
                        <td class="d-none">
                            @if($solicitud->devolucion)
                                {{ $solicitud->devolucion->requiere_abono ? 'Sí' : 'No' }}
                            @else
                                N/D
                            @endif
                        </td>      <!-- oculto -->
                        <td class="d-none">
                            @if($solicitud->devolucion)
                                {{ $solicitud->devolucion->tiene_entrega ? 'Sí' : 'No' }}
                            @else
                                N/D
                            @endif
                        </td>       <!-- oculto -->
                        <td>{{ $solicitud->devolucion->detalle_productos ?? 'N/D' }}</td>
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

                                @php
                                    $devolucion = $solicitud->devolucion;

                                    $tienePago = $devolucion?->tiene_pago;
                                    $tieneEntrega = $devolucion?->tiene_entrega;
                                    $entregaFisica = $devolucion?->entrega_fisica;
                                @endphp

                                @if ($solicitud->estado === 'aprobada' && !$solicitud->ejecucion && !is_null($tienePago))

                                    {{-- Paso 1: Aún no se marcó si hubo entrega --}}
                                    @if (is_null($tieneEntrega))
                                        @can('Devolucion_entrega')
                                            @can('Devolucion_ejecutar')
                                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEntrega{{ $solicitud->id }}">
                                                    Confirmar Despacho
                                                </button>
                                                &nbsp;
                                            @endcan
                                        @endcan

                                    {{-- Paso 2: Sí hubo entrega --}}
                                    @elseif ($tieneEntrega)
                                        @can('Devolucion_ejecutar')
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                                Ejecutar
                                            </button>
                                            &nbsp;
                                        @endcan

                                    {{-- Paso 3: No hubo entrega (sin distinguir si tiene pago o no) --}}
                                    @else
                                        @if (is_null($entregaFisica))
                                            {{-- Esperando confirmación del almacén --}}
                                            @can('Devolucion_entrega')
                                                @cannot('Devolucion_ejecutar')
                                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEntregaF{{ $solicitud->id }}">
                                                        Registrar Entrega
                                                    </button>
                                                    &nbsp;
                                                @endcannot
                                            @endcan

                                            @can('Devolucion_ejecutar')
                                                <button class="btn btn-outline-secondary btn-sm" disabled>
                                                    ⏳ Esperando confirmación
                                                </button>
                                                &nbsp;
                                            @endcan

                                        @elseif($entregaFisica === false)
                                            {{-- No hubo entrega física → ejecutar como anulación --}}
                                            @can('Devolucion_ejecutar')
                                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                                    Ejecutar
                                                </button>
                                                &nbsp;
                                            @endcan

                                        @elseif($entregaFisica === true)
                                            {{-- Sí hubo entrega física → ejecutar como devolución --}}
                                            @can('Devolucion_ejecutar')
                                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalEjecutar{{ $solicitud->id }}">
                                                    Ejecutar
                                                </button>
                                                &nbsp;
                                            @endcan
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
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title" id="modalTicketLabel{{ $solicitud->id }}">Detalle de Solicitud #{{ $solicitud->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí va TODO el contenido de tu ticket -->
                    @include('GestionSolicitudes.devolucion.detalle_solicitud', ['solicitud' => $solicitud])
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
    @include('GestionSolicitudes.devolucion.modal_entrega', ['solicitud' => $solicitud])

    <!-- Modal para registrar entrega fisica -->
    @include('GestionSolicitudes.devolucion.modal_entrega_fisica', ['solicitud' => $solicitud])                   

    <!-- Modal para ejecutar solicitud -->
    @include('GestionSolicitudes.devolucion.modal_ejecutar', ['solicitud' => $solicitud])
    

    <!-- Modal ALmacen -->
    <div class="modal fade" id="modalAlmacen{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalAlmacenLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('devolucion.actualizar.almacen', $solicitud->id) }}" class="modal-content">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <h5 class="modal-title" id="modalAlmacenLabel{{ $solicitud->id }}">Asignar Almacén</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="almacen_id" class="form-label">Seleccione un almacén</label>
                        <select name="almacen_id" class="form-select" required>
                            <option value="">-- Seleccionar --</option>
                            @foreach($almacenes as $almacen)
                                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    @endforeach
 
    @include('GestionSolicitudes.devolucion.create')

    <!-- Modal para Agregar Observación -->
    @include('GestionSolicitudes.devolucion.modal_observacion')
    @include('GestionSolicitudes.devolucion.script_observacion')

<script>
    // document.addEventListener('DOMContentLoaded', function () {
    //     const radios = document.querySelectorAll('.entrega-verificacion-radio');

    //     radios.forEach(function (radio) {
    //         radio.addEventListener('change', function () {
    //             const solicitudId = this.dataset.solicitudId;
    //             const tienePago = this.dataset.tienePago === '1';
    //             const esNoEntrega = this.value === '0';

    //             const mensaje = document.getElementById('resultado' + solicitudId);
    //             const btnConfirmar = document.querySelector('.btn-confirmar' + solicitudId);

    //             if (tienePago && esNoEntrega) {
    //                 mensaje?.classList.remove('d-none');
    //                 btnConfirmar.disabled = true;
    //             } else {
    //                 mensaje?.classList.add('d-none');
    //                 btnConfirmar.disabled = false;
    //             }
    //         });
    //     });
    // });
</script>

@endsection