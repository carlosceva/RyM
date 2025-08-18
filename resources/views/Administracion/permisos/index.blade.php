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
                    $solicitudes = ['Anulacion', 'Devolucion', 'Precio_especial', 'Sobregiro', 'Baja', 'Muestra'];
                    $acciones = ['ver', 'crear', 'borrar', 'aprobar', 'reprobar', 'ejecutar', 'entrega', 'pago'];
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
                                        <td>
                                            @php
                                                // Mapeo condicional: si la solicitud es 'Baja', la reemplazamos por 'Ajuste de Inv.'
                                                $nombreSolicitud = ($solicitud === 'Baja') ? 'Ajuste de Inv.' : ucwords(str_replace('_', ' ', $solicitud));
                                            @endphp
                                            {{ $nombreSolicitud }}
                                        </td>
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
                                <button class="btn btn-sm mb-2 btn-warning" data-toggle="modal" data-target="#modalPermisos{{ $role->id }}">
                                    <i class="fa fa-key"></i> Asignar Permisos
                                </button>
                                <button class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#modalOtrosPermisos{{ $role->id }}">
                                    <i class="fa fa-key"></i> Otros Permisos
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
            <input type="hidden" name="grupo" value="solicitudes">
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
                                @php
                                    // Mapeo condicional: Si la solicitud es 'Baja', reemplazamos por 'Ajuste de Inv.'
                                    $nombreSolicitud = ($solicitud === 'Baja') ? 'Ajuste de Inv.' : ucwords(str_replace('_', ' ', $solicitud));
                                @endphp
                                <tr>
                                    <td>{{ $nombreSolicitud }}</td>
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


<style>
    .form-check-input:checked {
        background-color: #28a745 !important; /* Verde */
        border-color: #28a745 !important;
    }

    .form-check-input:focus {
        box-shadow: none;
    }

    .permiso-box {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        border-radius: 5px;
        margin-bottom: 1rem;
        transition: background-color 0.3s ease;
    }

    .permiso-box:hover {
        background-color: #f8f9fa;
    }

    .scrollable-body {
        max-height: 400px;
        overflow-y: auto;
    }
</style>


<!-- Otros permisos -->
<div class="modal fade" id="modalOtrosPermisos{{ $role->id }}" tabindex="-1" role="dialog" aria-labelledby="modalOtrosPermisosLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <form method="POST" action="{{ route('permisos.guardar', $role->id) }}">
            @csrf
            <input type="hidden" name="grupo" value="otros">
            <div class="modal-content">
                <div class="modal-header bg-dark text-white">
                    <h5 class="modal-title">Otros Permisos: {{ $role->name }}</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body scrollable-body">
                    @php
                        $solicitudes = ['Anulacion', 'Devolucion', 'Precio_especial', 'Sobregiro', 'Baja', 'Muestra'];
                        $acciones = ['ver', 'crear', 'editar', 'borrar', 'aprobar', 'reprobar', 'ejecutar', 'entrega', 'pago'];
                        $excluir = collect($solicitudes)->flatMap(fn($s) => collect($acciones)->map(fn($a) => strtolower("{$s}_{$a}")));
                        $otrosPermisos = $permissions->reject(fn ($permiso) => $excluir->contains(strtolower($permiso->name)));
                        $agrupados = $otrosPermisos->groupBy(fn($p) => explode('_', $p->name)[0]);
                    @endphp

                    @if ($agrupados->isEmpty())
                        <p class="text-center text-muted">No hay otros permisos disponibles.</p>
                    @else
                        @foreach ($agrupados as $modulo => $permisosModulo)
                            @php
                                $nombreModulo = strtolower($modulo) === 'sistema' ? 'Configuraciones' : ucfirst($modulo);
                            @endphp
                            <div class="permiso-box">
                                <strong class="text-primary text-uppercase d-block mb-2">{{ $nombreModulo }}</strong>
                                <div class="row">
                                    @foreach ($permisosModulo as $permiso)
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                    id="permiso_{{ $permiso->id }}"
                                                    name="permisos[]" value="{{ $permiso->id }}"
                                                    {{ $role->hasPermissionTo($permiso->name) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="permiso_{{ $permiso->id }}">
                                                    {{ ucwords(str_replace('_', ' ', Str::after($permiso->name, $modulo . '_'))) }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    @endif
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
