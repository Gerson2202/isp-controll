@extends('adminlte::page')
@section('title', 'Movimientos') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Registrar Movimientos   </h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">


@stop

@section('content')
<div class="container mt-2">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="movimientosTabs" role="tablist">
               <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="consumibles-tab" data-bs-toggle="tab" 
                            data-bs-target="#consumibles" type="button" role="tab" aria-selected="false">
                        <i class="fas fa-boxes me-1"></i> Movimiento de Consumibles
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link " id="equipos-tab" data-bs-toggle="tab" 
                            data-bs-target="#equipos" type="button" role="tab" aria-selected="true">
                        <i class="fas fa-laptop me-1"></i> Movimiento de Equipos
                    </button>
                </li>
                
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="movimientosTabsContent">
                <div class="tab-pane fade " id="equipos" role="tabpanel">
                    @livewire('inventario.movimiento-equipo')
                </div>
                <div class="tab-pane fade show active" id="consumibles" role="tabpanel">
                    @livewire('inventario.movimiento-consumibles')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

{{-- Footer section --}}
@section('footer')
    <footer class="main-footer text-xs py-1" style="line-height: 1.2;">
        <div class="container-fluid">
            <div class="row align-items-center">
                <!-- Logo y texto -->
                <div class="col-8 col-sm-6">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('img/logo.png') }}" alt="Isprotik Logo" style="height: 18px; margin-right: 8px;">
                        <div>
                            <strong class="text-sm">© {{ date('Y') }} <a href="{{ route('dashboard') }}" class="text-primary" style="text-decoration: none;">Isprotik</a></strong>
                            <span class="text-muted d-none d-md-inline" style="font-size: 0.75rem;"> | Gestión para ISPs</span>
                        </div>
                    </div>
                </div>
                
                <!-- Versión y soporte -->
                <div class="col-4 col-sm-6 text-right">
                    <span class="d-none d-sm-inline text-muted mr-2" style="font-size: 0.75rem;"><strong>v1.0</strong></span>
                    <a href="https://wa.me/573215852059" target="_blank" class="text-muted" style="font-size: 0.75rem; text-decoration: none;">
                        <i class="fas fa-headset"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <style>
        .main-footer {
            background: #f4f6f9;
            border-top: 1px solid #dee2e6;
            padding: 4px 0 !important;
        }
        .main-footer a:hover {
            color: var(--primary) !important;
        }
        .main-footer img {
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        .main-footer img:hover {
            opacity: 1;
        }
    </style>

    <style>
        .main-footer {
            background: #f4f6f9;
            padding: 1rem;
            border-top: 1px solid #dee2e6;
        }
        .main-footer a:hover {
            color: var(--primary) !important;
            text-decoration: none;
        }
    </style>
@stop
@section('css')
    <!-- Puedes agregar estilos personalizados aquí si es necesario -->
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

