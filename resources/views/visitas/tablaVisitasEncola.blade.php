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
            <!-- Tabla de visitas en cola -->
            <table class="table">
                <thead>
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
                            <td>{{ $visita->ticket && $visita->ticket->cliente ? $visita->ticket->cliente->nombre : 'Sin cliente' }}</td>
                            <td>
                                <!-- Botón para agendar -->
                               <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-primary">Agendar</a> 
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
    <!-- Aquí incluye tus scripts personalizados -->
    @stack('scripts')
@stop

