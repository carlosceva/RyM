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

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="codigo">Código</label>
            </div>
            <div class="col-8">
              <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $usuario->codigo }}">
            </div>
          </div>

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="name">Nombre</label>
            </div>
            <div class="col-8">
              <input type="text" class="form-control" id="name" name="name" value="{{ $usuario->name }}">
            </div>
          </div>

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="telefono">Teléfono</label>
            </div>
            <div class="col-8">
              <input type="number" class="form-control" id="telefono" name="telefono" value="{{ $usuario->telefono }}">
            </div>
          </div>

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="email">E-mail</label>
            </div>
            <div class="col-8">
              <input type="email" class="form-control" id="email" name="email" value="{{ $usuario->email }}">
            </div>
          </div>

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="rol">Rol</label>
            </div>
            <div class="col-8">
              <select class="form-control" id="rol" name="rol">
                <option value="">Seleccionar Rol</option>
                @foreach($roles as $rol)
                  <option value="{{ $rol->name }}" {{ in_array($rol->name, $usuario->roles->pluck('name')->toArray()) ? 'selected' : '' }}>
                    {{ $rol->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="estado">Estado</label>
            </div>
            <div class="col-8">
              <select class="form-control" id="estado" name="estado">
                <option value="a" {{ $usuario->estado == 'a' ? 'selected' : '' }}>Activo</option>
                <option value="i" {{ $usuario->estado == 'i' ? 'selected' : '' }}>Inactivo</option>
              </select>
            </div>
          </div>

          <div class="form-row align-items-center mb-2">
            <div class="col-4">
              <label for="password">Nueva Contraseña</label>
            </div>
            <div class="col-8">
              <input type="password" class="form-control" id="password" name="password" placeholder="Dejar vacío si no quiere cambiarla">
            </div>
          </div>

          <div class="form-row align-items-center mb-3">
            <div class="col-4">
              <label for="password_confirmation">Confirmar Contraseña</label>
            </div>
            <div class="col-8">
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Repita la nueva contraseña">
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
