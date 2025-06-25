@auth
@php
    function iconoPorTipo($tipo) {
        return match($tipo) {
            'precio_especial' => '<i class="far fa-file-alt mr-2"></i>',
            'Devolucion de Venta' => '<i class="fas fa-undo mr-2"></i>',
            'Anulacion de Venta' => '<i class="far fa-times-circle mr-2"></i>',
            'Sobregiro de Venta' => '<i class="far fa-arrow-alt-circle-up mr-2"></i>',
            'Muestra de Mercaderia' => '<i class="far fa-file-alt mr-2"></i>',
            'Baja de Mercaderia' => '<i class="far fa-trash-alt mr-2"></i>',
            default => '<i class="far fa-bell mr-2"></i>',
        };
    }

    $permisosBase = [
        'precio_especial' => 'Precio_especial',
        'Anulacion de Venta' => 'Anulacion',
        'Devolucion de Venta' => 'Devolucion',
        'Baja de Mercaderia' => 'Baja',
        'Muestra de Mercaderia' => 'Muestra',
        'Sobregiro de Venta' => 'Sobregiro',
    ];

    $user = auth()->user();
    $notificaciones = $user?->unreadNotifications()
        ->where('type', \App\Notifications\SolicitudCreada::class)
        ->take(5)
        ->get();

    // Verificamos permisos globales
    $puedeVerPendientes = $user->hasAnyPermission(array_map(fn($p) => "{$p}_aprobar", $permisosBase));
    $puedeVerPorEjecutar = $user->hasAnyPermission(array_map(fn($p) => "{$p}_ejecutar", $permisosBase));

@endphp

<style>
    @media (min-width: 576px) {
  .custom-dropdown {
    width: 400px;
  }
}

@media (min-width: 768px) {
  .custom-dropdown {
    width: 500px;
  }
}

@media (max-width: 575.98px) {
  .custom-dropdown {
    width: 100vw;
    left: 0 !important;
    right: 0 !important;
    margin: 0 auto;
  }
}

</style>

    <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" id="notificacionesDropdown">
            <i class="far fa-envelope"></i>
            <span class="badge badge-danger navbar-badge">{{ $notificaciones->count() }}</span>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right custom-dropdown">
            <span class="dropdown-item dropdown-header">{{ ($notificaciones->count() == 0) ? 'No hay nuevas solicitudes' : $notificaciones->count().' nuevas solicitudes' }}</span>
            <div class="dropdown-divider"></div>

            {{-- Notificaciones no leídas --}}
            @foreach($notificaciones as $noti)
                <a href="{{ route('notificacion.ver', $noti->id) }}"
                   class="dropdown-item text-wrap bg-warning">
                    <i class="fas fa-file mr-2"></i> {{ $noti->data['mensaje'] }}
                </a>
                <div class="dropdown-divider"></div>
            @endforeach

            {{-- Botón para ver notificaciones leídas --}}
            <a href="#" class="dropdown-item dropdown-footer text-center" id="toggleNotificacionesLeidas">
                Ver notificaciones leídas
            </a>

            {{-- Contenedor oculto de notificaciones leídas --}}
            <div id="contenedorLeidas" style="display: none;">
                @foreach(auth()->user()->readNotifications()->where('type', \App\Notifications\SolicitudCreada::class)->take(10) as $leida)
                    <div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item text-wrap text-muted">
                            <i class="fas fa-file mr-2"></i> {{ $leida->data['mensaje'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </li>


<li class="nav-item dropdown">
    <a class="nav-link position-relative" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>

        {{-- Badge Pendientes --}}
        @if($puedeVerPendientes && $totalPendientes > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge badge-warning" style="font-size: 0.6rem;">
                {{ $totalPendientes }}
            </span>
        @endif

        {{-- Badge Por ejecutar --}}
        @if($puedeVerPorEjecutar && $totalPorEjecutar > 0)
            <span class="position-absolute top-0 start-75 translate-middle badge badge-success" style="font-size: 0.6rem; margin-left: -10px;">
                {{ $totalPorEjecutar }}
            </span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width: 300px;">
        <span class="dropdown-item dropdown-header">
            @if($puedeVerPendientes) {{ $totalPendientes }} Pendientes @endif
            @if($puedeVerPendientes && $puedeVerPorEjecutar) | @endif
            @if($puedeVerPorEjecutar) {{ $totalPorEjecutar }} Por ejecutar @endif
        </span>
        <div class="dropdown-divider"></div>

        @foreach($solicitudesPendientes as $tipo => $pendientes)
            @php 
                $porEjecutar = $solicitudesPorEjecutar[$tipo] ?? 0;
                $permisoBase = $permisosBase[$tipo] ?? null;
                $mostrar = false;
            @endphp

            @if($permisoBase)
                @php
                    $permisoAprobar = "{$permisoBase}_aprobar";
                    $permisoEjecutar = "{$permisoBase}_ejecutar";
                    $puedeAprobar = $pendientes > 0 && $user->can($permisoAprobar);
                    $puedeEjecutar = $porEjecutar > 0 && $user->can($permisoEjecutar);
                    $mostrar = $puedeAprobar || $puedeEjecutar;
                @endphp
            @endif

            @if($mostrar)
                <div class="dropdown-item">
                    <strong>{!! iconoPorTipo($tipo) !!} {{ ucfirst($tipo) }}</strong>
                    <div class="text-right">
                        @if($puedeAprobar)
                            <a href="{{ route($tiposConRutas[$tipo], ['estado' => 'pendiente']) }}"
                               class="badge badge-warning text-dark mr-2"
                               style="cursor:pointer; text-decoration:none;">
                                Pendientes: {{ $pendientes }}
                            </a>
                        @endif
                        @if($puedeEjecutar)
                            <a href="{{ route($tiposConRutas[$tipo], ['estado' => 'aprobada']) }}"
                               class="badge badge-success"
                               style="cursor:pointer; text-decoration:none;">
                                Por ejecutar: {{ $porEjecutar }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="dropdown-divider"></div>
            @endif
        @endforeach
    </div>
</li>
@endauth