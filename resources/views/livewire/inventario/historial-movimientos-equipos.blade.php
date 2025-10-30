<div class="container mt-2">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-history"></i> Movimientos de Equipos</h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Buscar Equipo</label>
                    <input type="text" class="form-control" wire:model.live="search"
                        placeholder="MAC, Serial o Descripción...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo Movimiento</label>
                    <select class="form-select" wire:model.live="tipo_movimiento">
                        <option value="">Todos</option>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                        <option value="traslado">Traslado</option>
                        <option value="asignacion">Asignación</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Inicio</label>
                    <input type="date" class="form-control" wire:model.live="fecha_inicio">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Fecha Fin</label>
                    <input type="date" class="form-control" wire:model.live="fecha_fin">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-outline-secondary" wire:click="limpiarFiltros">
                        <i class="fas fa-times"></i> Limpiar Filtros
                    </button>
                </div>
            </div>

            <!-- Tabla de movimientos -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Fecha</th>
                            <th>Equipo</th>
                            <th>Tipo</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Descripción</th>
                            <th>Registrado por</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movimientos as $movimiento)
                            <tr>
                                <td>
                                    <small>{{ $movimiento->created_at->format('d/m/Y') }}</small><br>
                                    <small class="text-muted">{{ $movimiento->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <strong class="text-primary">MAC:</strong> {{ $movimiento->inventario->mac }}<br>
                                    @if ($movimiento->inventario->serial)
                                        <small><strong>Serial:</strong>
                                            {{ $movimiento->inventario->serial }}</small><br>
                                    @endif
                                    <small
                                        class="text-muted">{{ \Illuminate\Support\Str::limit($movimiento->inventario->descripcion, 30) }}</small>
                                    <br>
                                    <small
                                        class="text-muted">{{ \Illuminate\Support\Str::limit($movimiento->inventario->modelo->nombre, 30) }}</small>

                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getBadgeColor($movimiento->tipo_movimiento) }}">
                                        {{ ucfirst($movimiento->tipo_movimiento) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($movimiento->bodegaAnterior)
                                        <i class="fas fa-warehouse text-primary"></i>
                                        <strong>Bodega:</strong> {{ $movimiento->bodegaAnterior->nombre }}
                                    @elseif($movimiento->userAnterior)
                                        <i class="fas fa-user text-success"></i>
                                        <strong>Usuario:</strong> {{ $movimiento->userAnterior->name }}
                                    @elseif($movimiento->nodoAnterior)
                                        <i class="fas fa-network-wired text-warning"></i>
                                        <strong>Nodo:</strong> {{ $movimiento->nodoAnterior->nombre }}
                                    @elseif($movimiento->clienteAnterior)
                                        <i class="fas fa-building text-info"></i>
                                        <strong>Cliente:</strong> {{ $movimiento->clienteAnterior->nombre }}
                                    @else
                                        <span class="text-muted"><i class="fas fa-times"></i> Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($movimiento->bodegaNueva)
                                        <i class="fas fa-warehouse text-primary"></i>
                                        <strong>Bodega:</strong> {{ $movimiento->bodegaNueva->nombre }}
                                    @elseif($movimiento->userNuevo)
                                        <i class="fas fa-user text-success"></i>
                                        <strong>Usuario:</strong> {{ $movimiento->userNuevo->name }}
                                    @elseif($movimiento->nodoNuevo)
                                        <i class="fas fa-network-wired text-warning"></i>
                                        <strong>Nodo:</strong> {{ $movimiento->nodoNuevo->nombre }}
                                    @elseif($movimiento->clienteNuevo)
                                        <i class="fas fa-building text-info"></i>
                                        <strong>Cliente:</strong> {{ $movimiento->clienteNuevo->nombre }}
                                    @else
                                        <span class="text-muted"><i class="fas fa-times"></i> Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $movimiento->descripcion }}">
                                        {{ \Illuminate\Support\Str::limit($movimiento->descripcion, 50) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-user-circle"></i>
                                    {{ $movimiento->usuario->name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-search fa-2x mb-3"></i><br>
                                    No se encontraron movimientos con los filtros aplicados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if ($movimientos->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <div class="btn-group">
                        <button class="btn btn-outline-info btn-sm" wire:click="previousPage"
                            wire:loading.attr="disabled" {{ $movimientos->onFirstPage() ? 'disabled' : '' }}>
                            <i class="fas fa-chevron-left"></i> Anterior
                        </button>

                        <button class="btn btn-outline-info btn-sm" disabled>
                            Página {{ $movimientos->currentPage() }} de {{ $movimientos->lastPage() }}
                        </button>

                        <button class="btn btn-outline-info btn-sm" wire:click="nextPage" wire:loading.attr="disabled"
                            {{ !$movimientos->hasMorePages() ? 'disabled' : '' }}>
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
