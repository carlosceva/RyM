<!-- Modal para confirmar solicitud -->
    <div class="modal fade" id="modalConfirmar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            <form action="{{ route('sobregiro.confirmar', $solicitud->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-header">
                <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Confirmar Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                <p>¿Está seguro de registrar esta acción?</p>

                <div class="mb-3">
                    <label for="cod_sobregiro_{{ $solicitud->id }}" class="form-label">Código de Sobregiro</label>
                    <input type="text" class="form-control" id="cod_sobregiro_{{ $solicitud->id }}" name="cod_sobregiro" 
                        value="{{ old('cod_sobregiro', $solicitud->cod_sobregiro ?? '') }}" required>
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