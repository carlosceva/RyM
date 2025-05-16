
<div class="card" wire:poll.10s>
    <div class="card-header">
        <h3 class="text-center">Seguidor de Solicitudes</h3>
    </div>
    <div class="card-body">

        @foreach($solicitudes as $solicitud)
            @php
                $ultimaEjecucion = $solicitud->ejecuciones->last();
                $estado = $solicitud->estado;

                $bgClass = match($estado) {
                    'pendiente' => 'bg-warning text-dark',
                    'aprobada' => 'bg-primary text-white',
                    'rechazada' => 'bg-danger text-white',
                    'ejecutada' => 'bg-success text-white',
                    default => 'bg-light'
                };
            @endphp

            <div wire:key="solicitud-{{ $solicitud->id }}" class="d-flex align-items-start justify-content-start mb-3 flash-update">

                {{-- Bloque 1: Solicitud --}}
                <div class="card {{ $bgClass }} shadow border-0 me-3" style="min-width: 220px;">
                    <div class="card-body text-center">
                        <p><strong>{{ $solicitud->tipo }} #{{ $solicitud->id }}</strong></p>
                        <p><strong>Solicitado por:</strong> {{ $solicitud->usuario->name ?? '---' }}</p>
                        <p>{{ $solicitud->fecha_solicitud }}</p>
                    </div>
                </div>

                {{-- Flecha --}}
                <div class="d-flex align-items-center me-3">
                    <span style="font-size: 2rem;">→</span>
                </div>

                {{-- Bloque 2: Estado --}}
                <div class="card {{ $bgClass }} shadow border-0 me-3" style="min-width: 220px;">
                    <div class="card-body text-center">
                        <p><strong>{{ ucfirst($estado) }}</strong></p>

                        @if($estado === 'pendiente')
                            <p class="mb-0">En espera de aprobación</p>
                        @elseif($estado === 'aprobada' || $estado === 'ejecutada')
                            <p><strong>Autorizado por:</strong> {{ optional($solicitud->autorizador)->name ?? '---' }}</p>
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @elseif($estado === 'rechazada')
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @endif
                    </div>
                </div>

                {{-- Flecha y bloque ejecutado (si aplica) --}}
                @if($estado === 'ejecutada')
                    <div class="d-flex align-items-center me-3">
                        <span style="font-size: 2rem;">→</span>
                    </div>

                    <div class="card {{ $bgClass }} shadow border-0" style="min-width: 220px;">
                        <div class="card-body text-center">
                            <p><strong>Ejecutada</strong></p>
                            <p><strong>Ejecutado por: </strong>{{ $ultimaEjecucion?->usuario->name ?? '---' }}</p>
                            <p>{{ $ultimaEjecucion?->fecha_ejecucion ?? '---' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <hr style="border: none; border-top: 1px solid #ccc; margin: 1rem 0;">
        @endforeach
    </div>
</div>
