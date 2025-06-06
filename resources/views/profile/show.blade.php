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

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Perfil de Usuario') }}</div>

                <div class="card-body">
                    <!-- Nombre -->
                    <div class="form-group row">
                        <label for="name" class="col-md-2 col-form-label">Nombre</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="name" name="name" value="{{ Auth::user()->name }}" disabled>
                        </div>
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="form-group row">
                        <label for="email" class="col-md-2 col-form-label">E-mail</label>
                        <div class="col-md-10">
                            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" disabled>
                        </div>
                    </div>

                    <!-- Teléfono -->
                    <div class="form-group row">
                        <label for="telefono" class="col-md-2 col-form-label">Teléfono</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ Auth::user()->telefono }}" disabled>
                        </div>
                    </div>

                    <!-- Código -->
                    <div class="form-group row">
                        <label for="codigo" class="col-md-2 col-form-label">Código</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="codigo" name="codigo" value="{{ Auth::user()->codigo }}" disabled>
                        </div>
                    </div>

                    <!-- Rol (usando Spatie) -->
                    <div class="form-group row">
                        <label for="rol" class="col-md-2 col-form-label">Rol</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="rol" name="rol"
                                value="{{ Auth::user()->getRoleNames()->implode(', ') }}" disabled>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-group row">
                        <label for="estado" class="col-md-2 col-form-label">Estado</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="estado" name="estado" value="{{ Auth::user()->estado == 'a' ? 'Activo' : 'Inactivo' }}" disabled>
                        </div>
                    </div>

                    <!-- Botón para abrir el modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editProfileModal">
                        Editar Información
                    </button>
                    <!-- Botón para cambiar contraseña -->
                    <a href="#" class="btn btn-secondary" data-toggle="modal" data-target="#changePasswordModal">Cambiar Contraseña</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar la información del perfil -->
<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Editar Información del Perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editProfileForm" method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="form-group row">
                        <label for="name" class="col-md-2 col-form-label">Nombre</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-md-2 col-form-label">Email</label>
                        <div class="col-md-10">
                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="telefono" class="col-md-2 col-form-label">Teléfono</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $user->telefono }}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="codigo" class="col-md-2 col-form-label">Código</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $user->codigo }}">
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="form-group row">
                        <label for="estado" class="col-md-2 col-form-label">Estado</label>
                        <div class="col-md-10">
                            <select class="form-control" id="estado" name="estado">
                                <option value="a" {{ $user->estado == 'a' ? 'selected' : '' }}>Activo</option>
                                <option value="i" {{ $user->estado == 'i' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
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


