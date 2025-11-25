<!-- Modal para crear nueva solicitud de cambio de mercadería -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Permiso/Vacacion </h5>
        <button type="button" class="btn-close" data-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form action="{{ route('Vacaciones.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validarYDeshabilitar(this)">
          @csrf

          <!-- Tipo de solicitud -->
          <input type="hidden" name="tipo" value="Vacacion">

          <!-- Usuario que solicita -->
          <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">

          <!-- Fecha -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Fecha</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
            </div>
          </div>

          <!-- Tipo Permiso -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Tipo de solicitud</label>
            <div class="col-sm-10">
              <select name="tipo_permiso" class="form-select" required>
                <option value="">-- Seleccione un tipo --</option>
                  <option value="Permiso">Permiso </option>
                  <option value="Vacacion">Vacacion </option>
              </select>
            </div>
          </div>

          <!-- Fecha inicial -->
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Fecha Inicial</label>
                <div class="col-sm-10">
                    <input type="date" 
                        class="form-control"
                        name="fecha_inicial"
                        required>
                </div>
            </div>

            <!-- Fecha final -->
            <div class="mb-3 row">
                <label class="col-sm-2 col-form-label">Fecha Final</label>
                <div class="col-sm-10">
                    <input type="date" 
                        class="form-control"
                        name="fecha_fin"
                        required>
                </div>
            </div>

          <!-- glosa -->
          <div class="mb-3 row">
            <label class="col-sm-2 col-form-label">Glosa</label>
            <div class="col-sm-10">
              <textarea class="form-control" name="glosa" rows="3" required></textarea>
            </div>
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
    function validarYDeshabilitar(form) {
    form.querySelector('button[type=submit]').disabled = true;
    return true;
}
</script>