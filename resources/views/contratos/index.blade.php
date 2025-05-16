@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Crear  Contratos</h1>
@stop

@section('content')

    <div class="container-fluid ">
          <!-- Mostrar mensaje de éxito si existe -->
          @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
        <!-- FIN mensaje de éxito -->
    </div>
    <div class="container-fluid ">
        <!-- Card para mostrar los clientes sin contrato -->
        <div class="card">
            <div class="card-header">
                <h4>Clientes sin Contratos</h4>
            </div>
            <div class="card-body">
                <!-- Tabla para mostrar los clientes -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientesSinContrato as $cliente)
                            <tr>
                                <td>{{ $cliente->id }}</td>
                                <td>{{ $cliente->nombre }}</td>
                                <td>{{ $cliente->direccion }}</td>
                                <td>
                                    <!-- Botón para asignar contrato -->
                                    <a href="{{ route('asignarContrato', $cliente->id) }}" class="btn btn-primary">Asignar</a> 
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
@stop

@section('css')
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  
@stop

@section('js')
    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Logo en sidebar-->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var logoItem = document.querySelector('li#sidebar-logo-item');
            if (logoItem) {
                logoItem.innerHTML = '<img src="{{ asset('img/logo.png') }}" style="max-width:120px;max-height:90px; margin-left:70px; margin-top:30px;" alt="Logo" />';
            }
        });
    </script>

@stop

