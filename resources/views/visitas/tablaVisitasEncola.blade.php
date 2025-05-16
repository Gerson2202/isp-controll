@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Cola de programación</h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  


@stop

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Visitas en Cola de Programación</h5> 
        </div>
        <div class="card-body">
            <!-- Contenedor scrollable y responsivo -->
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Descripción</th>
                            <th>#Ticket</th>
                            <th>Cliente</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitasEnCola as $visita)
                            <tr>
                                <td>{{ $visita->id }}</td>
                                <td>{{ $visita->descripcion }}</td>
                                <td>{{ $visita->ticket ? $visita->ticket->id : 'Sin ticket' }}</td>
                                <td>
                                    @if($visita->ticket && $visita->ticket->cliente)
                                        <a href="{{ route('clientes.show', $visita->ticket->cliente->id) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                            {{ $visita->ticket->cliente->nombre }}
                                        </a>
                                    @else
                                        Sin cliente
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-primary btn-sm">Agendar</a> 
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No hay visitas en cola de programación.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div> 
</div>

@stop

@section('css')
    <!-- Puedes agregar estilos personalizados aquí si es necesario -->
@stop

@section('js')
    @livewireScripts  <!-- Livewire debe cargarse antes que cualquier otro script -->
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Agregar SweetAlert2 desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Logo en sidebar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var logoItem = document.querySelector('li#sidebar-logo-item');
            if (logoItem) {
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px;" alt="Logo" />';
            }
        });
    </script>

    @stack('scripts')
@stop

