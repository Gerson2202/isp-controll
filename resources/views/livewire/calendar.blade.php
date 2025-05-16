<div class="container-fluid min-vh-100 d-flex flex-column">
    <div id="calendar"></div>

        <script>
            document.addEventListener('livewire:load', function () {
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: @this.events.map(event => ({
                        title: event.title,
                        start: event.start
                    })),
                    editable: true,
                    droppable: true,
                    eventClick: function(info) {
                        Swal.fire('Evento: ' + info.event.title); // SweetAlert para el título del evento
                    },
                });
        
                calendar.render();
            });
        </script>
    
    </div>
</div>
