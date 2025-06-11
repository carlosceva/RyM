@extends('dashboard')

@section('title', 'Permisos')

@section('content')
<div class="card-header">
    <h1 class="card-title" style="font-size: 1.8rem;">
        <i class="fa fa-book mr-1"></i>
        <span>Permisos</span>
    </h1>

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
        <table class="table table-bordered text-center">
            <thead class="thead-dark">
                <tr>
                    <th>Rol</th>
                    <th>Permisos Asignados</th>
                    @can('roles_crear')
                        <th>Acciones</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @php
                    $solicitudes = ['Anulacion', 'Devolucion', 'Precio_especial', 'Sobregiro', 'Baja', 'Muestra', 'usuarios', 'roles','permisos'];
                    $acciones = ['ver', 'crear', 'editar', 'borrar', 'aprobar', 'reprobar', 'ejecutar', 'entrega', 'pago'];
                @endphp

                @foreach ($roles as $role)
                    @php
                        // Inicializamos una variable para verificar si al menos un permiso está marcado
                        $roleHasPermission = false;
                    @endphp

                    <tr>
                        <td><strong>{{ $role->name }}</strong></td>
                        <td>
                            <table class="table table-sm table-bordered text-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Solicitud</th>
                                        @foreach ($acciones as $accion)
                                            <th>{{ ucfirst($accion) }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($solicitudes as $solicitud)
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $solicitud)) }}</td>
                                        @foreach ($acciones as $accion)
                                            @php
                                                $permisoName = "{$solicitud}_{$accion}";
                                                $permiso = $permissions->firstWhere('name', $permisoName);
                                            @endphp
                                            <td>
                                                @if($permiso)
                                                    @if ($role->hasPermissionTo($permiso->name))
                                                        @php
                                                            // Si al menos un permiso está activado, marcar la variable como true
                                                            $roleHasPermission = true;
                                                        @endphp
                                                        <i class="fa fa-check text-success"></i>
                                                    @else
                                                        <i class="fa fa-times text-danger"></i>
                                                    @endif
                                                @else
                                                    <!-- Si el permiso no existe -->
                                                    <span>-</span> <!-- Guion -->
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>
                        @can('roles_crear')
                            <td>
                                <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#modalPermisos{{ $role->id }}">
                                    <i class="fa fa-key"></i> Asignar Permisos
                                </button>
                            </td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODALES DE PERMISOS FUERA DE LA TABLA --}}
@foreach ($roles as $role)
<div class="modal fade" id="modalPermisos{{ $role->id }}" tabindex="-1" role="dialog" aria-labelledby="modalPermisosLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form method="POST" action="{{ route('permisos.guardar', $role->id) }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Asignar Permisos a: {{ $role->name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>Solicitud</th>
                                @foreach ($acciones as $accion)
                                    <th>{{ ucfirst($accion) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($solicitudes as $solicitud)
                            <tr>
                                <td>{{ ucwords(str_replace('_', ' ', $solicitud)) }}</td>
                                @foreach ($acciones as $accion)
                                    @php
                                        $permisoName = "{$solicitud}_{$accion}";
                                        $permiso = $permissions->firstWhere('name', $permisoName);
                                    @endphp
                                    <td>
                                        @if ($permiso)
                                            <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                            {{ $role->hasPermissionTo($permiso->name) ? 'checked' : '' }}>
                                        @else
                                            <span>-</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
@endsection
