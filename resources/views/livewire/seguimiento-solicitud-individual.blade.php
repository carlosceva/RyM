@props(['solicitud'])

@php
    $ultimaEjecucion = $solicitud->ejecuciones->last();
    $estado = $solicitud->estado;

    // Colores por estado
    $solicitudClass = 'bg-primary text-white'; // siempre visible

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
    $col = '';
    if($tipo === 'devolucion de venta' || $tipo === 'anulacion de venta' || $tipo === 'baja de mercaderia' || $tipo === 'sobregiro de venta'){
        $col = 'col-md-3';
    }else{
        $col = 'col-md-4';
    }
@endphp

<div class="container-fluid">
    <div class="row text-center mb-2">
        <div class="{{ $col }}"><strong>Solicitud #{{ $solicitud->id }}</strong></div>

        @if($tipo === 'baja de mercaderia')
            <div class="{{ $col }}"><strong>Confirmación</strong></div>
        @endif

        <div class="{{ $col }}"><strong>Aprobación</strong></div>

        @if($tipo === 'devolucion de venta' || $tipo === 'anulacion de venta' || $tipo === 'sobregiro de venta')
            <div class="{{ $col }}"><strong>Confirmación</strong></div>
        @endif

        <div class="{{ $col }}"><strong>Ejecución</strong></div>
    </div>

    <div class="row">
        {{-- Bloque 1: Solicitud --}}
        <div class="{{ $col }} mb-3">
            <div class="card {{ $solicitudClass }} shadow border-0 ">
                <div class="card-body text-center">
                    <p>{{ $solicitud->usuario->name ?? '---' }}</p>
                    <p>{{ $solicitud->fecha_solicitud }}</p>
                </div>
            </div>
        </div>

        @if($tipo === 'baja de mercaderia')
            <div class="{{ $col }} mb-3">
                <div class="card {{ $aprobacionClass }} shadow border-0 ">
                    <div class="card-body text-center">
                        @if( $solicitud->estado == 'pendiente')
                            <p class="mb-0">Pendiente</p>
                        @elseif($solicitud->estado == 'confirmada' || $solicitud->estado == 'ejecutada' || $solicitud->estado == 'aprobada')
                            <p>{{ $solicitud->bajaMercaderia->autorizador->name ?? '---' }}</p>
                            <p> {{ $solicitud->bajaMercaderia->fecha_autorizacion ?? 'N/D' }} </p>
                        @else
                            <p> {{ ucfirst($solicitud->estado) }} </p>
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Bloque 2: Aprobación --}}
        <div class="{{ $col }} mb-3">
            <div class="card {{ $aprobacionClass }} shadow border-0 ">
                <div class="card-body text-center">
                    @if($aprobacionRealizada)
                        @if($tipo !== 'sobregiro de venta')
                            @if($estado === 'aprobada' || $estado === 'ejecutada')
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

        @php
            $devolucion = $solicitud->devolucion;

            $boolEstado = function($valor) {
                if (is_null($valor) || $valor === '' || $valor === 'null') return null;
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            };

            $estadoClass = fn($valor) => is_null($valor) ? 'bg-warning' : ($valor ? 'bg-success' : 'bg-danger');
            $estadoTexto = fn($valor) => is_null($valor) ? '<i class="fa fa-question-circle"></i>' : ($valor ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>');

            $tienePago = $boolEstado($devolucion?->tiene_pago);
            $tieneEntrega = $boolEstado($devolucion?->tiene_entrega);
            $entregaFisica = $boolEstado($devolucion?->entrega_fisica);

            $anulacion = $solicitud->anulacion;

            $boolEstadoA = function($valor) {
                if (is_null($valor) || $valor === '' || $valor === 'null') return null;
                return filter_var($valor, FILTER_VALIDATE_BOOLEAN);
            };

            $estadoClassA = fn($valor) => is_null($valor) ? 'bg-warning' : ($valor ? 'bg-success' : 'bg-danger');
            $estadoTextoA = fn($valor) => is_null($valor) ? '<i class="fa fa-question-circle"></i>' : ($valor ? '<i class="fa fa-check-circle"></i>' : '<i class="fa fa-times-circle"></i>');

            $tienePagoA = $boolEstadoA($anulacion?->tiene_pago);
            $tieneEntregaA = $boolEstadoA($anulacion?->tiene_entrega);
            $entregaFisicaA = $boolEstadoA($anulacion?->entrega_fisica);
        @endphp

        @if($tipo === 'anulacion de venta')
            <div class="{{ $col }} mb-3">
                <div class="card shadow border-0">
                    <div class="card-body p-2">
                        <div class="d-flex text-center gap-2 flex-wrap">
                            {{-- Pago --}}
                            <div class="flex-fill">
                                <div class="p-2 rounded  {{ $estadoClassA($tienePagoA) }}">
                                    Pago {!! $estadoTextoA($tienePagoA) !!}
                                </div>
                            </div>
                            {{-- Despacho --}}
                            <div class="flex-fill">
                                <div class="p-2 rounded  {{ $estadoClassA($tieneEntregaA) }}">
                                    Despacho {!! $estadoTextoA($tieneEntregaA) !!}
                                </div>
                            </div>
                            {{-- Entrega --}}
                            <div class="flex-fill">
                                <div class="p-2 rounded  {{ $estadoClassA($entregaFisicaA) }}">
                                    Entrega {!! $estadoTextoA($entregaFisicaA) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($tipo === 'devolucion de venta')
            <div class="{{ $col }} mb-3">
                <div class="card shadow border-0">
                    <div class="card-body p-2">
                        <div class="d-flex text-center gap-2 flex-wrap">
                            {{-- Pago --}}
                            <div class="flex-fill">
                                <div class="p-2 rounded  {{ $estadoClass($tienePago) }}">
                                    Pago {!! $estadoTexto($tienePago) !!}
                                </div>
                            </div>
                            {{-- Despacho --}}
                            <div class="flex-fill">
                                <div class="p-2 rounded  {{ $estadoClass($tieneEntrega) }}">
                                    Despacho {!! $estadoTexto($tieneEntrega) !!}
                                </div>
                            </div>
                            {{-- Entrega --}}
                            <div class="flex-fill">
                                <div class="p-2 rounded  {{ $estadoClass($entregaFisica) }}">
                                    Entrega {!! $estadoTexto($entregaFisica) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($tipo === 'sobregiro de venta')
            <div class="{{ $col }} mb-3">
                <div class="card {{ $aprobacionClass }} shadow border-0 ">
                    <div class="card-body text-center">
                        @if( $solicitud->estado == 'pendiente' || $solicitud->estado == 'aprobada')
                            <p class="mb-0">Pendiente</p>
                        @elseif($solicitud->estado == 'confirmada' || $solicitud->estado == 'ejecutada')
                            <p>{{ $solicitud->sobregiro->confirmador->name ?? '---' }}</p>
                            <p> {{ $solicitud->sobregiro->fecha_confirmacion ?? 'N/D' }} </p>
                        @else
                            <p> {{ ucfirst($solicitud->estado) }} </p>
                            <p>{{ $solicitud->fecha_autorizacion ?? '---' }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Bloque 3: Ejecución --}}
        <div class="{{ $col }} mb-3">
            @if($estado !== 'rechazada')
                <div class="card {{ $ejecucionClass }} shadow border-0 ">
            @else
                <div class="card bg-danger shadow border-0 ">
            @endif
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
