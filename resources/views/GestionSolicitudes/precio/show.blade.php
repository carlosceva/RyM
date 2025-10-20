@extends('dashboard')

@section('title', 'Detalle de Solicitud #'.$solicitud->id)

@section('content_header')
    <h1>Detalle de Solicitud #{{ $solicitud->id }}</h1>
@stop

@section('content')
    <div class="mb-3">
        <a href="{{ route('PrecioEspecial.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>

    @include('GestionSolicitudes.precio.detalle_solicitud', ['solicitud' => $solicitud])

    <hr>
    <h5 class="mt-4">Seguimiento de Solicitud</h5>
    <livewire:seguimiento-solicitud :solicitudId="$solicitud->id" />

    @include('GestionSolicitudes.precio.modal_ejecutar', ['solicitud' => $solicitud])
    @include('GestionSolicitudes.precio.modal_confirmar_venta', ['solicitud' => $solicitud])

    <!-- Modal para Agregar Observación -->
    @include('GestionSolicitudes.precio.modal_observacion')

    @include('GestionSolicitudes.precio.script_observacion')
@endsection
