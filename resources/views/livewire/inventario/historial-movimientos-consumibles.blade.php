<div class="container mt-2">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-boxes"></i>Movimientos de Consumibles</h5>
        </div>
        <div class="card-body">
            <!-- Filtros -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label">Buscar Consumible</label>
                    <input type="text" class="form-control" wire:model.live="search" 
                           placeholder="Nombre o descripción...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo Movimiento</label>
                    <select class="form-select" wire:model.live="tipo_movimiento">
                        <option value="">Todos</option>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option>
                        <option value="traslado">Traslado</option>
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
                            <th>Consumible</th>
                            <th>Cantidad</th>
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
                                    <strong class="text-primary">{{ $movimiento->consumible->nombre }}</strong><br>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($movimiento->consumible->descripcion, 30) }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-{{ $movimiento->tipo_movimiento === 'entrada' ? 'success' : 'primary' }}">
                                        {{ $movimiento->cantidad }} {{ $movimiento->consumible->unidad ?? 'und' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $this->getBadgeColor($movimiento->tipo_movimiento) }}">
                                        {{ ucfirst($movimiento->tipo_movimiento) }}
                                    </span>
                                </td>
                                <td>
                                    @if($movimiento->origen_id)
                                        @php
                                            $origenNombre = $movimiento->origen_nombre;
                                        @endphp
                                        @if($movimiento->origen_tipo === 'bodega')
                                            <i class="fas fa-warehouse text-primary"></i>
                                        @elseif($movimiento->origen_tipo === 'usuario')
                                            <i class="fas fa-user text-success"></i>
                                        @elseif($movimiento->origen_tipo === 'nodo')
                                            <i class="fas fa-network-wired text-warning"></i>
                                        @elseif($movimiento->origen_tipo === 'cliente')
                                            <i class="fas fa-building text-info"></i>
                                        @endif
                                        {{ $origenNombre }}
                                    @else
                                        <span class="text-muted"><i class="fas fa-times"></i> Entrada</span>
                                    @endif
                                </td>
                                <td>
                                    @if($movimiento->destino_id)
                                        @php
                                            $destinoNombre = $movimiento->destino_nombre;
                                        @endphp
                                        @if($movimiento->destino_tipo === 'bodega')
                                            <i class="fas fa-warehouse text-primary"></i>
                                        @elseif($movimiento->destino_tipo === 'usuario')
                                            <i class="fas fa-user text-success"></i>
                                        @elseif($movimiento->destino_tipo === 'nodo')
                                            <i class="fas fa-network-wired text-warning"></i>
                                        @elseif($movimiento->destino_tipo === 'cliente')
                                            <i class="fas fa-building text-info"></i>
                                        @endif
                                        {{ $destinoNombre }}
                                    @else
                                        <span class="text-muted"><i class="fas fa-times"></i> N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $movimiento->descripcion }}">
                                        {{ \Illuminate\Support\Str::limit($movimiento->descripcion, 40) }}
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-user-circle"></i>
                                    {{ $movimiento->usuario->name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-search fa-2x mb-3"></i><br>
                                    No se encontraron movimientos con los filtros aplicados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($movimientos->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    <div class="btn-group">
                        <button class="btn btn-outline-primary btn-sm" 
                                wire:click="previousPage" 
                                wire:loading.attr="disabled"
                                {{ $movimientos->onFirstPage() ? 'disabled' : '' }}>
                            <i class="fas fa-chevron-left"></i> Anterior
                        </button>
                        
                        <button class="btn btn-outline-primary btn-sm" disabled>
                            Página {{ $movimientos->currentPage() }} de {{ $movimientos->lastPage() }}
                        </button>
                        
                        <button class="btn btn-outline-primary btn-sm" 
                                wire:click="nextPage" 
                                wire:loading.attr="disabled"
                                {{ !$movimientos->hasMorePages() ? 'disabled' : '' }}>
                            Siguiente <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>