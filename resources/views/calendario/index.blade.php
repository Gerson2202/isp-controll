@extends('adminlte::page')

@section('title', 'Calendario de Visitas')

@section('content_header')
    <h1><i class="fas fa-calendar-alt"></i> Calendario de Visitas</h1>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <style>
        #calendar {
            width: 100%;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: 600px;
        }
        .fc-event {
            cursor: pointer;
            padding: 3px 5px;
        }
        .event-details {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            z-index: 1000;
            width: 80%;
            max-width: 500px;
        }
    </style>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal para detalles -->
    <div id="eventDetails" class="event-details" style="display: none;">
        <span class="close-btn" style="float:right;cursor:pointer;font-size:20px;">&times;</span>
        <h3 id="eventTitle"></h3>
        <p><strong>Ticket ID:</strong> <span id="eventTicketId"></span></p>
        <p><strong>Estado:</strong> <span id="eventStatus" class="badge"></span></p>
        <p><strong>Inicio:</strong> <span id="eventStart"></span></p>
        <p><strong>Fin:</strong> <span id="eventEnd"></span></p>
        <p><strong>Descripción:</strong> <span id="eventDescription"></span></p>
        <div class="modal-footer" style="margin-top: 20px; text-align: right;">
            <button id="viewClientBtn" class="btn btn-info" style="display: none;">
                <i class="fas fa-user"></i> Ver Cliente
            </button>
            <button id="editEventBtn" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar Programación
            </button>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Al final del <body> (después de jQuery) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js"></script>

    {{-- Incio de logica para fullcalendar --}}
    <script>
        $(document).ready(function() {
    // Configuración inicial de Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    // Inicializar calendario
    var calendar = $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        defaultView: 'month',
        locale: 'es',
        editable: true,
        eventDurationEditable: true,
        events: {
            url: "{{ route('visitas.calendario') }}",
            method: 'GET',
            failure: function() {
                toastr.error('Error al cargar las visitas');
            }
        },
        eventRender: function(event, element) {
            // Modificar el título para incluir el cliente como enlace
            var titleHtml = `Ticket #${event.ticket_id} - `;
            if (event.cliente_id) {
                titleHtml += `<span class="cliente-link" 
                              style="color: #3A7BFF; cursor: pointer;" 
                              data-cliente-id="${event.cliente_id}">
                              ${event.cliente_nombre}
                             </span>`;
            } else {
                titleHtml += event.cliente_nombre;
            }
            element.find('.fc-title').html(titleHtml);
        },
        eventDrop: function(event, delta, revertFunc) {
            actualizarEvento(event, revertFunc, 'Visita reagendada correctamente');
        },
        eventResize: function(event, delta, revertFunc) {
            actualizarEvento(event, revertFunc, 'Duración de visita actualizada');
        },
        eventClick: function(calEvent, jsEvent, view) {
            $('#eventTitle').text(calEvent.title);
            $('#eventTicketId').text(calEvent.ticket_id);
            $('#eventStatus').text(calEvent.estado);
            $('#eventStatus').removeClass().addClass('badge ' + 
                (calEvent.estado === 'Pendiente' ? 'badge-warning' : 
                 calEvent.estado === 'En progreso' ? 'badge-info' : 
                 'badge-success'));
            $('#eventStart').text(moment(calEvent.start).format('LLLL'));
            $('#eventEnd').text(moment(calEvent.end).format('LLLL'));
            $('#eventDescription').text(calEvent.descripcion);
            
            // Configurar el botón de edición
            $('#editEventBtn').off('click').on('click', function() {
                window.location.href = `/visitas/${calEvent.id}/edit`;
            });
            
            // Configurar el botón para ver cliente si existe
            if (calEvent.cliente_id) {
                $('#viewClientBtn').show().off('click').on('click', function() {
                    window.location.href = `/clientes/${calEvent.cliente_id}`;
                });
            } else {
                $('#viewClientBtn').hide();
            }
            
            $('#eventDetails').fadeIn();
        }
    });

    // Manejar clic en nombre de cliente (fuera del modal)
    $(document).on('click', '.cliente-link', function(e) {
        e.stopPropagation();
        const clienteId = $(this).data('cliente-id');
        window.location.href = `/clientes/${clienteId}`;
    });

    function actualizarEvento(event, revertFunc, mensajeExito) {
        var updateData = {
            fecha_inicio: event.start.format('YYYY-MM-DD HH:mm:ss'),
            fecha_cierre: event.end.format('YYYY-MM-DD HH:mm:ss'),
            _token: "{{ csrf_token() }}"
        };

        // Mostrar notificación de carga
        toastr.info('Actualizando visita...', '', {timeOut: 2000});

        $.ajax({
            url: "/visitas/" + event.id + "/actualizar-fechas",
            method: 'PATCH',
            data: updateData,
            success: function(response) {
                if (response.success) {
                    toastr.success(mensajeExito);
                } else {
                    revertFunc();
                    toastr.error(response.message || 'Error al actualizar');
                }
            },
            error: function(xhr) {
                revertFunc();
                if (xhr.status === 422) {
                    toastr.error(xhr.responseJSON.message || 'Datos inválidos');
                } else {
                    toastr.error('Error de conexión con el servidor');
                }
            }
        });
    }

    $('.close-btn').click(function() {
        $('#eventDetails').fadeOut();
    });
});
    </script>

     {{-- Fin de logica para fullcalendar --}}
@stop