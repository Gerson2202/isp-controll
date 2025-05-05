@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')

   <h1 class="ml-2">Pagos</h1>

@stop

@section('content') 
    <div class="mt-8">
        @livewire('facturacion.registrar-pago')
    </div>
@stop

@section('css')

    @livewireStyles	
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Livewire Styles -->
@stop

@section('js')
    @livewireScripts <!-- Antes de tu script -->        
    <!-- jQuery (requerido por Toastr) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- Livewire Scripts (DEBE ir antes de nuestro código) -->
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
    {{-- CREACION DE IMAGEN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function generarImagenComprobante() {
            const comprobante = document.getElementById('comprobantePago');

            if (!comprobante) {
                alert('No se encontró el contenido del comprobante.');
                return;
            }

            html2canvas(comprobante, {
                scale: 2, // mejor calidad
                useCORS: true
            }).then(canvas => {
                const imgData = canvas.toDataURL("image/png");

                // Mostrar en nueva pestaña
                const newTab = window.open();
                newTab.document.body.innerHTML = `<img src="${imgData}" style="max-width:100%;">`;

                // Opcional: permitir descarga directa (descomenta si quieres)
                const link = document.createElement('a');
                link.download = 'comprobante_pago.png';
                link.href = imgData;
                link.click();
            });
        }
    </script>
@stop



