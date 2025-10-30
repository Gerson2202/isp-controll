<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-grid-3x3-gap me-2"></i>Dashboard de Inventario
                    </h4>
                </div>
                <div class="card-body">
                    {{-- Estadísticas --}}
                    <div class="row mb-4">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-primary">{{ $this->estadisticas['total_bodegas'] }}</h3>
                                    <p class="mb-0 text-muted">Bodegas</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-success">{{ $this->estadisticas['total_clientes'] }}</h3>
                                    <p class="mb-0 text-muted">Clientes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-warning">{{ $this->estadisticas['total_nodos'] }}</h3>
                                    <p class="mb-0 text-muted">Nodos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-body text-center">
                                    <h3 class="text-info">{{ $this->estadisticas['total_usuarios'] }}</h3>
                                    <p class="mb-0 text-muted">Usuarios</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Buscador --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="bi bi-search me-2"></i>Consultar Inventario
                                    </h5>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Tipo de Inventario</label>
                                            <select wire:model.live="tipoSeleccionado" class="form-select">
                                                @foreach($opciones as $valor => $etiqueta)
                                                    <option value="{{ $valor }}">{{ $etiqueta }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold">Buscar</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-search"></i>
                                                </span>
                                                <input type="text" 
                                                       wire:model.live="search" 
                                                       class="form-control" 
                                                       placeholder="Escriba el nombre para buscar...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Resultados --}}
                    @if(!empty($resultados))
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="bi bi-list-ul me-2"></i>
                                            Resultados de Búsqueda ({{ count($resultados) }} encontrados)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            @foreach($resultados as $resultado)
                                                <div class="col-md-6 col-lg-4">
                                                    <div class="card h-100 border">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="card-title mb-0 text-truncate">{{ $resultado['nombre'] }}</h6>
                                                                <span class="badge bg-secondary">{{ $resultado['tipo_display'] }}</span>
                                                            </div>
                                                            <p class="card-text small text-muted mb-0">
                                                                ID: {{ $resultado['id'] }}
                                                            </p>
                                                        </div>
                                                        <div class="card-footer bg-transparent">
                                                            <a href="{{ $resultado['ruta'] }}" 
                                                               class="btn btn-sm btn-outline-primary w-100">
                                                                <i class="bi bi-eye me-1"></i>Ver Inventario
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif(!empty($search))
                        {{-- No hay resultados --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-search display-1 text-muted"></i>
                                        <h5 class="text-muted mt-3">No se encontraron resultados</h5>
                                        <p class="text-muted">Intente con otros términos de búsqueda</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Vista cuando no hay búsqueda --}}
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body text-center py-5">
                                        <i class="bi bi-inboxes display-1 text-muted"></i>
                                        <h5 class="text-muted mt-3">Seleccione un tipo y busque para ver el inventario</h5>
                                        <p class="text-muted">Puede buscar entre bodegas, clientes, nodos y usuarios</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>