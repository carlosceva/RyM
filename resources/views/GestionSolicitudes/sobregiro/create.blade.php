    <!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Sobregiro de venta</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('Sobregiro.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
          @csrf
            <!-- Tipo de Solicitud -->
            <input type="hidden" name="tipo" value="Sobregiro de Venta">

            <!-- Usuario que solicita (Oculto porque es el usuario autenticado) -->
            <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">

            <!-- Fecha de solicitud (Generada automáticamente) -->
            <div class="form-group row align-items-center mb-3">
                <label for="fecha_solicitud" class="col-3 col-form-label fw-bold">Fecha</label>
                <div class="col-9">
                    <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                </div>
            </div>

            <!-- Estado (Se define automáticamente como pendiente) -->
            <input type="hidden" name="estado" value="pendiente">

            <!-- Cliente -->
            <div class="form-group row align-items-center mb-3">
                <label for="cliente" class="col-3 col-form-label fw-bold">Cliente</label>
                <div class="col-9">
                    <input type="text" class="form-control" id="cliente" name="cliente">
                </div>
            </div>

            <!-- Importe -->
            <div class="form-group row align-items-center mb-3">
                <label for="importe" class="col-3 col-form-label fw-bold">Importe (Bs)</label>
                <div class="col-9">
                    <input type="text" class="form-control" id="importe" name="importe">
                    @error('importe')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Glosa (Descripción o motivo de la solicitud) -->
            <div class="mb-3">
                <label for="glosa" class="form-label">Glosa</label>
                <textarea class="form-control" id="glosa" name="glosa" rows="3"></textarea>
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