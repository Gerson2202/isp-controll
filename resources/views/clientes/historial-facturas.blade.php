@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-4">Histórico de Facturas</h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  


@stop

@section('content')
<div class="container-fluid ">

    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title">Histórico de Facturas: {{ $cliente->nombre }}</h3>
            </div>
            <div class="card-body">
                @livewire('facturacion.historial-facturas', ['cliente' => $cliente])
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
    <!-- Aquí incluye tus scripts personalizados -->
    @stack('scripts')
@stop


