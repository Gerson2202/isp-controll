@extends('adminlte::page')
@section('title', 'Dashboard') <!-- Corregí "Dasboard" a "Dashboard" -->

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
    @stack('scripts')
@stop

