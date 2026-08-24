<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Bonificaciones</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('Bonificaciones.store') }}" method="POST" onsubmit="return validarYDeshabilitar(this)">
          @csrf

          <input type="hidden" name="tipo" value="Bonificaciones">
          <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">
          <input type="hidden" name="estado" value="pendiente">

          <!-- Fecha -->
          <div class="form-group row mb-3">
            <label for="fecha_solicitud" class="col-12 col-md-3 col-form-label">Fecha</label>
            <div class="col-12 col-md-9">
              <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
            </div>
          </div>

          <!-- Nota de venta -->
          <div class="form-group row mb-3">
            <label for="nota_venta" class="col-12 col-md-3 col-form-label">Nota de venta</label>
            <div class="col-12 col-md-9">
              <input type="number" class="form-control" id="nota_venta" name="nota_venta" value="{{ old('nota_venta') }}" min="0" step="1" required>
            </div>
          </div>

          <!-- Beneficiario -->
          <div class="form-group row mb-3">
            <label for="beneficiario" class="col-12 col-md-3 col-form-label">Beneficiario</label>
            <div class="col-12 col-md-9">
              <input type="text" class="form-control" id="beneficiario" name="beneficiario" value="{{ old('beneficiario') }}" placeholder="coloque aquí nombre del cliente" required>
            </div>
          </div>

          <!-- Teléfono -->
          <div class="form-group row mb-3">
            <label for="telefono" class="col-12 col-md-3 col-form-label">Teléfono</label>
            <div class="col-12 col-md-9">
              <input type="tel" class="form-control" id="telefono" name="telefono" value="{{ old('telefono') }}" placeholder="Por si no tiene código en SAI se le creará." required>
            </div>
          </div>

          <!-- Monto -->
          <div class="form-group row mb-3">
            <label for="monto" class="col-12 col-md-3 col-form-label">Monto Bs.</label>
            <div class="col-12 col-md-9">
              <input type="number" class="form-control" id="monto" name="monto" value="{{ old('monto') }}" min="0" step="0.01" required>
            </div>
          </div>

          <!-- Glosa -->
          <div class="form-group row mb-3">
            <label for="glosa" class="col-12 col-md-3 col-form-label">Glosa</label>
            <div class="col-12 col-md-9">
              <textarea class="form-control" id="glosa" name="glosa" rows="4" required>{{ old('glosa') }}</textarea>
            </div>
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

<script>
function validarYDeshabilitar(formulario) {
    formulario.querySelector('button[type=submit]').disabled = true;
    return true;
}
</script>
