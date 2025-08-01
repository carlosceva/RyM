<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addUserModalLabel">Agregar Usuario</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('usuario.store') }}" method="POST">
          @csrf

          <div class="form-row align-items-center">
            <div class="col-4">
              <label for="codigo">Código</label>
            </div>
            <div class="col-8 mb-2">
              <input type="text" class="form-control" id="codigo" name="codigo" required>
            </div>
          </div>

          <div class="form-row align-items-center">
            <div class="col-4">
              <label for="name">Nombre</label>
            </div>
            <div class="col-8 mb-2">
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
          </div>

          <div class="form-row align-items-center">
            <div class="col-4">
              <label for="telefono">Teléfono</label>
            </div>
            <div class="col-8 mb-2">
              <input type="number" class="form-control" id="telefono" name="telefono" required>
            </div>
          </div>

          <div class="form-row align-items-center">
            <div class="col-4">
              <label for="email">E-mail</label>
            </div>
            <div class="col-8 mb-2">
              <input type="email" class="form-control" id="email" name="email">
            </div>
          </div>

          <div class="form-row align-items-center">
            <div class="col-4">
              <label for="rol">Rol</label>
            </div>
            <div class="col-8 mb-2">
              <select name="rol" id="rol" class="form-control" required>
                <option value="">Seleccionar Rol</option>
                @foreach($roles as $rol)
                  <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="form-row align-items-center">
            <div class="col-4">
              <label for="password">Contraseña</label>
            </div>
            <div class="col-8 mb-3">
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
