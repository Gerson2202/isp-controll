<div>
    {{-- Buscador --}}
    <div class="mb-3">
        <input wire:model.live="search" type="text" class="form-control"
            placeholder="Buscar por id de ticket o nombre de cliente...">

    </div>


    {{-- Tabla de visitas --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th># Ticket</th>
                        <th>Cliente</th>
                        <th>Dirección</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($visitas as $visita)
                        <tr>
                            <td>{{ $visita->ticket->id ?? 'N/A' }}</td>
                            <td>{{ $visita->ticket->cliente->nombre ?? 'N/A' }}</td>
                            <td>{{ $visita->ticket->cliente->direccion ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $visita->estado == 'Pendiente' ? 'warning' : 'info' }}">
                                    {{ $visita->estado }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="verInformacion({{ $visita->id }})" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Ver información
                                </button>

                                <button wire:click="cerrarVisita({{ $visita->id }})" class="btn btn-sm btn-success">
                                    <i class="bi bi-check-circle"></i> Cerrar visita
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No hay visitas pendientes o en proceso.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-3 d-flex justify-content-center">
                {{ $visitas->links() }}
            </div>
        </div>
    </div>
    {{-- Modal --}}
    <div wire:ignore.self class="modal fade" id="modalInfo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-clipboard-check me-2"></i>Detalles de la Visita
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body bg-light">
                    @if ($visitaSeleccionada)
                        <div class="row g-4">
                            {{-- Información del Ticket --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-header bg-secondary text-white fw-bold">
                                        <i class="bi bi-ticket-perforated me-2"></i>Información del Ticket
                                    </div>
                                    <div class="card-body">
                                        <p><strong>ID del Ticket:</strong> {{ $visitaSeleccionada->ticket->id }}</p>
                                        <p><strong>Tipo de Reporte:</strong>
                                            {{ $visitaSeleccionada->ticket->tipo_reporte ?? 'N/A' }}</p>
                                        <p><strong>Situación:</strong>
                                            {{ $visitaSeleccionada->ticket->situacion ?? 'N/A' }}</p>
                                        <p><strong>Tarea de visita:</strong>
                                            {{ $visitaSeleccionada->descripcion ?? 'N/A' }}</p>
                                        <p><strong>Estado Visita:</strong>
                                            <span
                                                class="badge bg-{{ $visitaSeleccionada->estado == 'Pendiente' ? 'warning' : ($visitaSeleccionada->estado == 'En progreso' ? 'info' : 'success') }}">
                                                {{ $visitaSeleccionada->estado }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Información del Cliente --}}
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-header bg-secondary text-white fw-bold">
                                        <i class="bi bi-person-lines-fill me-2"></i>Información del Cliente
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Nombre:</strong>
                                            {{ $visitaSeleccionada->ticket->cliente->nombre ?? 'N/A' }}</p>
                                        <p><strong>Dirección:</strong>
                                            {{ $visitaSeleccionada->ticket->cliente->direccion ?? 'N/A' }}</p>
                                        <p><strong>IP:</strong> {{ $visitaSeleccionada->ticket->cliente->ip ?? 'N/A' }}
                                        </p>
                                        <p><strong>Nodo:</strong>
                                            {{ $visitaSeleccionada->ticket->cliente->pool->nodo->nombre ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Técnicos Asignados --}}
                            <div class="col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-secondary text-white fw-bold">
                                        <i class="bi bi-people-fill me-2"></i>Técnicos Asignados
                                    </div>
                                    <div class="card-body">
                                        @if ($visitaSeleccionada->usuarios->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered align-middle mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Nombre</th>
                                                            <th>Correo</th>
                                                            <th>Fecha Inicio</th>
                                                            <th>Fecha Cierre</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($visitaSeleccionada->usuarios as $usuario)
                                                            <tr>
                                                                <td>{{ $usuario->name }}</td>
                                                                <td>{{ $usuario->email }}</td>
                                                                <td>
                                                                    {{ $usuario->pivot->fecha_inicio
                                                                        ? \Carbon\Carbon::parse($usuario->pivot->fecha_inicio)->format('d/m/Y H:i')
                                                                        : 'Sin asignar' }}
                                                                </td>
                                                                <td>
                                                                    {{ $usuario->pivot->fecha_cierre
                                                                        ? \Carbon\Carbon::parse($usuario->pivot->fecha_cierre)->format('d/m/Y H:i')
                                                                        : 'Sin cerrar' }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">No hay técnicos asignados a esta visita.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Información de la Visita --}}
                            <div class="col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-secondary text-white fw-bold">
                                        <i class="bi bi-info-circle me-2"></i>Detalles de la Visita
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Descripción del Problema:</strong></p>
                                        <div class="p-2 border rounded bg-white mb-3">
                                            {{ $visitaSeleccionada->descripcion ?? 'Sin descripción' }}
                                        </div>

                                        <p><strong>Solución (si existe):</strong></p>
                                        <div class="p-2 border rounded bg-white">
                                            {{ $visitaSeleccionada->solucion ?? 'Sin solución registrada aún' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-exclamation-circle fs-3"></i>
                            <p class="mt-2">No hay información para mostrar.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            Livewire.on('abrir-modal', () => {
                const modal = new bootstrap.Modal(document.getElementById('modalInfo'));
                modal.show();
            });
        </script>
    @endpush



</div>
