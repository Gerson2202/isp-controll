<div>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tickets Abiertos</h4>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Buscar por tipo, situación o cliente..."
                                wire:model.live.debounce.300ms="search">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            @if($search)
                            <button class="btn btn-outline-secondary" type="button" 
                                    wire:click="$set('search', '')" title="Limpiar búsqueda">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <select class="form-select" style="width: auto;" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('tipo_reporte')" style="cursor: pointer;">
                                    Tipo de Reporte
                                    @if($sortField === 'tipo_reporte')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('situacion')" style="cursor: pointer;">
                                    Situación
                                    @if($sortField === 'situacion')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                    Fecha
                                    @if($sortField === 'created_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->tipo_reporte }}</td>
                                    <td>{{ $ticket->situacion }}</td>
                                    <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $ticket->cliente->nombre }}</td>
                                    <td>
                                        <a href="{{ route('tickets.edit', $ticket->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        @if($search)
                                            No se encontraron tickets con "{{ $search }}"
                                        @else
                                            No se encontraron tickets abiertos
                                        @endif
                                    </td>
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
        th:hover {
            background-color: #f8f9fa;
        }
        .fa-sort {
            opacity: 0.3;
        }
        .fa-sort:hover {
            opacity: 0.7;
        }
    </style>
    @endpush
</div>