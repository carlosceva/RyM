<!-- Vista para crear solicitud -->
<div class="modal fade" id="modalNuevaSolicitud" tabindex="-1" aria-labelledby="modalCrearSolicitudLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalCrearSolicitudLabel">Crear Solicitud de Anulación de Venta</h5>
            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('Anulacion.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                @csrf
                <!-- Tipo de Solicitud -->
                <input type="hidden" name="tipo" value="Anulacion de Venta">

                <!-- Usuario que solicita -->
                <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">

                <!-- Fecha de solicitud -->
                <div class="mb-3">
                    <label for="fecha_solicitud" class="form-label">Fecha de Solicitud</label>
                    <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                </div>

                <!-- Estado inicial -->
                <input type="hidden" name="estado" value="pendiente">

                <!-- Nota de venta -->
                <div class="mb-3">
                    <label for="nota_venta" class="form-label">Nota de venta</label>
                    <input type="text" class="form-control" id="nota_venta" name="nota_venta">
                </div>

                <!-- Almacén -->
                <div class="row mb-2 align-items-center">
                    <label for="id_almacen" class="col-md-3 col-form-label">Almacén</label>
                    <div class="col-md-9">
                        <select name="id_almacen" id="id_almacen" class="form-select" required>
                            <option value="">-- Seleccione un almacén --</option>
                            @foreach($almacenes as $almacen)
                                <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Motivo -->
                <div class="mb-3">
                    <label for="motivo" class="form-label">Motivo</label>
                    <input type="text" class="form-control" id="motivo" name="motivo">
                </div>

                <!-- Glosa -->
                <div class="mb-3">
                    <label for="glosa" class="form-label">Glosa</label>
                    <textarea class="form-control" id="glosa" name="glosa" rows="3"></textarea>
                </div>

                <!-- Tiene pago registrado -->
                <div class="mb-3 d-flex align-items-center">
                    <label class="form-label mb-0 me-3" style="white-space: nowrap;">¿Tiene pago registrado?</label>

                    <div class="d-flex align-items-center">
                        <div class="form-check form-check-inline d-flex align-items-center me-3">
                            <input class="form-check-input me-1" type="radio" name="tiene_pago" id="tiene_pago_si" value="1" required>
                            <label class="form-check-label" for="tiene_pago_si">Sí</label>
                        </div>

                        <div class="form-check form-check-inline d-flex align-items-center">
                            <input class="form-check-input me-1" type="radio" name="tiene_pago" id="tiene_pago_no" value="0">
                            <label class="form-check-label" for="tiene_pago_no">No</label>
                        </div>
                    </div>
                </div>

                <!-- Observación de pago (solo si tiene pago) -->
                <div class="mb-3 d-none" id="obs_pago_group">
                    <label for="obs_pago" class="form-label">Observación del Pago</label>
                    <small class="text-muted d-block mb-1">Indicar si se abonará a otra nota o se devolverá en efectivo.</small>
                    <input type="text" class="form-control" name="obs_pago" id="obs_pago">
                </div>

                <!-- Botones -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Crear Solicitud</button>
                </div>
            </form>
        </div>
        </div>
    </div>
</div>


