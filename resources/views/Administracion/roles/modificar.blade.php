<!-- editar-usuario -->
<div class="modal fade" id="editModal{{ $rol->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $rol->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $rol->id }}">Editar Rol</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editarUsuarioForm" action="{{ route('roles.update', ['rol' => $rol->id]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $rol->name }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-control" id="estado" name="estado">
                        <option value="a" {{ $rol->estado == 'a' ? 'selected' : '' }}>Activo</option>
                        <option value="i" {{ $rol->estado == 'i' ? 'selected' : '' }}>Inactivo</option>
                    </select>
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