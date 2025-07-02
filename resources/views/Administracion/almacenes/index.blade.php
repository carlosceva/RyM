@extends('dashboard')

@section('title', 'Almacenes')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-file mr-1"></i>
        <span>Almacenes</span>
    </h1>
    @can('usuarios_crear')
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-bs-toggle="modal" data-bs-target="#crearAlmacenModal" class="btn btn-success"><i class="fa fa-plus-circle"></i>&nbsp; Agregar</a>
        </div> 
    </div>
     @endcan           
</div>
    
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Encargado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($almacenes as $almacen)
                <tr>
                    <td>{{ $almacen->id }}</td>
                    <td>{{ $almacen->nombre }}</td>
                    <td>{{ $almacen->encargado ? $almacen->encargado->name : 'Sin encargado' }}</td>
                    <td>
                        <!-- Botón Editar (modal) -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarAlmacenModal{{ $almacen->id }}">Editar</button>

                        <!-- Botón para abrir modal eliminar -->
                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarAlmacenModal{{ $almacen->id }}">
                            Desactivar
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@foreach($almacenes as $almacen)
<!-- Modal Editar -->
<div class="modal fade" id="editarAlmacenModal{{ $almacen->id }}" tabindex="-1" aria-labelledby="editarAlmacenLabel{{ $almacen->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('almacen.update', $almacen->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title" id="editarAlmacenLabel{{ $almacen->id }}">Editar Almacén</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nombre{{ $almacen->id }}" class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="nombre{{ $almacen->id }}" class="form-control" value="{{ $almacen->nombre }}" required>
                </div>

                <!-- Selector de Encargado -->
                <div class="mb-3">
                    <label for="id_encargado{{ $almacen->id }}" class="form-label">Encargado</label>
                    <select name="id_encargado" id="id_encargado{{ $almacen->id }}" class="form-control">
                        <option value="">Seleccionar encargado</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ $almacen->id_encargado == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Eliminar -->
<div class="modal fade" id="eliminarAlmacenModal{{ $almacen->id }}" tabindex="-1" aria-labelledby="eliminarAlmacenLabel{{ $almacen->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('almacen.destroy', $almacen->id) }}" method="POST" class="modal-content">
            @csrf
            @method('DELETE')

            <div class="modal-header">
                <h5 class="modal-title" id="eliminarAlmacenLabel{{ $almacen->id }}">Confirmar Desactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p>¿Está seguro que desea <strong>desactivar</strong> el almacén <em>"{{ $almacen->nombre }}"</em>?</p>
                <p>Esta acción no eliminará el almacén permanentemente, solo lo marcará como inactivo.</p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Desactivar</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<!-- Modal Crear -->
<div class="modal fade" id="crearAlmacenModal" tabindex="-1" aria-labelledby="crearAlmacenLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('almacen.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="crearAlmacenLabel">Crear Almacén</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nombreCrear" class="form-label">Nombre</label>
                    <input type="text" name="nombre" id="nombreCrear" class="form-control" required>
                </div>

                <!-- Selector de Encargado -->
                <div class="mb-3">
                    <label for="id_encargado" class="form-label">Encargado</label>
                    <select name="id_encargado" id="id_encargado" class="form-control">
                        <option value="">Seleccionar encargado</option>
                        @foreach($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-success">Crear</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.querySelector('#crearAlmacenModal form').addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Creando...';
    });

</script>
@endsection
