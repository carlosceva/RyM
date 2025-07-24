@extends('dashboard')

@section('title', 'Notificaciones')

@section('content_header')
    <h1>Notificaciones</h1>
@stop

@section('content')
    <h3>No leídas</h3>
    <ul class="list-group mb-4">
        @forelse ($noLeidas as $n)
            <li class="list-group-item">
                <a href="{{ route('notificaciones.marcarLeidaYRedirigir', $n->id) }}">
                    {!! nl2br(e($n->mensaje)) !!}
                </a>
                <span class="float-right text-muted text-sm">{{ $n->created_at->diffForHumans() }}</span>
            </li>
        @empty
            <li class="list-group-item">Sin notificaciones no leídas.</li>
        @endforelse
    </ul>

    <!-- Paginación manual para las no leídas -->
    <div class="d-flex justify-content-center">
        <ul class="pagination">
            <!-- Enlace "Anterior" -->
            @if ($page > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ route('notificaciones.index', ['page' => $page - 1]) }}">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Números de página -->
            @for ($i = 1; $i <= $totalPagesNoLeidas; $i++)
                <li class="page-item {{ $i == $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ route('notificaciones.index', ['page' => $i]) }}">{{ $i }}</a>
                </li>
            @endfor

            <!-- Enlace "Siguiente" -->
            @if ($page < $totalPagesNoLeidas)
                <li class="page-item">
                    <a class="page-link" href="{{ route('notificaciones.index', ['page' => $page + 1]) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </div>

    <h3>Leídas</h3>
    <ul class="list-group">
        @forelse ($leidas as $n)
            <li class="list-group-item">
                {!! nl2br(e($n->mensaje)) !!}
                <span class="float-right text-muted text-sm">{{ $n->created_at->diffForHumans() }}</span>
            </li>
        @empty
            <li class="list-group-item">Sin notificaciones leídas.</li>
        @endforelse
    </ul>

    <!-- Paginación manual para las leídas -->
    <div class="d-flex justify-content-center">
        <ul class="pagination">
            <!-- Enlace "Anterior" -->
            @if ($page > 1)
                <li class="page-item">
                    <a class="page-link" href="{{ route('notificaciones.index', ['page' => $page - 1]) }}">Anterior</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Anterior</span>
                </li>
            @endif

            <!-- Números de página -->
            @for ($i = 1; $i <= $totalPagesLeidas; $i++)
                <li class="page-item {{ $i == $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ route('notificaciones.index', ['page' => $i]) }}">{{ $i }}</a>
                </li>
            @endfor

            <!-- Enlace "Siguiente" -->
            @if ($page < $totalPagesLeidas)
                <li class="page-item">
                    <a class="page-link" href="{{ route('notificaciones.index', ['page' => $page + 1]) }}">Siguiente</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Siguiente</span>
                </li>
            @endif
        </ul>
    </div>
@stop
