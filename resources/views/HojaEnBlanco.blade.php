@extends('dashboard')

@section('title', 'Dashboard')

@section('content')

<div class="card mb-4">
    <div class="card-header">
        <h2 class="card-title">Mis Pedidos</h2>
    </div>
    <div class="card-body" style="position: relative; height: 300px; max-width: 100%;">
        <canvas id="graficoMisSolicitudes"></canvas>
    </div>
</div>

<!-- Aquí podrías agregar tarjetas o tabla resumen si lo deseas -->

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('graficoMisSolicitudes').getContext('2d');

    const data = {
        labels: {!! json_encode($tiposSolicitud) !!},
        datasets: [
            {
                label: 'Pendientes',
                backgroundColor: '#fbc02d',
                data: {!! json_encode($pendientesPorTipo) !!}
            },
            {
                label: 'Aprobadas',
                backgroundColor: '#1976d2',
                data: {!! json_encode($aprobadasPorTipo) !!}
            },
            {
                label: 'Rechazadas',
                backgroundColor: '#d32f2f',
                data: {!! json_encode($rechazadasPorTipo) !!}
            },
            {
                label: 'Ejecutadas',
                backgroundColor: '#388e3c',
                data: {!! json_encode($ejecutadasPorTipo) !!}
            }
        ]
    };

    new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
        responsive: true,
        maintainAspectRatio: false,  // <--- muy importante para que use la altura del contenedor
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Estado de Mis Solicitudes por Tipo' }
        },
        scales: {
            x: { beginAtZero: true },
            y: { beginAtZero: true }
        }
    }
});

</script>
@endpush
