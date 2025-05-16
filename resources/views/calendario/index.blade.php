@extends('adminlte::page')

@section('title', 'Calendario de Visitas')

@section('content_header')
    <h1 class="ml-2"><i class="fas fa-calendar-alt"></i> Calendario de Visitas</h1>
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
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="card">
            
                <div id="calendar"></div>
            
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
            <button id="btnVerMapa" class="btn btn-success mt-2" style="display: none;">
                📍 Ver en Mapa
            </button>




            <div class="modal-footer" style="margin-top: 20px; text-align: right;">
                <button id="viewClientBtn" class="btn btn-info" style="display: none;">
                    <i class="fas fa-user"></i> Ver Cliente
                </button>
                <button id="editEventBtn" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Editar Programación
                </button>
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
                    <span class="d-none d-sm-inline text-muted mr-2" style="font-size: 0.75rem;"><strong>v1.2.3</strong></span>
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
                    $('#eventlatitud').text(calEvent.latitud);
                    $('#eventlongitud').text(calEvent.longitud);
                    
                    // Botón editar
                    $('#editEventBtn').off('click').on('click', function() {
                        window.location.href = `/visitas/${calEvent.id}/edit`;
                    });

                    // Botón ver cliente
                    if (calEvent.cliente_id) {
                        $('#viewClientBtn').show().off('click').on('click', function() {
                            window.open(`/clientes/${calEvent.cliente_id}`, '_blank');
                        });
                    } else {
                        $('#viewClientBtn').hide();
                    }

                    // Botón ver en mapa
                    if (calEvent.latitud && calEvent.longitud) {
                        $('#btnVerMapa').show().off('click').on('click', function() {
                            const url = `https://www.google.com/maps?q=${calEvent.latitud},${calEvent.longitud}`;
                            window.open(url, '_blank', 'noopener,noreferrer');
                        });
                    } else {
                        $('#btnVerMapa').hide();
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