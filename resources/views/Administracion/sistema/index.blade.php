@extends('dashboard')

@section('content')
<div class="container">
    <h1>Gestión de Backups</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('backup.realizar') }}" method="POST" style="display:inline-block">
        @csrf
        <button type="submit" class="btn btn-primary">Realizar Backup</button>
    </form>

    <form action="{{ route('backup.restaurar') }}" method="POST" enctype="multipart/form-data" style="display:inline-block; margin-left:20px;">
        @csrf
        <input type="file" name="backup_file" accept=".sql,.txt" required>
        <button type="submit" class="btn btn-warning">Restaurar Backup</button>
    </form>
</div>
@endsection