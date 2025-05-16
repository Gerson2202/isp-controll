@extends('adminlte::page')
@section('title', 'Dashboard')

@section('content_header')
    <h1 class="m-0 text-dark">Panel de Control</h1>
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
                        <td><a href="#">#{{ $ticket->id }}</a></td>
                        <td>{{ $ticket->cliente->nombre }}</td>
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

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@stop

@section('js')
<!-- Logo en sidebar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var logoItem = document.querySelector('li#sidebar-logo-item');
            if (logoItem) {
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px; margin-top: 30px" alt="Logo" />';
            }
        });
    </script>

@stop