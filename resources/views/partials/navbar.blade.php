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

@media (max-width: 768px) {
    .dropdown-menu {
        width: 100%;  /* Hacer el dropdown más ancho en pantallas pequeñas */
    }
    .dropdown-item {
        min-width: 200px; /* Ancho mínimo más pequeño para pantallas pequeñas */
        padding: 8px 12px; /* Ajustamos el padding */
    }
}

</style>

@php
    $notificaciones = auth()->user()?->notificacionesLocalesNoLeidas()->get();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-envelope"></i>
        @if($notificaciones->count())
            <span class="position-absolute top-0 start-50 translate-middle badge badge-danger" style="font-size: 0.6rem; ">
                {{ $notificaciones->count() }}
            </span>
        @endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="max-height: 300px; overflow-y: auto; width: 300px; padding: 0 10px;">
        @forelse ($notificaciones as $n)
            <a href="{{ route('notificaciones.marcarLeidaYRedirigir', $n->id) }}" class="dropdown-item" style="white-space: normal; word-wrap: break-word; padding: 10px 15px; min-width: 250px;">
                <i class="fas fa-envelope mr-2"></i> {!! nl2br(e($n->mensaje)) !!}
                <span class="float-right text-muted text-sm">{{ $n->created_at->diffForHumans() }}</span>
            </a>
        @empty
            <span class="dropdown-item" style="padding: 10px 15px;">Sin notificaciones nuevas</span>
        @endforelse
        <div class="dropdown-divider"></div>
        <a href="{{ route('notificaciones.index') }}" class="dropdown-item dropdown-footer" style="padding: 10px 15px;">Ver todas</a>
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
                    <strong>{!! iconoPorTipo($tipo) !!} 
                        {{ ucfirst($tipo) === 'Baja de Mercaderia' ? 'Ajuste de inventario' : ucfirst($tipo) }}
                    </strong>
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