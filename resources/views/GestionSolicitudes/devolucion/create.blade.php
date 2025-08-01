<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Devolución de Venta</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('Devolucion.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                    @csrf
                    <input type="hidden" name="tipo" value="Devolucion de Venta">
                    <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">
                    <input type="hidden" name="estado" value="pendiente">

                    <!-- Fecha de solicitud -->
                    <div class="form-row align-items-center mb-2">
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="fecha_solicitud">Fecha</label>
                        </div>
                        <div class="col-8 col-md-9 col-lg-10">
                            <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                        </div>
                    </div>

                    <!-- Nota de venta -->
                    <div class="form-row align-items-center mb-2">
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="nota_venta"># Nota</label>
                        </div>
                        <div class="col-8 col-md-9 col-lg-10">
                            <input type="text" class="form-control" id="nota_venta" name="nota_venta" required>
                        </div>
                    </div>

                    <!-- Almacén -->
                    <div class="form-row align-items-center mb-2">
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="almacen">Almacén</label>
                        </div>
                        <div class="col-8 col-md-9 col-lg-10">
                            <select name="almacen" id="almacen" class="form-control" required>
                                <option value="">-- Seleccione un almacén --</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Motivo -->
                    <div class="form-row align-items-center mb-2">
                        <div class="col-4 col-md-3 col-lg-2">
                            <label for="motivo">Motivo</label>
                        </div>
                        <div class="col-8 col-md-9 col-lg-10">
                            <input type="text" class="form-control" id="motivo" name="motivo" required>
                        </div>
                    </div>

                    <!-- Glosa (ocupa fila completa) -->
                    <div class="form-row mb-2">
                        <div class="col-12">
                            <label for="glosa">Glosa</label>
                            <textarea class="form-control" id="glosa" name="glosa" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Tiene pago registrado -->
                    <div class="form-row align-items-center mb-2">
                        <div class="col-4 col-md-5 col-lg-5">
                            <label class="mb-0">¿Tiene pago registrado?</label>
                        </div>
                        <div class="col-8 col-md-7 col-lg-7 d-flex align-items-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tiene_pago" id="tiene_pago_si" value="1" required>
                                <label class="form-check-label" for="tiene_pago_si">Sí</label>
                            </div>
                            <div class="form-check form-check-inline ml-3">
                                <input class="form-check-input" type="radio" name="tiene_pago" id="tiene_pago_no" value="0">
                                <label class="form-check-label" for="tiene_pago_no">No</label>
                            </div>
                        </div>
                    </div>

                    <!-- Observación de pago -->
                    <div class="form-row d-none mb-3" id="obs_pago_group">
                        <div class="col-12">
                            <label for="obs_pago">Observación del Pago</label>
                            <small class="text-muted d-block mb-1">Indicar si se abonará a otra nota o se devolverá en efectivo.</small>
                            <input type="text" class="form-control" name="obs_pago" id="obs_pago">
                        </div>
                    </div>

                    <!-- Inputs para productos -->
                    <div class="form-row mb-3">
                        <div class="col-6 col-md-4 mb-2 mb-md-0">
                            <input type="text" id="producto" class="form-control" placeholder="Producto">
                        </div>
                        <div class="col-6 col-md-4 mb-2 mb-md-0">
                            <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="button" class="btn btn-primary w-100" onclick="agregarProducto()">Agregar</button>
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

                    <!-- Campo oculto para detalle -->
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