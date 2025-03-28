<div>
    
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="card ">
            <div class="card-header">
                <h4 class="card-title">Tickets Abiertos</h4>
            </div>
            <div class="card-body">
                <!-- Tabla con los tickets -->
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Tipo de Reporte</th>
                            <th>Situación</th>
                            <th>Cliente</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->tipo_reporte }}</td>
                                <td>{{ $ticket->situacion }}</td>
                                <td>{{ $ticket->cliente->nombre }}</td>
                                <td>
                                    <!-- Botón para seleccionar el ticket -->
                                    <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-primary">Seleccionar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <!-- Aquí podrías agregar un pie de página o alguna información adicional si es necesario -->
            </div>
        </div>
    </div>
    
    
</div>
