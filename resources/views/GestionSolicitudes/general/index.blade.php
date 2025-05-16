@extends('dashboard')

@section('title', 'Solicitudes')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="far fa-file-alt mr-1"></i>
        <span>Solicitudes</span>
    </h1>
                
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
        <div class="container py-4">
            <!-- Responsive ticket layout -->
            <div class="row row-cols-1 row-cols-md-2 g-4">
                @forelse ($solicitudes as $solicitud)
                <!-- Ticket -->
                <div class="col">
                    <div class="card shadow-sm h-100 ">
                        <!-- Header -->
                        <div class="card-header @if($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') 
                                bg-success 
                            @elseif($solicitud->estado === 'rechazada') 
                                bg-danger 
                            @endif">
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
                        @if($solicitud->tipo === "Sobregiro de Venta")
                            <div class="row  p-2 ">
                                <div class="col-12 col-md-6 ">
                                    <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
                                </div>
                            </div>

                            <div class="row  p-2 ">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Cliente:</strong> {{ $solicitud->sobregiro->cliente ?? 'No asignado' }}</p>
                                </div>

                                <div class="col-12 col-md-6 mt-3 mt-md-0">
                                    <p class="mb-2"><strong>Importe: </strong>{{ $solicitud->sobregiro->importe ?? '0.0' }}</p>
                                </div>
                            </div>

                            <div class="row p-2">
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <strong class="me-2">Motivo:</strong>
                                        <div class="border p-2 rounded bg-light small flex-grow-1">
                                            {{ $solicitud->glosa ?? 'Sin glosa' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($solicitud->tipo === "Anulacion de Venta")
                            <div class="row  p-2 ">
                                <div class="col-12 col-md-6 ">
                                    <p class="mb-1"><strong>Solicitante:</strong> {{ $solicitud->usuario->name ?? 'N/D' }}</p>
                                </div>
                            </div>

                            <div class="row  p-2 ">
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Nota de venta:</strong> {{ $solicitud->anulacion->nota_venta }}</p>
                                </div>
                            </div>

                            <div class="row  p-2 ">
                                <div class="col-12 ">
                                    <p class="mb-2"><strong>Motivo: </strong>{{ $solicitud->anulacion->motivo }}</p>
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
                        @else
                            <div class="row">
                                <!-- Columna izquierda -->
                                <div class="col-12 col-md-6">
                                    <p class="mb-1"><strong>Solicitante:</strong></p>
                                    <p class="mb-2">{{ $solicitud->usuario->name ?? 'N/D' }}</p>

                                    <p><strong>Motivo:</strong></p>
                                    <div class="border p-2 rounded bg-light small">
                                        {{ $solicitud->glosa ?? 'Sin glosa' }}
                                    </div>
                                </div>

                                <!-- Columna derecha -->
                                <div class="col-12 col-md-6 mt-3 mt-md-0">
                                    @if(!empty($solicitud->precioEspecial) && !empty($solicitud->precioEspecial->detalle_productos))
                                        <!-- Si tiene precio especial -->
                                        <p class="mb-2"><strong>{{ $solicitud->precioEspecial->cliente ?? 'Sin cliente' }}</strong></p>
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
                                                    @php
                                                        $productos = explode(',', $solicitud->precioEspecial->detalle_productos);
                                                    @endphp
                                                    @foreach ($productos as $index => $item)
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
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif(!empty($solicitud->muestraMercaderia) && !empty($solicitud->muestraMercaderia->detalle_productos))
                                        <!-- Si tiene muestra de mercadería -->
                                        <p class="mb-2"><strong>{{ $solicitud->muestraMercaderia->cliente ?? 'Sin cliente' }}</strong></p>
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
                                                    @php
                                                        $productos = explode(',', $solicitud->muestraMercaderia->detalle_productos);
                                                    @endphp
                                                    @foreach ($productos as $index => $item)
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
                                                </tbody>
                                            </table>
                                        </div>
                                    @elseif(!empty($solicitud->bajaMercaderia) && !empty($solicitud->bajaMercaderia->detalle_productos))
                                        <!-- Si tiene baja de mercadería -->
                                        <p class="mb-2"><strong>{{ $solicitud->bajaMercaderia->almacen ?? 'Sin almacen' }}</strong></p>
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
                                                    @php
                                                        $productos = explode(',', $solicitud->bajaMercaderia->detalle_productos);
                                                    @endphp
                                                    @foreach ($productos as $index => $item)
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
                                                </tbody>
                                            </table>
                                        </div>
                                    
                                    
                                    @endif
                                </div>

                            </div>
                        @endif
                            <!-- Autorización -->
                            <div class="row mt-3">
                                <div class="col-12 border-top pt-2">
                                    <div class="d-flex justify-content-between flex-wrap small">
                                        <span><strong>{{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</strong></span>
                                        <span class="badge bg-{{ ($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') ? 'success' : ($solicitud->estado === 'rechazada' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($solicitud->estado) }}
                                        </span>
                                        <span>{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Ejecución -->
                             @if(($solicitud->tipo === 'Muestra de Mercaderia' || $solicitud->tipo === 'Baja de Mercaderia' || $solicitud->tipo ==='Sobregiro de Venta' || $solicitud->tipo ==='Anulacion de Venta') && $solicitud->estado !=='rechazada')
                            <div class="row mt-2">
                                <div class="col-12 border-top pt-2">
                                    <div class="d-flex justify-content-between flex-wrap small">
                                        <span><strong>{{ $solicitud->ejecucion->usuario->name ?? 'Sin Ejecutar' }} </strong></span>
                                        <span class="badge bg-{{ $solicitud->ejecucion ? 'success' : 'warning' }}">
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
                            
                        <!-- Footer con acciones -->
                        <div class="card-footer text-end 
                            @if($solicitud->estado === 'aprobada' || $solicitud->estado === 'ejecutada') 
                                bg-success 
                            @elseif($solicitud->estado === 'rechazada') 
                                bg-danger 
                            @endif">
                            @if($solicitud->estado == 'pendiente')
                                
                                    <!-- Aprobar con modal -->
                                    <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#observacionModal"
                                            onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }})">
                                        Aprobar
                                    </button>

                                    <!-- Rechazar con modal -->
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#observacionModal"
                                            onclick="setAccionAndSolicitudId('rechazar', {{ $solicitud->id }})">
                                        Rechazar
                                    </button>
                                
                            @endif
                        </div> 
                    </div>
                </div>
                @empty
                    <div class="col">
                        <p class="text-center">No hay solicitudes registradas.</p>
                    </div>
                @endforelse

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
        <form id="formObservacion" action="{{ route('general.aprobar_o_rechazar') }}" method="POST">
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