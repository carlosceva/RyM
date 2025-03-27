@extends('dashboard')

@section('title', 'Roles')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fa fa-book mr-1"></i>
        <span>Roles</span>
    </h1>
    
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success"><i class="fa fa-plus-circle"></i>&nbsp; Agregar</a>
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
            <table class="table table-hover table-bordered" id="roles">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                @foreach ($roles as $rol)
                    <tr>
                        <td>{{ $rol->id }}</td>
                        <td>{{ $rol->name }}</td>
                        <td>
                            <?= $rol->estado === 'a' ? 'Activo' : ($rol->estado === 'i' ? 'Inactivo' : '') ?>
                        </td>
                        <td>
                                <a href="#" data-toggle="modal" data-target="#editModal{{ $rol->id }}" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target="#deleteModal{{ $rol->id }}" title="Eliminar" style="color:#C70039"> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    @include('Administracion.roles.modificar', ['rol' => $rol])
                    @include('Administracion.roles.eliminar', ['rol' => $rol])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('Administracion.roles.agregar')
@endsection