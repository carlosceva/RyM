@extends('dashboard')

@section('title', 'Clientes')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fa fa-users mr-1"></i>
        <span>Clientes</span>
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
            <table class="table table-hover table-bordered" id="clientes">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>E-MAIL</th>
                        <th>TELEFONO</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                @foreach ($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->id }}</td>
                        <td>{{ $cliente->name }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>{{ $cliente->telefono }}</td>
                        <td>
                            <?= $cliente->estado === 'a' ? 'Activo' : ($cliente->estado === 'i' ? 'Inactivo' : '') ?>
                        </td>
                        <td>
                                <a href="#" data-toggle="modal" data-target="#editModal{{ $cliente->id }}" title="Editar"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target="#deleteModal{{ $cliente->id }}" title="Eliminar" style="color:#C70039"> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    @include('GestionClientes.clientes.modificar', ['cliente' => $cliente])
                    @include('GestionClientes.clientes.eliminar', ['cliente' => $cliente])
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @include('GestionClientes.clientes.agregar')
@endsection