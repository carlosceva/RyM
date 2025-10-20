<!-- Modal para confirmar venta -->
    <div class="modal fade" id="modalConfirmar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabelConfirmar{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('venta.confirmar', $solicitud->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalLabelConfirmar{{ $solicitud->id }}">Confirmar Venta para Solicitud #{{ $solicitud->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Se realizó la venta?</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="venta_realizada" value="s" id="venta_si_{{ $solicitud->id }}" required>
                            <label class="form-check-label" for="venta_si_{{ $solicitud->id }}">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="venta_realizada" value="n" id="venta_no_{{ $solicitud->id }}" required>
                            <label class="form-check-label" for="venta_no_{{ $solicitud->id }}">No</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>