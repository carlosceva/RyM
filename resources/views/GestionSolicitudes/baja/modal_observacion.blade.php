<!-- Modal para Agregar Observación -->
<div class="modal fade" id="observacionModal" tabindex="-1" aria-labelledby="observacionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="observacionModalHeader" class="modal-header">
        <h5 class="modal-title" id="observacionModalLabel">Agregar Observación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formObservacion" action="{{ route('baja.aprobar_o_rechazar') }}" method="POST">
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