<!-- Modal para crear nueva solicitud de cambio de mercadería -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Cambio de Mercadería</h5>
        <button type="button" class="btn-close" data-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="{{ route('CambiosFisicos.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validarYDeshabilitar(this)">
          @csrf

          <!-- Tipo de solicitud -->
          <input type="hidden" name="tipo" value="Cambio fisico en Mercaderia">

          <!-- Usuario que solicita -->
          <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">

          <!-- Fecha -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Fecha</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
            </div>
          </div>

          <!-- Nota de venta -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Nota de Venta</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="nota_venta" required>
            </div>
          </div>

          <!-- Almacén -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Almacén</label>
            <div class="col-sm-10">
              <select name="id_almacen" class="form-select" required>
                <option value="">-- Seleccione un almacén --</option>
                @foreach($almacenes as $almacen)
                  <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <!-- Motivo -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Motivo</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="motivo" rows="3" required></textarea>
            </div>
          </div>

          <!-- Detalle de productos -->
          <label class="form-label">Detalle de Productos</label>

          <div class="row g-2 mb-3">
            <div class="col-md-4">
              <input type="text" id="producto" class="form-control" placeholder="Producto">
            </div>
            <div class="col-md-4">
              <input type="number" id="cantidad" class="form-control" placeholder="Cantidad">
            </div>
            <div class="col-md-3">
              <input type="text" id="medida" class="form-control" placeholder="U/M">
            </div>
            <div class="col-md-1">
              <button type="button" class="btn btn-primary w-100" onclick="agregarProducto()">
                +
              </button>
            </div>
          </div>

          <!-- Tabla -->
          <div class="table-responsive mb-3">
            <table class="table table-bordered" id="tablaProductos">
              <thead class="table-dark">
                <tr>
                  <th>Producto</th>
                  <th>Cantidad</th>
                  <th>U/M</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <!-- Campo para enviar detalle -->
          <input type="hidden" name="detalle_productos" id="detalle_productos">

          <!-- Archivo adjunto -->
          <div class="mb-3">
            <label class="form-label">Adjuntar archivo</label>
            <input type="file" name="archivo" class="form-control" accept=".xlsx,.xls,.csv,.pdf,.docx,.jpg,.png">
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Crear Solicitud</button>
          </div>

        </form>
      </div>

    </div>
  </div>
</div>

<script>
let productos = [];

function agregarProducto() {
    const producto = document.getElementById('producto').value.trim();
    const cantidad = parseFloat(document.getElementById('cantidad').value.trim());
    const medida = document.getElementById('medida').value.trim();

    if (!producto || !cantidad || cantidad <= 0) {
        alert("Complete correctamente los datos.");
        return;
    }

    productos.push({ producto, cantidad, medida });
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
        tbody.innerHTML += `
            <tr>
                <td>${item.producto}</td>
                <td>${item.cantidad}</td>
                <td>${item.medida}</td>
                <td>
                  <button type="button" class="btn btn-danger btn-sm" onclick="eliminarProducto(${index})">Eliminar</button>
                </td>
            </tr>
        `;
    });

    document.getElementById('detalle_productos').value =
        productos.map(p => `${p.producto}-${p.cantidad}-${p.medida}`).join(",");
}

function limpiarInputs() {
    document.getElementById('producto').value = "";
    document.getElementById('cantidad').value = "";
    document.getElementById('medida').value = "";
}

function validarYDeshabilitar(form) {
    if (productos.length === 0) {
        alert("Debe agregar al menos un producto.");
        return false;
    }

    form.querySelector('button[type=submit]').disabled = true;
    return true;
}
</script>
