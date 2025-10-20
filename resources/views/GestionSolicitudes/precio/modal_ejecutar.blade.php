<!-- Modal Ejecutar -->
<div class="modal fade" id="modalEjecutar{{ $solicitud->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('precioEspecial.ejecutar', $solicitud->id) }}" method="POST">
                @csrf
                @method('POST')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Confirmar Ejecución</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>¿Está seguro de registrar esta acción?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Ejecutar</button>
                </div>
            </form>
        </div>
    </div>
</div>
