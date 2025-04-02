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
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
@stop


@section('js')
    @livewireScripts  <!-- Livewire debe cargarse antes que cualquier otro script -->
    
    <!-- Agregar jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Agregar jQuery UI (necesario para el drag-and-drop en FullCalendar) -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
            events: function(start, end, timezone, callback) {
                $.ajax({
                    url: '{{ route('events.index') }}',  // Ruta para obtener los eventos desde el controlador
                    dataType: 'json',
                    success: function(data) {
                        // Asegúrate de que los eventos estén bien formateados antes de pasarlos a FullCalendar
                        console.log(data);  // Para depurar la respuesta JSON
                        callback(data);  // Pasa los eventos a FullCalendar
                    },
                    error: function(xhr, status, error) {
                        console.error('Error al cargar los eventos: ', error);
                    }
                });
            },
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            droppable: true, // Permite arrastrar y soltar los eventos
            editable: true,  // Habilita la edición de eventos (arrastrar y cambiar el tamaño)
            eventDrop: function(event, delta, revertFunc) {
                // Esta función se ejecuta cuando un evento es arrastrado a una nueva fecha
                var newStart = event.start.format('YYYY-MM-DD HH:mm:ss');
                var newEnd = event.end.format('YYYY-MM-DD HH:mm:ss');

                // Enviar la nueva fecha al backend para actualizar el evento
                $.ajax({
                    url: '/events/' + event.visita_id,  // URL de la ruta para actualizar el evento
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',  // Necesario para la protección contra CSRF
                        start: newStart,
                        end: newEnd,
                    },
                    success: function(response) {
                        console.log('Evento actualizado con éxito');
                    },
                    error: function(xhr, status, error) {
                        revertFunc();  // Si hay un error, revertimos la acción de arrastre
                        alert('Hubo un error al actualizar el evento');
                    }
                });
            },
            eventClick: function(event) {
                // Llenar el modal con los datos del evento
                $('#eventModalLabel').text('Detalles del Evento: ' + event.title);
                $('#tipoReporte').text(event.tipo_reporte || 'No disponible');
                $('#situacion').text(event.situacion || 'No disponible');
                $('#descripcion').text(event.descripcion || 'No disponible');
                $('#cliente').text(event.cliente || 'No disponible');
                $('#fechaInicio').text(moment(event.start).format('DD/MM/YYYY HH:mm'));
                $('#fechaCierre').text(moment(event.end).format('DD/MM/YYYY HH:mm'));
                $('#solucion').text(event.solucion || 'No se ha solucionado');

                var editUrl = '/visitas/' + event.visita_id + '/edit';  // Aquí usamos visita_id
                $('#editButton').attr('href', editUrl);  // Establece correctamente el href del botón Editar
                $('#eventModal').modal('show');
            },
        });
    });
</script>


    @stack('scripts')
@stop
