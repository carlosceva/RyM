<div class="modal fade" id="modalEntregaF{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Verificar Entrega</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form method="POST" action="{{ route('solicitud.devolucion.verificarEntregaFisica', $solicitud->id) }}">
                    @csrf
                    <div class="modal-body">

                        <div class="mb-3">
                            <label class="form-label">¿Tiene Entrega física?</label>
                            <div class="form-check">
                                <input class="form-check-input entrega-radio" type="radio" name="entrega" id="entregaSiF{{ $solicitud->id }}" value="1" data-solicitud-id="{{ $solicitud->id }}">
                                <label class="form-check-label" for="entregaSiF{{ $solicitud->id }}">Sí</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input entrega-radio" type="radio" name="entrega" id="entregaNoF{{ $solicitud->id }}" value="0" data-solicitud-id="{{ $solicitud->id }}">
                                <label class="form-check-label" for="entregaNoF{{ $solicitud->id }}">No</label>
                            </div>
                        </div>
                        <div id="mensajeEntregaF{{ $solicitud->id }}" class="alert alert-warning mt-3 d-none" role="alert">
                            ⚠️ Recuerde registrar esta entrega también en su sistema externo.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Confirmar verificación</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>