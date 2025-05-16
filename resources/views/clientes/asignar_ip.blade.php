@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Clientes por asignar IP</h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  


@stop

@section('content')
    <div class="container-fluid ">
        <!-- Card Container -->
        <div class="card">
            <div class="card-header ">
                <h4>Clientes sin IP asignada</h4>
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
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px; margin-top:30px;" alt="Logo" />';
            }
        });
    </script>
    @stack('scripts')
@stop


