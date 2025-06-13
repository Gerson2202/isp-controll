@extends('adminlte::page')
@section('title', 'Lista de inventario') <!-- Corregí "Dasboard" a "Dashboard" -->

@section('content_header')
   <h1 class="ml-3">Inventario</h1>
   @livewireStyles
    <!-- Agrega los estilos de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Agregar CSS de DataTables -->
    <!-- CSS de DataTables con Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- CSS de DataTables Responsive -->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">


@stop

@section('content')
    <div class="container-fluid ">
        <div class="card">
            <div class="card-header">
                <h3>Equipos en Inventario</h3>
            </div>
            <div class="card-body">
                <!-- Tabla de inventarios -->
                <div class="table-responsive">
                    <table id="inventarios-table" class="table table-striped table-bordered nowrap w-100">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Mac</th>
                                <th>Asignado a</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventarios as $inventario)
                            <tr>
                                <td>{{ $inventario->id }}</td>
                                <td>{{ $inventario->modelo->nombre }}</td>
                                <td>{{ $inventario->descripcion }}</td>
                                <td>{{ $inventario->mac }}</td>
                                <td>
                                    <!-- Lógica para mostrar a qué está asignado el inventario -->
                                    @if($inventario->nodo)
                                    <strong>Nodo:</strong>  {{ $inventario->nodo->nombre }} <!-- Si está asignado a un nodo, mostramos el nombre del nodo -->
                                    @elseif($inventario->cliente)
                                    <strong>Cliente:</strong> {{$inventario->cliente->nombre}}<!-- Si está asignado a un cliente, mostramos "Cliente" -->
                                    @else
                                        No asignado <!-- Si no está asignado a ninguno, mostramos "No asignado" -->
                                    @endif
                                </td>
                                <td>
                                    <!-- Botón para ver detalles -->
                                    <a href="{{ route('equipos.show', $inventario->id) }}" class="btn btn-info btn-sm">Ver detalles</a>
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
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Agregar SweetAlert2 desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Aquí incluye tus scripts personalizados -->
    <!-- Script para activar DataTables -->
    <!-- Agregar jQuery (DataTables depende de jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JS de DataTables Responsive -->
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>


    <!-- Agregar el archivo JS de DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#inventarios-table').DataTable({
                "responsive": true,  // Hace que la tabla sea responsive
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json" // Configuración en español
                }
            });
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

    @stack('scripts')
@stop

