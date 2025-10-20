<!-- resources/views/GestionSolicitudes/precio/modal_observacion.blade.php -->
<div class="modal fade" id="observacionModal" tabindex="-1" aria-labelledby="observacionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div id="observacionModalHeader" class="modal-header">
                <h5 class="modal-title" id="observacionModalLabel">Agregar Observación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formObservacion" action="{{ route('precioespecial.aprobar_o_rechazar') }}" method="POST">
                    @csrf
                    <input type="hidden" name="solicitud_id" id="solicitud_id">
                    <input type="hidden" name="accion" id="accion">

                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="clienteModal" class="col-form-label"><strong>Cliente:</strong></label>
                        </div>
                        <div class="col">
                            <input type="text" readonly class="form-control" id="clienteModal" />
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-auto">
                            <label for="glosaModal" class="col-form-label"><strong>Motivo:</strong></label>
                        </div>
                        <div class="col">
                            <input type="text" readonly class="form-control" id="glosaModal" />
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Detalle de Productos</label>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablaProductosEditar">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>U/M</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <input type="hidden" name="detalle_productos_editado" id="detalle_productos_editado">
                    </div>

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
