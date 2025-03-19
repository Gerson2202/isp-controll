
@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1>Vista de Equipo</h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  


@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Detalles del Equipo</h4>
    </div>
    <div class="card-body">
        <p><strong>Modelo:</strong> {{ $inventario->modelo }}</p>
        <p><strong>MAC Address:</strong> {{ $inventario->mac }}</p>
        <p><strong>Descripción:</strong> {{ $inventario->descripcion }}</p>
        @if ($inventario->foto)
            <img src="{{ asset('storage/' . $inventario->foto) }}" alt="Foto del inventario" class="img-fluid">
        @endif
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

