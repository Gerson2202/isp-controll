@extends('adminlte::page')
@section('title', 'Asignar Ip') 

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
 <h1>
    <i class="fas fa-network-wired mr-2 ml-3"></i> <!-- Red cableada -->
    Asignar IP
</h1>
</div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Card Container -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-list-ol mr-2"></i>Clientes pendientes por asignación IP
            </h4>
        </div>
            <div class="container-fluid mt-1">
                <!-- Mostrar mensaje de éxito si existe -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <div class="alert alert-info alert-dismissible fade show mt-1" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Nota importante:</strong> Los clientes con contratos en estado <strong class="text-uppercase">cancelado</strong> no se mostrarán en este listado.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <!-- FIN mensaje de éxito -->
        </div>
            <div class="card-body">
                
                <!-- Mostrar mensaje de error -->
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Tabla de clientes con IP nula -->
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Dirección</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                <tr>
                                    <td>{{ $cliente->id }}</td>
                                    <td>{{ $cliente->nombre }}</td>
                                    <td>{{ $cliente->direccion }}</td>
                                    <td>
                                        <!-- Botón para asignar IP -->
                                        <a href="{{ route('asignarIpCliente', $cliente->id) }}" class="btn btn-success btn-sm">
                                            Asignar IP
                                        </a> 
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@stop

{{-- include footer y logo  --}}
@include('partials.global-footer')
