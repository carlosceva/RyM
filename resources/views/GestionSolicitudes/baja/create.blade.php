<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Baja de Mercaderia</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('Baja.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
            <!-- Tipo de Solicitud -->
            <input type="hidden" name="tipo" value="Baja de Mercaderia">

            <!-- Usuario que solicita (Oculto porque es el usuario autenticado) -->
            <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">

             <!-- Fecha de solicitud -->
            <div class="mb-3 row">
                <label for="fecha_solicitud" class="col-sm-2 col-form-label">Fecha actual: </label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                </div>
            </div>

            <!-- Estado -->
            <input type="hidden" name="estado" value="pendiente">

            <!-- Glosa -->
            <div class="mb-3 row">
                <label for="glosa" class="col-sm-2 col-form-label">Motivo: </label>
                <div class="col-sm-10">
                    <textarea class="form-control" id="glosa" name="glosa" rows="4" required></textarea>
                </div>
            </div>

            <!-- Almacén -->
            <div class="row mb-2 align-items-center">
                <label for="almacen" class="col-md-2 col-form-label">Almacén</label>
                <div class="col-md-10">
                    <select name="almacen" id="almacen" class="form-select" required>
                        <option value="">-- Seleccione un almacén --</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->nombre }}">{{ $almacen->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tipo -->
            <div class="row mb-2 align-items-center">
                <label for="tipo_ajuste" class="col-md-2 col-form-label">Tipo de ajuste</label>
                <div class="col-md-10">
                    <select name="tipo_ajuste" id="tipo_ajuste" class="form-select" required>
                        <option value="">-- Seleccione un tipo --</option>
                        
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        
                    </select>
                </div>
            </div>

            <!-- Inputs para producto, cantidad y precio -->
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" id="producto" class="form-control" placeholder="Producto">
                </div>
                <div class="col-md-3">
                    <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
                </div>
                <div class="col-12 col-md-3">
                    <input type="text" id="medida" class="form-control" placeholder="Ud. medida">
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
                            <th>Ud. Medida</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Campo oculto para enviar los productos como cadena -->
            <input type="hidden" name="detalle_productos" id="detalle_productos">

            <!-- Campo para adjuntar archivo -->
            <div class="mb-3">
                <label for="archivo" class="form-label">Adjuntar Archivo</label>
                <input type="file" name="archivo" class="form-control" id="archivo" accept=".xlsx, .xls, .csv, .pdf, .docx, .jpg, .png">
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

<!-- JavaScript para tabla dinámica -->
<script>
let productos = [];

function agregarProducto() {
    const producto = document.getElementById('producto').value.trim();
    const cantidad = parseInt(document.getElementById('cantidad').value.trim());
    const medida = document.getElementById('medida').value.trim();

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
        medida,  
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
            <td>${item.medida}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button></td>
        </tr>`;
        tbody.innerHTML += fila;
    });

    const detalleCadena = productos.map(p => `${p.producto}-${p.cantidad}-${p.medida}`).join(",");
    document.getElementById('detalle_productos').value = detalleCadena;
}

function limpiarInputs() {
    document.getElementById('producto').value = '';
    document.getElementById('cantidad').value = '';
    document.getElementById('medida').value = '';
}

function validarProductos() {
    if (productos.length === 0) {
        alert("Debes agregar al menos un producto.");
        return false;
    }
    return true;
}
</script>