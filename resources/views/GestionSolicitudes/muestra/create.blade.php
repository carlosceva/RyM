<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Muestra de Mercadería</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('Muestra.store') }}" method="POST">
          @csrf

          <input type="hidden" name="tipo" value="Muestra de Mercaderia">
          <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">
          <input type="hidden" name="estado" value="pendiente">

          <!-- Fecha -->
          <div class="form-group row mb-3">
            <div class="col-12 d-flex align-items-center">
              <label for="fecha_solicitud" class="col-4 col-md-2 col-form-label">Fecha</label>
              <div class="col-8 col-md-10">
                <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
              </div>
            </div>
          </div>

          <!-- Cliente y COD SAI -->
          <div class="form-group row mb-3">
            <!-- Cliente -->
            <div class="col-12 col-md-6 d-flex align-items-center mb-2 mb-md-0">
              <label for="cliente" class="col-4 col-form-label">Cliente</label>
              <div class="col-8">
                <input type="text" class="form-control" id="cliente" name="cliente" required>
              </div>
            </div>

            <!-- COD SAI -->
            <div class="col-12 col-md-6 d-flex align-items-center">
              <label for="cod_sai" class="col-4 col-form-label">COD SAI</label>
              <div class="col-8">
                <input type="text" class="form-control" id="cod_sai" name="cod_sai" required>
              </div>
            </div>
          </div>

          <!-- Glosa -->
          <div class="form-group row mb-3">
            <label for="glosa" class="col-12 col-md-2 col-form-label">Motivo</label>
            <div class="col-12 col-md-10">
              <textarea class="form-control" id="glosa" name="glosa" rows="4" required></textarea>
            </div>
          </div>

          <!-- Detalle -->
          <div class="form-group row mb-2">
            <label class="col-12 col-form-label">Detalle</label>
          </div>

          <div class="form-group row align-items-end mb-3">
            <!-- Inputs -->
            <div class="col-12 col-md-10">
              <div class="form-row">
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                  <input type="text" id="codsai" class="form-control" placeholder="codsai">
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                  <input type="text" id="producto" class="form-control" placeholder="Producto">
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                  <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
                </div>
                <div class="col-12 col-md-3 mb-2 mb-md-0">
                  <input type="text" id="medida" class="form-control" placeholder="U/M">
                </div>
              </div>
            </div>

            <!-- Botón -->
            <div class="col-12 col-md-2 mt-3 mt-md-0">
              <button type="button" class="btn btn-primary w-100" onclick="agregarProducto()">Agregar</button>
            </div>
          </div>

          <!-- Tabla dinámica -->
          <div class="table-responsive mb-3">
            <table class="table table-bordered table-striped" id="tablaProductos">
              <thead class="table-dark">
                <tr>
                  <th>Cod-SAI</th>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>U/M</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <input type="hidden" name="detalle_productos" id="detalle_productos">

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
    const codsai = document.getElementById('codsai').value.trim();
    const producto = document.getElementById('producto').value.trim();
    const medida = document.getElementById('medida').value.trim();
    const cantidad = parseInt(document.getElementById('cantidad').value.trim());

    if (!producto || isNaN(cantidad) || !medida) {
        alert("Por favor complete todos los campos correctamente.");
        return;
    }

    if (cantidad <= 0) {
        alert("La cantidad debe ser mayor a cero.");
        return;
    }

    // Redondear a 2 decimales para uniformidad
    productos.push({ 
        codsai,
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
            <td>${item.codsai}</td>
            <td>${item.producto}</td>
            <td>${item.cantidad}</td>
            <td>${item.medida}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button></td>
        </tr>`;
        tbody.innerHTML += fila;
    });

    const detalleCadena = productos.map(p => `${p.codsai}-${p.producto}-${p.cantidad}-${p.medida}`).join(",");
    document.getElementById('detalle_productos').value = detalleCadena;
}

function limpiarInputs() {
    document.getElementById('codsai').value = '';
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