<!-- resources/views/acceso-no-autorizado.blade.php -->
@extends('dashboard')

@section('title', 'Acceso No Autorizado')

@section('content_header')
    <h1>Acceso No Autorizado</h1>
@stop

@section('content')
    <div class="alert alert-danger">
        No tienes permisos para acceder a esta página.
    </div>
@stop
