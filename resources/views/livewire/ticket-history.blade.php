<div>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Historial de Tickets</h4>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Buscar..."
                                wire:model.live.debounce.300ms="search">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" wire:model.live="selectedStatus">
                            <option value="">Todos los estados</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- <div class="col-md-3">
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div> --}}
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('tipo_reporte')" style="cursor: pointer;">
                                    Tipo Reporte
                                    @if ($sortField === 'tipo_reporte')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('situacion')" style="cursor: pointer;">
                                    Situación
                                    @if ($sortField === 'situacion')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Cliente</th>
                                <th wire:click="sortBy('fecha_cierre')" style="cursor: pointer;">
                                    Fecha Cierre
                                    @if ($sortField === 'fecha_cierre')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Solucion</th>
                                <th wire:click="sortBy('estado')" style="cursor: pointer;">
                                    Estado
                                    @if ($sortField === 'estado')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.edit', $ticket->id) }}" title="Click para editar"
                                            class="btn btn-sm btn-primary">
                                            {{ $ticket->tipo_reporte }}
                                        </a>
                                    </td>


                                    <td>{{ Str::limit($ticket->situacion, 50) }}</td>
                                    <td>
                                        <a href="{{ route('clientes.show', $ticket->cliente_id) }}"
                                            class="client-link text-success">
                                            <i class="fas fa-user"></i>{{ $ticket->cliente->nombre }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($ticket->fecha_cierre)
                                            {{ \Carbon\Carbon::parse($ticket->fecha_cierre)->format('d/m/Y H:i') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        {{ $ticket->solucion ? Str::limit($ticket->solucion, 50) : 'Sin solución' }}

                                        @if (Str::lower(trim($ticket->solucion)) === 'se agendo visita')
                                            <a href="{{ route('visitas.show', $ticket->visita->id ?? null) }}"
                                                class="btn btn-info btn-sm ms-2">
                                                <i class="bi bi-eye"></i> Ver visita
                                            </a>
                                        @endif
                                    </td>

                                    <td>
                                        <span
                                            class="badge bg-{{ $ticket->estado === 'cerrado' ? 'success' : ($ticket->estado === 'en_proceso' ? 'warning' : 'danger') }}">
                                            {{ $statusOptions[$ticket->estado] ?? $ticket->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron tickets</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Paginación --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $tickets->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
                </div>

            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .badge {
                font-size: 0.9em;
                padding: 0.5em 0.75em;
            }
        </style>
    @endpush
</div>
