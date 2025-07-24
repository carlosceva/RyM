@extends('dashboard')

@section('content')

<div class="container mt-4">
    <div class="card shadow rounded-lg">
        <div class="card-header bg-success text-white d-flex align-items-center">
            <i class="fab fa-whatsapp fa-lg mr-2"></i>
            <h5 class="mb-0">Configuración de Notificaciones por WhatsApp</h5>
        </div>
        <div class="card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="switch_twilio"
                       {{ $notificacionesTwilio === '1' ? 'checked' : '' }}>
                <label class="form-check-label font-weight-bold" for="switch_twilio">
                    Activar notificaciones por WhatsApp (Twilio)
                </label>
            </div>

            <div id="estado_mensaje" class="alert {{ $notificacionesTwilio === '1' ? 'alert-success' : 'alert-danger' }}">
                {{ $notificacionesTwilio === '1' 
                    ? '✅ Las notificaciones por WhatsApp están activadas.' 
                    : '⚠️ Las notificaciones por WhatsApp están desactivadas.' }}
            </div>
        </div>
    </div>

    <div class="card shadow rounded-lg">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <i class="fa fa-hdd fa-lg mr-2"></i>
            <h5 class="mb-0">Gestión de Backups</h5>
        </div>
        <div class="card-body">
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
    </div>
</div>

<!-- FontAwesome para el ícono de WhatsApp -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- jQuery ya está cargado en AdminLTE, pero lo dejamos por si acaso -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $('#switch_twilio').on('change', function () {
        let estado = $(this).is(':checked') ? 1 : 0;

        fetch("{{ route('configuracion.twilio') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ estado: estado })
        }).then(response => {
            if (!response.ok) {
                alert("Error guardando configuración.");
                return;
            }

            // Actualizar mensaje dinámicamente
            let mensaje = $('#estado_mensaje');
            if (estado === 1) {
                mensaje
                    .removeClass('alert-danger')
                    .addClass('alert-success')
                    .text('✅ Las notificaciones por WhatsApp están activadas.');
            } else {
                mensaje
                    .removeClass('alert-success')
                    .addClass('alert-danger')
                    .text('⚠️ Las notificaciones por WhatsApp están desactivadas.');
            }
        });
    });
</script>

@endsection
