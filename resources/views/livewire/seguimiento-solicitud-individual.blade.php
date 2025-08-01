@props(['solicitud'])

@php
    $ultimaEjecucion = $solicitud->ejecuciones->last();
    $estado = $solicitud->estado;

    $solicitudClass = 'bg-primary text-white';

    $aprobacionRealizada = in_array($estado, ['aprobada', 'ejecutada', 'rechazada', 'confirmada']);
    $aprobacionClass = match($estado) {
        'pendiente' => 'bg-warning text-dark',
        'aprobada', 'ejecutada' => 'bg-primary text-white',
        'rechazada' => 'bg-danger text-white',
        'confirmada' => 'bg-primary text-white',
        default => 'bg-light text-muted'
    };

    $ejecucionRealizada = $estado === 'ejecutada';
    $ejecucionClass = $ejecucionRealizada
        ? 'bg-success text-white'
        : 'bg-warning text-dark';

    $tipo = Str::lower(trim($solicitud->tipo));
    $col = in_array($tipo, ['devolucion de venta', 'anulacion de venta', 'baja de mercaderia', 'sobregiro de venta']) ? 'col-md-3' : 'col-md-4';
@endphp

<div class="container-fluid">
    <div class="row">
        {{-- Bloque 1: Solicitud --}}
        <div class="{{ $col }} mb-3">
            <h6 class="text-center mb-1"><strong>Solicitud #{{ $solicitud->id }}</strong></h6>
            <div class="card {{ $solicitudClass }} shadow border-0">
                <div class="card-body text-center">
                    <p>{{ $solicitud->usuario->name ?? '---' }}</p>
                    <p>{{ $solicitud->fecha_solicitud }}</p>
                </div>
            </div>
        </div>

        {{-- Bloque 2: Confirmación (Baja de mercadería) --}}
        @if($tipo === 'baja de mercaderia')
            <div class="{{ $col }} mb-3">
                <h6 class="text-center mb-1"><strong>Confirmación</strong></h6>
                <div class="card {{ $aprobacionClass }} shadow border-0">
                    <div class="card-body text-center">
                        @if($estado == 'pendiente')
                            <p class="mb-0">Pendiente</p>
                        @elseif(in_array($estado, ['confirmada', 'ejecutada', 'aprobada']))
                            <p>{{ $solicitud->bajaMercaderia->autorizador->name ?? '---' }}</p>
                            <p>{{ $solicitud->bajaMercaderia->fecha_autorizacion ?? 'N/D' }}</p>
                        @else
                            <p>{{ ucfirst($estado) }}</p>
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Bloque 3: Aprobación --}}
        <div class="{{ $col }} mb-3">
            <h6 class="text-center mb-1"><strong>Aprobación</strong></h6>
            <div class="card {{ $aprobacionClass }} shadow border-0">
                <div class="card-body text-center">
                    @if($aprobacionRealizada)
                        @if($tipo !== 'sobregiro de venta')
                            @if(in_array($estado, ['aprobada', 'ejecutada']))
                                <p>{{ optional($solicitud->autorizador)->name ?? '---' }}</p>
                                <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                            @elseif($estado === 'rechazada')
                                <p>Rechazada</p>
                                <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                            @elseif($estado === 'confirmada')
                                <p class="mb-0">Pendiente</p>
                            @endif
                        @else
                            <p>{{ optional($solicitud->autorizador)->name ?? '---' }}</p>
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @endif
                    @else
                        <p class="mb-0">Pendiente</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bloque 4: Confirmación (otros tipos) --}}
        @php
            $devolucion = $solicitud->devolucion;
            $anulacion = $solicitud->anulacion;

            $bool = fn($v) => is_null($v) || $v === '' || $v === 'null' ? null : filter_var($v, FILTER_VALIDATE_BOOLEAN);
            $estadoClass = fn($v) => is_null($v) ? 'bg-warning' : ($v ? 'bg-success' : 'bg-danger');
            $estadoTexto = fn($v) => is_null($v) ? '<i class="fa fa-question-circle"></i>' : ($v ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>');

            $tienePago = $bool($devolucion?->tiene_pago);
            $tieneEntrega = $bool($devolucion?->tiene_entrega);
            $entregaFisica = $bool($devolucion?->entrega_fisica);

            $tienePagoA = $bool($anulacion?->tiene_pago);
            $tieneEntregaA = $bool($anulacion?->tiene_entrega);
            $entregaFisicaA = $bool($anulacion?->entrega_fisica);
        @endphp

        @if($tipo === 'anulacion de venta')
            <div class="{{ $col }} mb-3">
                <h6 class="text-center mb-1"><strong>Confirmación</strong></h6>
                <div class="card shadow border-0">
                    <div class="card-body p-2">
                        <div class="d-flex text-center gap-2 flex-wrap">
                            <div class="flex-fill">
                                <div class="p-2 rounded {{ $estadoClass($tienePagoA) }}">Pago {!! $estadoTexto($tienePagoA) !!}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="p-2 rounded {{ $estadoClass($tieneEntregaA) }}">Despacho {!! $estadoTexto($tieneEntregaA) !!}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="p-2 rounded {{ $estadoClass($entregaFisicaA) }}">Entrega {!! $estadoTexto($entregaFisicaA) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($tipo === 'devolucion de venta')
            <div class="{{ $col }} mb-3">
                <h6 class="text-center mb-1"><strong>Confirmación</strong></h6>
                <div class="card shadow border-0">
                    <div class="card-body p-2">
                        <div class="d-flex text-center gap-2 flex-wrap">
                            <div class="flex-fill">
                                <div class="p-2 rounded {{ $estadoClass($tienePago) }}">Pago {!! $estadoTexto($tienePago) !!}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="p-2 rounded {{ $estadoClass($tieneEntrega) }}">Despacho {!! $estadoTexto($tieneEntrega) !!}</div>
                            </div>
                            <div class="flex-fill">
                                <div class="p-2 rounded {{ $estadoClass($entregaFisica) }}">Entrega {!! $estadoTexto($entregaFisica) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($tipo === 'sobregiro de venta')
            <div class="{{ $col }} mb-3">
                <h6 class="text-center mb-1"><strong>Confirmación</strong></h6>
                <div class="card {{ $aprobacionClass }} shadow border-0">
                    <div class="card-body text-center">
                        @if(in_array($estado, ['pendiente', 'aprobada']))
                            <p class="mb-0">Pendiente</p>
                        @elseif(in_array($estado, ['confirmada', 'ejecutada']))
                            <p>{{ $solicitud->sobregiro->confirmador->name ?? '---' }}</p>
                            <p>{{ $solicitud->sobregiro->fecha_confirmacion ?? 'N/D' }}</p>
                        @else
                            <p>{{ ucfirst($estado) }}</p>
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Bloque 5: Ejecución --}}
        <div class="{{ $col }} mb-3">
            <h6 class="text-center mb-1"><strong>Ejecución</strong></h6>
            <div class="card {{ $estado !== 'rechazada' ? $ejecucionClass : 'bg-danger' }} shadow border-0">
                <div class="card-body text-center">
                    @if($estado !== 'rechazada')
                        @if($ejecucionRealizada)
                            <p>{{ $ultimaEjecucion?->usuario->name ?? '---' }}</p>
                            <p>{{ $ultimaEjecucion?->fecha_ejecucion ?? '---' }}</p>
                        @else
                            <p class="mb-0">Pendiente</p>
                        @endif
                    @else
                        <p>Rechazada</p>
                        <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
