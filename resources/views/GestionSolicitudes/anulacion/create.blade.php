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

                <input type="hidden" name="tipo" value="Anulacion de Venta">
                <input type="hidden" name="id_usuario" value="{{ auth()->id() }}">
                <input type="hidden" name="estado" value="pendiente">

                <!-- Fecha de solicitud -->
                <div class="form-group row align-items-center mb-3">
                <label for="fecha_solicitud" class="col-3 col-form-label fw-bold">Fecha</label>
                <div class="col-9">
                    <input type="text" class="form-control" id="fecha_solicitud" value="{{ now()->format('Y-m-d H:i:s') }}" disabled>
                </div>
                </div>

                <!-- Nota de venta -->
                <div class="form-group row align-items-center mb-3">
                <label for="nota_venta" class="col-3 col-form-label fw-bold"># Nota</label>
                <div class="col-9">
                    <input type="text" class="form-control" id="nota_venta" name="nota_venta">
                </div>
                </div>

                <!-- Almacén -->
                <div class="form-group row align-items-center mb-3">
                <label for="id_almacen" class="col-3 col-form-label fw-bold">Almacén</label>
                <div class="col-9">
                    <select name="id_almacen" id="id_almacen" class="form-control" required>
                    <option value="">-- Seleccione un almacén --</option>
                    @foreach($almacenes as $almacen)
                        <option value="{{ $almacen->id }}">{{ $almacen->nombre }}</option>
                    @endforeach
                    </select>
                </div>
                </div>

                <!-- Motivo -->
                <div class="form-group row align-items-center mb-3">
                <label for="motivo" class="col-3 col-form-label fw-bold">Motivo</label>
                <div class="col-9">
                    <input type="text" class="form-control" id="motivo" name="motivo">
                </div>
                </div>

                <!-- Glosa (textarea, que quede apilado para mejor UX) -->
                <div class="form-group mb-3">
                <label for="glosa" class="fw-bold">Glosa</label>
                <textarea class="form-control" id="glosa" name="glosa" rows="3"></textarea>
                </div>

                <!-- Tiene pago registrado -->
                <div class="form-group row align-items-center mb-3">
                <label class="col-form-label fw-bold mb-0">¿Tiene pago registrado?</label>
                <div class="col-9 d-flex align-items-center">
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tiene_pago" id="tiene_pago_si" value="1" required>
                    <label class="form-check-label" for="tiene_pago_si">Sí</label>
                    </div>
                    <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="tiene_pago" id="tiene_pago_no" value="0">
                    <label class="form-check-label" for="tiene_pago_no">No</label>
                    </div>
                </div>
                </div>

                <!-- Observación de pago -->
                <div class="form-group mb-3 d-none" id="obs_pago_group">
                <label for="obs_pago" class="fw-bold d-block mb-1">Observación del Pago</label>
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
