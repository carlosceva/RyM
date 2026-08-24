@extends('dashboard')

@section('title', 'Detalle de Solicitud #'.$solicitud->id_visible)

@section('content_header')
    <h1>Detalle de Solicitud #{{ $solicitud->id_visible }}</h1>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('Bonificaciones.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    @include('GestionSolicitudes.bonificacion.detalle_solicitud', ['solicitud' => $solicitud])

    <hr>
    <h5 class="mt-4">Seguimiento de Solicitud</h5>
    <livewire:seguimiento-solicitud :solicitudId="$solicitud->id" />

    @include('GestionSolicitudes.bonificacion.modal_ejecutar', ['solicitud' => $solicitud])

    <!-- Modal para Agregar Observación -->
    @include('GestionSolicitudes.bonificacion.modal_observacion')

    @include('GestionSolicitudes.bonificacion.script_observacion')
@endsection