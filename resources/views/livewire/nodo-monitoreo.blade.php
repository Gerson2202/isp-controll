<div>
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="bi bi-server me-2"></i>Lista de Nodos</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- Buscador --}}
                        <div class="input-group mb-4">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                                   class="form-control"
                                   placeholder="Buscar por nombre o IP..."
                                   wire:model.live="search">
                            @if($search)
                                <button class="btn btn-outline-secondary"
                                        wire:click="$set('search', '')"
                                        type="button">
                                    <i class="bi bi-x"></i> Limpiar
                                </button>
                            @endif
                        </div>

                        {{-- Tabla --}}
                        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                                    <tr>
                                        <th width="5%">
                                            <button class="btn btn-link btn-sm p-0 text-decoration-none"
                                                    wire:click="sort('id')">
                                                ID
                                                @if($sortBy === 'id')
                                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </button>
                                        </th>
                                        <th width="25%">
                                            <button class="btn btn-link btn-sm p-0 text-decoration-none"
                                                    wire:click="sort('nombre')">
                                                Nombre
                                                @if($sortBy === 'nombre')
                                                    <i class="bi bi-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </button>
                                        </th>
                                       
                                        <th width="15%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nodos as $nodo)
                                        <tr wire:key="nodo-{{ $nodo->id }}">
                                            <td class="fw-bold">{{ $nodo->id }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-pc-display me-2 text-primary"></i>
                                                    <span>{{ $nodo->nombre }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('nodo.estadisticas', $nodo->id) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-graph-up"></i> Ver
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <i class="bi bi-inbox display-4 text-muted"></i>
                                                <h5 class="mt-3 text-muted">No hay nodos disponibles</h5>
                                                <p class="text-muted">Intenta con otra búsqueda o crea un nuevo nodo</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginación --}}
                        <div class="mt-4">
                            {{ $nodos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</div>