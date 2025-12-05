@extends('adminlte::page')
@section('title', 'Dashboard')

@section('content_header')
<h1 class="m-0 text-dark d-flex align-items-center">
    <i class="bi bi-speedometer2 me-3 fs-2 text-info"></i>
    Panel de Control
</h1>
@stop

@section('content')
    <div class="row">
        <!-- Tarjetas resumen -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-primary">
                <div class="inner">
                    <h3>{{ $clientesCount }}</h3>
                    <p>Clientes Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('clientesBuscar') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-info">
                <div class="inner">
                    <h3>{{ $equiposCount }}</h3>
                    <p>Equipos Instalados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-network-wired"></i>
                </div>
                <a href="{{ route('inventarioList') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-success">
                <div class="inner">
                    <h3>{{ $nodosCount }}</h3>
                    <p>Nodos Activos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-server"></i>
                </div>
                <a href="{{ route('nodosIndex') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-gradient-warning">
                <div class="inner">
                    <h3>{{ $ticketsAbiertos }}</h3>
                    <p>Tickets Abiertos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <a href="{{ route('ticketsIndex') }}" class="small-box-footer">
                    Más info <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Últimos Tickets -->
    <div class="card">
        <div class="card-header border-transparent">
            <h3 class="card-title">Últimos Tickets</h3>
            <div class="card-tools">
                <span class="badge badge-danger">{{ $ticketsRecientes->count() }} Tickets nuevos</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ticketsRecientes as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.edit', $ticket->id) }}">
                                    #{{ $ticket->id }}
                                </a>
                            </td>

                            <td>
                                <a href="{{ route('clientes.show', $ticket->cliente->id) }}">
                                    {{ $ticket->cliente->nombre }}
                                </a>
                            </td>

                            <td>{{ Str::limit($ticket->tipo_reporte, 30) }}</td>
                            <td>
                                <span class="badge badge-{{ $ticket->estado == 'Abierto' ? 'danger' : 'success' }}">
                                    {{ $ticket->estado }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <a href="{{ route('tickets.historial') }}" class="btn btn-sm btn-secondary float-right">Ver Todos</a>
        </div>
    </div>
@stop
{{-- include footer y logo  --}}
@include('partials.global-footer')
