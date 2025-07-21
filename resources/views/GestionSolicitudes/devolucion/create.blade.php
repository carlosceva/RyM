<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Aumenté tamaño del modal para mejor distribución -->
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
                        <label for="fecha_solicitud" class="col-md-3 col-form-label">Fecha de Solicitud</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                        </div>
                    </div>

                    <!-- Nota de venta -->
                    <div class="row mb-2 align-items-center">
                        <label for="nota_venta" class="col-md-3 col-form-label">Nota de venta</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="nota_venta" name="nota_venta" required>
                        </div>
                    </div>

                    <!-- Almacén -->
                    <div class="row mb-2 align-items-center">
                        <label for="almacen" class="col-md-3 col-form-label">Almacén</label>
                        <div class="col-md-9">
                            <select name="almacen" id="almacen" class="form-select" required>
                                <option value="">-- Seleccione un almacén --</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Motivo -->
                    <div class="row mb-2 align-items-center">
                        <label for="motivo" class="col-md-3 col-form-label">Motivo</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="motivo" name="motivo" required>
                        </div>
                    </div>

                    <!-- Glosa -->
                    <div class="row mb-2">
                        <label for="glosa" class="col-md-3 col-form-label">Glosa</label>
                        <div class="col-md-9">
                            <textarea class="form-control" id="glosa" name="glosa" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Tiene pago registrado -->
                    <div class="mb-3 d-flex align-items-center">
                        <label class="form-label mb-0 me-3" style="white-space: nowrap;">¿Tiene pago registrado?</label>

                        <div class="d-flex align-items-center">
                            <div class="form-check form-check-inline d-flex align-items-center me-3">
                                <input class="form-check-input me-1" type="radio" name="tiene_pago" id="tiene_pago_si" value="1" required>
                                <label class="form-check-label" for="tiene_pago_si">Sí</label>
                            </div>

                            <div class="form-check form-check-inline d-flex align-items-center">
                                <input class="form-check-input me-1" type="radio" name="tiene_pago" id="tiene_pago_no" value="0">
                                <label class="form-check-label" for="tiene_pago_no">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Observación de pago (solo si tiene pago) -->
                    <div class="mb-3 d-none" id="obs_pago_group">
                        <label for="obs_pago" class="form-label">Observación del Pago</label>
                        <small class="text-muted d-block mb-1">Indicar si se abonará a otra nota o se devolverá en efectivo.</small>
                        <input type="text" class="form-control" name="obs_pago" id="obs_pago">
                    </div>

                    <!-- Inputs para producto, cantidad y precio -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <input type="text" id="producto" class="form-control" placeholder="Producto">
                        </div>
                        <div class="col-md-3">
                            <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary " onclick="agregarProducto()">Agregar</button>
                        </div>
                    </div>

                    <!-- Tabla dinámica -->
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-striped" id="tablaProductos">
                            <thead class="table-dark">
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Campo oculto para enviar los productos como cadena -->
                    <input type="hidden" name="detalle_productos" id="detalle_productos">

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

    <!-- JavaScript para tabla dinámica -->
<script>
let productos = [];

function agregarProducto() {
    const producto = document.getElementById('producto').value.trim();
    const cantidad = parseInt(document.getElementById('cantidad').value.trim());

    if (!producto || isNaN(cantidad)) {
        alert("Por favor complete todos los campos correctamente.");
        return;
    }

    if (cantidad <= 0) {
        alert("La cantidad debe ser mayor a cero.");
        return;
    }

    // Redondear a 2 decimales para uniformidad
    productos.push({ 
        producto, 
        cantidad, 
    });

    actualizarTabla();
    limpiarInputs();
}

function eliminarProducto(index) {
    productos.splice(index, 1);
    actualizarTabla();
}

function actualizarTabla() {
    const tbody = document.querySelector("#tablaProductos tbody");
    tbody.innerHTML = "";

    productos.forEach((item, index) => {
        const fila = `<tr>
            <td>${item.producto}</td>
            <td>${item.cantidad}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button></td>
        </tr>`;
        tbody.innerHTML += fila;
    });

    const detalleCadena = productos.map(p => `${p.producto}-${p.cantidad}`).join(",");
    document.getElementById('detalle_productos').value = detalleCadena;
}

function limpiarInputs() {
    document.getElementById('producto').value = '';
    document.getElementById('cantidad').value = '';
}

function validarProductos() {
    if (productos.length === 0) {
        alert("Debes agregar al menos un producto.");
        return false;
    }
    return true;
}
</script>