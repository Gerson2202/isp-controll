<div>
    <h3>Calendario de Visitas</h3>

    <!-- Calendario de FullCalendar -->
    <div id="calendar"></div>

    <hr>

    <h3>Agendar Visita</h3>

    <!-- Formulario para agendar la visita -->
    <form wire:submit.prevent="agendarVisita">
        <div>
            <label for="ticket_id">Seleccionar Ticket:</label>
            <select wire:model="ticket_id" id="ticket_id" required>
                <option value="">Selecciona un Ticket</option>
                @foreach($tickets as $ticket)
                    <option value="{{ $ticket->id }}">{{ $ticket->titulo }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="fecha_inicio">Fecha y Hora de Inicio:</label>
            <input type="datetime-local" wire:model="fecha_inicio" required>
        </div>

        <div>
            <label for="fecha_cierre">Fecha y Hora de Cierre:</label>
            <input type="datetime-local" wire:model="fecha_cierre" required>
        </div>

        <div>
            <label for="usuarios">Seleccionar Usuarios:</label>
            <select wire:model="usuarios" id="usuarios" multiple required>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit">Agendar Visita</button>
    </form>

    @if (session()->has('message'))
        <div>
            {{ session('message') }}
        </div>
    @endif

    <!-- Incluir el script de FullCalendar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.2.0/fullcalendar.min.js"></script>

    <script>
        $(document).ready(function() {
            // Inicializa el calendario con los eventos
            $('#calendar').fullCalendar({
                events: [
                    @foreach($visitas as $visita)
                        {
                            title: 'Ticket: {{ $visita->ticket->titulo }}',
                            start: '{{ $visita->fecha_inicio }}',
                            end: '{{ $visita->fecha_cierre }}',
                            description: 'Asignado a: {{ implode(", ", $visita->usuarios->pluck('name')->toArray()) }}',
                        },
                    @endforeach
                ],
                editable: true,
                droppable: true,
                eventClick: function(event) {
                    // Muestra los detalles de la visita al hacer clic en un evento
                    alert('Visita: ' + event.title + '\nUsuarios: ' + event.description);
                },
            });
        });
    </script>
</div>
