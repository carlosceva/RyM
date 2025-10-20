<div class="modal fade" id="modalEntrega{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Verificar Despacho</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form method="POST" action="{{ route('solicitud.devolucion.verificarEntrega', $solicitud->id) }}">
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label">¿Tiene despacho en sistema?</label>
                        <div class="form-check">
                            <input class="form-check-input entrega-verificacion-radio"
                                   type="radio"
                                   name="entrega"
                                   id="entregaSi{{ $solicitud->id }}"
                                   value="1"
                                   data-solicitud-id="{{ $solicitud->id }}"
                                   data-tiene-pago="{{ isset($solicitud->devolucion) && $solicitud->devolucion->tiene_pago ? '1' : '0' }}">
                            <label class="form-check-label" for="entregaSi{{ $solicitud?->id ?? 'temp' }}">Sí</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input entrega-verificacion-radio"
                                   type="radio"
                                   name="entrega"
                                   id="entregaNo{{ $solicitud->id }}"
                                   value="0"
                                   data-solicitud-id="{{ $solicitud->id }}"
                                   data-tiene-pago="{{ isset($solicitud->devolucion) && $solicitud->devolucion->tiene_pago ? '1' : '0' }}">
                            <label class="form-check-label" for="entregaNo{{ $solicitud->id ?? 'temp'}}">No</label>
                        </div>
                    </div>

                    {{-- Mensaje de advertencia --}}
                    <!-- <div id="resultado{{ $solicitud->id }}" class="alert alert-warning mt-3 d-none fw-bold text-center">
                        ⚠️ Debe registrar la entrega en el sistema externo antes de continuar.
                    </div> -->

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-confirmar{{ $solicitud->id }}">Confirmar verificación</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>