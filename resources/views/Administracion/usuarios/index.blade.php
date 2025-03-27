@extends('dashboard')

@section('title', 'Usuarios')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fa fa-users mr-1"></i>
        <span>Usuarios</span>
    </h1>
    
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-toggle="modal" data-target="#addUserModal" class="btn btn-success"><i class="fa fa-plus-circle"></i>&nbsp; Agregar</a>
        </div> 
    </div>
                
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
    <div class="card table-responsive">
        <div class="card-body">
            <table class="table table-hover table-bordered" id="usuario">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            {{ $usuario->roles->pluck('name')->join(', ') }}
                        </td>
                        <td>
                            <?= $usuario->estado === 'a' ? 'Activo' : ($usuario->estado === 'i' ? 'Inactivo' : '') ?>
                        </td>
                        <td>
                            <a href="#" data-toggle="modal" data-target="#editModal{{ $usuario->id }}" title="Editar" ><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                            <a href="#" data-toggle="modal" data-target="#deleteModal{{ $usuario->id }}" title="Eliminar" style="color:#dc3545"> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            &nbsp;
                            <!-- Botón para abrir el modal -->
                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#modalAsignarRol{{ $usuario->id }}">
                                Asignar Rol
                            </button>
                        </td>
                    </tr>
                    @include('Administracion.usuarios.modificar', ['usuario' => $usuario])
                    @include('Administracion.usuarios.eliminar', ['usuario' => $usuario])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('Administracion.usuarios.agregar')
   <!-- Modal para asignar rol (por cada usuario) -->
@foreach($usuarios as $usuario)
<div class="modal fade" id="modalAsignarRol{{ $usuario->id }}" tabindex="-1" aria-labelledby="modalAsignarRolLabel{{ $usuario->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAsignarRolLabel{{ $usuario->id }}">Asignar Rol a {{ $usuario->name }}</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('roles.asignar', $usuario->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="rol" class="form-label">Seleccionar Rol:</label>
                        <select name="rol" id="rol" class="form-control">
                            <option value="">Seleccionar Rol</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Asignar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection