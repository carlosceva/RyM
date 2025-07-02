<!-- Modal para Confirmar Solicitud -->
<div class="modal fade" id="modalConfirmar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalConfirmarLabel{{ $solicitud->id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('baja.confirmar', $solicitud->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="modalConfirmarLabel{{ $solicitud->id }}">Confirmar Solicitud #{{ $solicitud->id }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <p>¿Está seguro de confirmar esta solicitud?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Confirmar</button>
        </div>
      </form>
    </div>
  </div>
</div>

