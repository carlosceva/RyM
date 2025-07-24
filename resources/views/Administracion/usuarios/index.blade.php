@extends('dashboard')

@section('title', 'Usuarios')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fa fa-users mr-1"></i>
        <span>Usuarios</span>
    </h1>
    @can('usuarios_crear')
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-toggle="modal" data-target="#addUserModal" class="btn btn-success"><i class="fa fa-plus-circle"></i>&nbsp; Agregar</a>
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
    <div class="card table-responsive">
        <div class="card-body">
            <table class="table table-hover table-bordered" id="usuario">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>codigo</th>
                        <th>Nombre</th>
                        <th>Telefono</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        @if (auth()->user()->can('usuarios_editar') || auth()->user()->can('usuarios_borrar'))
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    @foreach ($usuarios as $usuario)
                    @php
                        $estado = Str::lower(trim($usuario->estado));
                        $claseFila = '';

                        if ($estado === 'i') {
                            $claseFila = 'table-danger';
                        }
                    @endphp

                    <tr class="{{ $claseFila }}">
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->codigo }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->telefono }}</td>
                        <td>
                            {{ $usuario->roles->pluck('name')->join(', ') }}
                        </td>
                        <td>
                            <?= $usuario->estado === 'a' ? 'Activo' : ($usuario->estado === 'i' ? 'Inactivo' : '') ?>
                        </td>
                            @if (auth()->user()->can('usuarios_editar') || auth()->user()->can('usuarios_borrar'))
                                <td>
                                    @can('usuarios_editar')
                                        <a href="#" data-toggle="modal" data-target="#editModal{{ $usuario->id }}" title="Editar" ><i class="fa fa-edit" aria-hidden="true"></i></a>
                                        &nbsp;
                                    @endcan
                                    @can('usuarios_borrar')
                                        <a href="#" data-toggle="modal" data-target="#deleteModal{{ $usuario->id }}" title="Eliminar" style="color:#dc3545"> <i class="fa fa-trash" aria-hidden="true"></i></a>
                                        &nbsp;
                                    @endcan
                                </td>
                            @endif
                    </tr>
                    @include('Administracion.usuarios.modificar', ['usuario' => $usuario])
                    @include('Administracion.usuarios.eliminar', ['usuario' => $usuario])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('Administracion.usuarios.agregar')

@endsection