@extends('dashboard')

@section('title', 'Perfil de Usuario')

@section('content_header')
    <h1>Informacion de usuario</h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Perfil de Usuario') }}</div>

                <div class="card-body">
                    <!-- Nombre -->
                    <div class="form-group row">
                        <label for="name" class="col-3 col-md-2 col-form-label">Nombre</label>
                        <div class="col-9 col-md-10">
                            <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" disabled>
                        </div>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="form-group row">
                        <label for="email" class="col-3 col-md-2 col-form-label">E-mail</label>
                        <div class="col-9 col-md-10">
                            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" disabled>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group row">
                        <label for="telefono" class="col-3 col-md-2 col-form-label">Teléfono</label>
                        <div class="col-9 col-md-10">
                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ Auth::user()->telefono }}" disabled>
                        </div>
                    </div>

                    <!-- Código -->
                    <div class="form-group row">
                        <label for="codigo" class="col-3 col-md-2 col-form-label">Código</label>
                        <div class="col-9 col-md-10">
                            <input type="text" class="form-control" id="codigo" name="codigo" value="{{ Auth::user()->codigo }}" disabled>
                        </div>
                    </div>

                    <!-- Rol (usando Spatie) -->
                    <div class="form-group row">
                        <label for="rol" class="col-3 col-md-2 col-form-label">Rol</label>
                        <div class="col-9 col-md-10">
                            <input type="text" class="form-control" id="rol" name="rol"
                                value="{{ Auth::user()->getRoleNames()->implode(', ') }}" disabled>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-group row">
                        <label for="estado" class="col-3 col-md-2 col-form-label">Estado</label>
                        <div class="col-9 col-md-10">
                            <input type="text" class="form-control" id="estado" name="estado" value="{{ Auth::user()->estado == 'a' ? 'Activo' : 'Inactivo' }}" disabled>
                        </div>
                    </div>

                    <!-- Botón para cambiar contraseña -->
                    <a href="#" class="btn btn-secondary" data-toggle="modal" data-target="#changePasswordModal">Cambiar Contraseña</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para cambiar la contraseña -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Cambiar Contraseña</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('profile.changePassword') }}">
                    @csrf
                    <div class="form-group">
                        <label for="old_password">Contraseña Actual</label>
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirmation">Confirmar Nueva Contraseña</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection


