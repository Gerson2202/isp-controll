@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1>Lista de  Contratos</h1>
@stop

@section('content')
    @livewire('contratos.contratos-list')
@stop

@section('css')
@livewireStyles
<!-- Agrega los estilos de Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Toastr -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

@stop

@section('js')
    @livewireScripts  <!-- Livewire debe cargarse antes que cualquier otro script -->
    <!-- jQuery (requerido por Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Agregar SweetAlert2 desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Aquí incluye tus scripts personalizados -->
    @stack('scripts')

    <script>
        
        // 1. Primero verificamos si Livewire está cargado
        function initializeLivewireEvents() {
            // Configuración de Toastr
            toastr.options = {
                "positionClass": "toast-top-right",
                "progressBar": true,
                "timeOut": 5000,
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "preventDuplicates": true
            };

            // Eventos Livewire
            window.Livewire.on('notify', (data) => {
                toastr[data.type](data.message, data.title || 'Mensaje del sistema');
            });
        }

        // 2. Esperamos a que todo esté listo
        if (window.Livewire) {
            initializeLivewireEvents();
        } else {
            document.addEventListener('livewire:load', function () {
                initializeLivewireEvents();
            });
        }

        // 3. Manejador alternativo por si falla lo anterior
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializeLivewireEvents, 1000);
        });
    </script>
@stop



