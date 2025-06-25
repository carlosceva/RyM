
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
                      <th class="d-none">Tipo</th>                  <!-- oculto -->
                      <th>Fecha</th>
                      <th class="d-none">Solicitante</th>           <!-- oculto -->
                      <th>Cliente</th>
                      <th class="d-none">Motivo</th>                <!-- oculto -->
                      <th class="d-none">Productos</th>
                      <th>Estado</th>
                      <th class="d-none">Autorizador</th>           <!-- oculto -->
                      <th class="d-none">Fecha autorizacion</th>    <!-- oculto -->
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
                        <td>{{ $solicitud->precioEspecial?->cliente ?? 'No asignado' }}</td>
                        <td class="d-none">{{ $solicitud->glosa ?? 'Sin glosa' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->precioEspecial?->detalle_productos ?? 'Sin detalle de productos' }}</td>
                        <td>{{ ucfirst($estado) }}</td>
                        <td class="d-none">{{ $solicitud->autorizador->name ?? 'Sin autorizar' }}</td>      <!-- oculto -->
                        <td class="d-none">{{ $solicitud->fecha_autorizacion ?? 'N/D' }}</td>      <!-- oculto -->
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
                                                    data-detalle="{{ $solicitud->precioEspecial?->detalle_productos ?? 'Sin detalle de productos' }}"
                                                    data-cliente="{{ $solicitud->precioEspecial?->cliente ?? 'No asignado' }}"
                                                    data-glosa="{{ $solicitud->glosa ?? 'Sin glosa' }}"
                                                    onclick="setAccionAndSolicitudId('aprobar', {{ $solicitud->id }}, this)">
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
                                @can('Precio_especial_ejecutar')
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <!-- Aquí va TODO el contenido de tu ticket -->
                    @include('GestionSolicitudes.precio.detalle_solicitud', ['solicitud' => $solicitud])
                    <!-- Aquí agregamos el seguimiento de la solicitud -->
                    <hr>
                    <h5 class="mt-4">Seguimiento de Solicitud</h5>
                    <!-- Seguimiento usando Livewire (o solo vista Blade) -->
                    <livewire:seguimiento-solicitud :solicitudId="$solicitud->id" />
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para ejecutar solicitud -->
    <div class="modal fade" id="modalEjecutar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('precioEspecial.ejecutar', $solicitud->id) }}" method="POST">
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
 
    @include('GestionSolicitudes.precio.create')

    <!-- Modal para Agregar Observación -->
    <div class="modal fade" id="observacionModal" tabindex="-1" aria-labelledby="observacionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div id="observacionModalHeader" class="modal-header">
                    <h5 class="modal-title" id="observacionModalLabel">Agregar Observación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formObservacion" action="{{ route('precioespecial.aprobar_o_rechazar') }}" method="POST">
                    @csrf
                    <!-- Campo oculto para la solicitud_id -->
                    <input type="hidden" name="solicitud_id" id="solicitud_id" value="">
                    <input type="hidden" name="accion" id="accion" value="">

                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="clienteModal" class="col-form-label"><strong>Cliente:</strong></label>
                        </div>
                        <div class="col">
                            <input type="text" readonly class="form-control" id="clienteModal" />
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="glosaModal" class="col-form-label"><strong>Motivo:</strong></label>
                        </div>
                        <div class="col">
                            <input type="text" readonly class="form-control" id="glosaModal" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detalle de Productos</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablaProductosEditar">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                        <!-- Campo oculto para enviar los productos actualizados -->
                        <input type="hidden" name="detalle_productos_editado" id="detalle_productos_editado">
                    </div>

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
function setAccionAndSolicitudId(accion, solicitudId, boton = null) {
    document.getElementById('accion').value = accion;
    document.getElementById('solicitud_id').value = solicitudId;

    const header = document.getElementById('observacionModalHeader');
    const title = document.getElementById('observacionModalLabel');

    // Limpiar clases anteriores
    header.classList.remove('bg-primary', 'bg-danger', 'text-white');

    if (accion === 'aprobar') {
        header.classList.add('bg-primary', 'text-white');
        title.textContent = 'Aprobar Solicitud';
    } else if (accion === 'rechazar') {
        header.classList.add('bg-danger', 'text-white');
        title.textContent = 'Rechazar Solicitud';
    } else {
        title.textContent = 'Agregar Observación';
    }

    if (boton) {
        const detalle = boton.getAttribute('data-detalle');
        const cliente = boton.getAttribute('data-cliente') || 'No asignado';
        const glosa = boton.getAttribute('data-glosa') || 'Sin glosa';

        // Mostrar cliente y glosa en el modal
        document.getElementById('clienteModal').value = cliente;
        document.getElementById('glosaModal').value = glosa;

        if (detalle) {
            cargarProductosParaEditar(detalle);
        }
    }
}
</script>

<script>
function cargarProductosParaEditar(detalleString) {
    const productos = detalleString.split(',').map(item => {
        const [producto, cantidad, precio] = item.split('-');
        return { producto, cantidad, precio };
    });

    const tbody = document.querySelector("#tablaProductosEditar tbody");
    tbody.innerHTML = "";

    productos.forEach((item, index) => {
        const fila = `
        <tr>
            <td>${item.producto}</td>
            <td>${item.cantidad}</td>
            <td>
                <input type="number" step="0.01" min="0" class="form-control precio-input" 
                       value="${item.precio}" data-index="${index}">
            </td>
        </tr>`;
        tbody.innerHTML += fila;
    });

    // Guarda en un dataset para usarlo al enviar
    document.getElementById('tablaProductosEditar').dataset.productos = JSON.stringify(productos);
}

function actualizarDetalleProductosEditado() {
    const precios = document.querySelectorAll('.precio-input');
    const productos = JSON.parse(document.getElementById('tablaProductosEditar').dataset.productos);

    precios.forEach(input => {
        const index = input.dataset.index;
        productos[index].precio = parseFloat(input.value).toFixed(2);
    });

    const detalleCadena = productos.map(p => `${p.producto}-${p.cantidad}-${p.precio}`).join(",");
    document.getElementById('detalle_productos_editado').value = detalleCadena;
}

// Antes de enviar el formulario, actualiza el campo oculto
document.getElementById('formObservacion').addEventListener('submit', function (e) {
    actualizarDetalleProductosEditado();
});
</script>


@endsection