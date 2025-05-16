<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"> <!-- Cambiado a modal-lg para mejor espacio -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Precio Especial</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('PrecioEspecial.store') }}" method="POST" onsubmit="return validarProductos()">
          @csrf

          <!-- Tipo de Solicitud -->
          <input type="hidden" name="tipo" value="precio_especial">

          <!-- Usuario que solicita -->
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

          <!-- Cliente -->
          <div class="mb-3 row">
            <label for="cliente" class="col-sm-2 col-form-label">Cliente: </label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="cliente" name="cliente" required>
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
              <div class="col-md-3">
                  <input type="number" id="precio" class="form-control" step="0.01" placeholder="Precio">
              </div>
              <div class="col-md-2">
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
                          <th>Precio</th>
                          <th>Acción</th>
                      </tr>
                  </thead>
                  <tbody></tbody>
              </table>
          </div>

          <!-- Campo oculto para enviar los productos como cadena -->
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
    const producto = document.getElementById('producto').value.trim();
    const cantidad = parseInt(document.getElementById('cantidad').value.trim());
    const precio = parseFloat(document.getElementById('precio').value.trim());

    if (!producto || isNaN(cantidad) || isNaN(precio)) {
        alert("Por favor complete todos los campos correctamente.");
        return;
    }

    if (cantidad <= 0) {
        alert("La cantidad debe ser mayor a cero.");
        return;
    }

    if (precio <= 0) {
        alert("El precio debe ser mayor a cero.");
        return;
    }

    // Redondear a 2 decimales para uniformidad
    productos.push({ 
        producto, 
        cantidad, 
        precio: precio.toFixed(2) 
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
            <td>${item.precio}</td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button></td>
        </tr>`;
        tbody.innerHTML += fila;
    });

    const detalleCadena = productos.map(p => `${p.producto}-${p.cantidad}-${p.precio}`).join(",");
    document.getElementById('detalle_productos').value = detalleCadena;
}

function limpiarInputs() {
    document.getElementById('producto').value = '';
    document.getElementById('cantidad').value = '';
    document.getElementById('precio').value = '';
}

function validarProductos() {
    if (productos.length === 0) {
        alert("Debes agregar al menos un producto.");
        return false;
    }
    return true;
}
</script>
