@extends('dashboard')

@section('title', 'Dashboard')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title" style="font-size: 1.8rem;">
            <i class="fas fa-image mr-1"></i>
            <span>Dashboard</span>
        </h1>
    </div>

    <div class="card-body">
        <div class="row">
            @foreach($tarjetas as $card)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="mr-3" style="font-size: 2rem;">
                                    {!! $card['icono'] !!}
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $card['total'] }} solicitudes</h5>
                                    <h4 class="text-muted">{{ $card['titulo'] }}</h4>
                                </div>
                            </div>

                            <div class="mt-3">
                                <a href="{{ route($card['ruta'], ['estado' => 'pendiente']) }}" class="btn btn-warning btn-sm d-block mb-2 text-left">
                                    Ver {{ $card['pendientes'] }} pendientes →
                                </a>
                                <a href="{{ route($card['ruta'], ['estado' => 'por_ejecutar']) }}" class="btn btn-success btn-sm d-block text-left">
                                    Ver {{ $card['por_ejecutar'] }} por ejecutar →
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection