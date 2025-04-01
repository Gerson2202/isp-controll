@extends('adminlte::page')
@section('title', 'Dashboard')

@section('content_header')
   <h1 class="ml-3">Calendario</h1>
   @livewireStyles
   <!-- Agrega los estilos de Bootstrap -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
   <!-- Agregar los estilos de FullCalendar -->
   <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.css" rel="stylesheet">
@stop

@section('content')
<div class="container-fluid">
    <div class="card">
            <div class="card-header">
                <h5>Programador de actividades</h5>
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

            </div>
            <div class="card-body">
                <div id="calendar"></div> <!-- Aquí se mostrará el calendario -->

            </div>
    </div>
</div>
<!-- Modal para mostrar detalles del evento -->
<div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Detalles del Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Aquí se llenarán los datos dinámicamente -->
                <p><strong>Tipo de Reporte:</strong> <span id="tipoReporte"></span></p>
                <p><strong>Situación:</strong> <span id="situacion"></span></p>
                <p><strong>Descripción de la Visita:</strong> <span id="descripcion"></span></p>
                <p><strong>Cliente:</strong> <span id="cliente"></span></p>
                <p><strong>Fecha de Inicio:</strong> <span id="fechaInicio"></span></p>
                <p><strong>Fecha de Cierre:</strong> <span id="fechaCierre"></span></p>
                <p><strong>Solucion:</strong> <span id="solucion"></span></p>
            </div>
            <div class="modal-footer">
                <!-- Botón Cerrar -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <!-- Botón Editar -->
                <a id="editButton" href="#" class="btn btn-primary">Editar</a>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
    <!-- Aquí puedes agregar tus estilos personalizados si es necesario -->
@stop

@section('js')
    @livewireScripts  <!-- Livewire debe cargarse antes que cualquier otro script -->
    <!-- Agregar los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Agregar SweetAlert2 desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Agregar Moment.js antes de FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
    
    <!-- Agregar FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.2.0/dist/fullcalendar.min.js"></script>
    
   <!-- Inicializar FullCalendar -->
   <script>
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            events: '{{ route('events.index') }}', // Ruta para obtener los eventos desde el controlador
            header: {
                left: 'prev,next today', // Botones de navegación
                center: 'title', // Título del calendario
                right: 'month,agendaWeek,agendaDay' // Botones de vista (mes, semana, día)
            },
             eventClick: function(event) {
                // Llenar el modal con los datos del evento
                $('#eventModalLabel').text('Detalles del Evento: ' + event.title);
                $('#tipoReporte').text(event.tipo_reporte);
                $('#situacion').text(event.situacion);
                $('#descripcion').text(event.descripcion);
                $('#cliente').text(event.cliente);
                $('#fechaInicio').text(event.start.format('DD/MM/YYYY HH:mm'));
                $('#fechaCierre').text(event.end.format('DD/MM/YYYY HH:mm'));
                $('#solucion').text(event.solucion);
                // Asignar la URL de edición con el visita_id
                var editUrl = '/visitas/' + event.visita_id + '/edit'; // Aquí usamos visita_id
                $('#editButton').attr('href', editUrl); // Establece correctamente el href del botón Editar

                // Mostrar el modal
                $('#eventModal').modal('show');
                }
            });
    });
    </script>


    
    @stack('scripts')
@stop
