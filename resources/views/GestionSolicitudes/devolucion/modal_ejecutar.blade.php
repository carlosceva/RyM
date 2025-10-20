<div class="modal fade" id="modalEjecutar{{ $solicitud->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $solicitud->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-sm">
                <form action="{{ route('devolucion.ejecutar', $solicitud->id) }}" method="POST">
                    @csrf
                    @method('POST')
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalLabel{{ $solicitud->id }}">Confirmar Ejecución</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        @php
                            $devolucion = $solicitud->devolucion;
                            $tienePago = (bool) $devolucion?->tiene_pago;
                            $tieneEntrega = (bool) $devolucion?->tiene_entrega;
                            $entregaFisica = $devolucion?->entrega_fisica;
                            $esAnulacion = !$tienePago && !$tieneEntrega && ($entregaFisica === false || is_null($entregaFisica));
                        @endphp
                        
                        <ul class="list-group mb-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pago registrado:
                                <span class="fw-bold {{ $tienePago ? 'text-success' : 'text-danger' }}">
                                    {{ $tienePago ? 'Sí' : 'No' }}
                                </span>
                            </li>

                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Despacho en sistema registrado:
                                <span class="fw-bold {{ $tieneEntrega ? 'text-success' : 'text-danger' }}">
                                    {{ $tieneEntrega ? 'Sí' : 'No' }}
                                </span>
                            </li>

                            @if (!$tieneEntrega)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Entrega física confirmada:
                                    <span class="fw-bold 
                                        @if (is_null($entregaFisica)) text-secondary 
                                        @elseif ($entregaFisica) text-success 
                                        @else text-danger 
                                        @endif">
                                        @if (is_null($entregaFisica))
                                            Sin confirmar
                                        @elseif ($entregaFisica)
                                            Sí
                                        @else
                                            No
                                        @endif
                                    </span>
                                </li>
                            @endif
                        </ul>

                        @if ($esAnulacion)
                            <div class="alert alert-danger fw-bold text-center">
                                Se procederá como <u>anulación</u>.
                            </div>
                        @else
                            <div class="alert alert-warning fw-bold text-center">
                                Se procederá como <u>devolución</u>.
                            </div>
                        @endif

                        <p class="text-center mt-2">¿Está seguro de ejecutar esta acción?</p>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">{{ $esAnulacion ? 'Convertir' : 'Ejecutar' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>