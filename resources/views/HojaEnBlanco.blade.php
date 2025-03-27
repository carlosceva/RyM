@extends('dashboard')

@section('title', 'G. CASO DE USO')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fas fa-image mr-1"></i>
        <span>Gestionar Caso de uso</span>
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
            <table class="table table-hover table-bordered" id="tabla1">
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
                        <td>1</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>5</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>6</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>7</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>8</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>9</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
                        <td>
                                <a href="#" data-toggle="modal" data-target=""><i class="fa fa-edit" aria-hidden="true"></i></a>
                            &nbsp;
                                <a href="#" data-toggle="modal" data-target=""> <i class="fa fa-trash" aria-hidden="true"></i></a>
                            
                        </td>
                    </tr>
                    <tr>
                        <td>10</td>
                        <td>Usuario 1</td>
                        <td>usuario1@netcrow.com</td>
                        <td>Orgrimar</td>
                        <td>77813264</td>
                        <td>Activo</td>
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