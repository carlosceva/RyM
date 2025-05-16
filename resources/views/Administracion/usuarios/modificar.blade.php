<!-- editar-usuario -->
<div class="modal fade" id="editModal{{ $usuario->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $usuario->id }}">Editar Usuario</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarUsuarioForm" action="{{ route('usuario.update', ['usuario' => $usuario->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group row">
                    <label for="codigo" class="col-md-3 col-form-label">Codigo</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $usuario->codigo }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-md-3 col-form-label">Nombre</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="name" name="name" value="{{ $usuario->name }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="telefono" class="col-md-3 col-form-label">Telefono</label>
                    <div class="col-md-9">
                        <input type="number" class="form-control" id="telefono" name="telefono" value="{{ $usuario->telefono }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-md-3 col-form-label">Email</label>
                    <div class="col-md-9">
                        <input type="email" class="form-control" id="email" name="email" value="{{ $usuario->email }}" >
                    </div>
                </div>

                <div class="form-group row">
                    <label for="key" class="col-md-3 col-form-label">Key</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="key" name="key" value="{{ $usuario->key }}" >
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="estado" class="col-md-3 col-form-label">Estado</label>
                    <div class="col-md-9">
                        <select class="form-control" id="estado" name="estado">
                            <option value="a" {{ $usuario->estado == 'a' ? 'selected' : '' }}>Activo</option>
                            <option value="i" {{ $usuario->estado == 'i' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                </div>

                <!-- Nueva Contraseña -->
                <div class="mb-3">
                        <label for="password" class="form-label">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Dejar vacío si no quiere cambiarla">
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repita la nueva contraseña">
                    </div>
                
                <!-- ... Código posterior ... -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>