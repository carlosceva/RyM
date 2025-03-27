@extends('dashboard')

@section('title', 'Anulacion')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="far fa-times-circle mr-1"></i>
        <span>Anulacion de Venta</span>
    </h1>
    
    <div class="float-right d-sm-block"> 
        <div class="btn-group" role="group" aria-label="Basic mixed styles example">
            <a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success"><i class="fa fa-plus"></i>&nbsp; Agregar</a>
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
                        <th>NOMBRE</th>
                        <th>USUARIO</th>
                        <th>DIRECCION</th>
                        <th>TELEFONO</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
    
@endsection