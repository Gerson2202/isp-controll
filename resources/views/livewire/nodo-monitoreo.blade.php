<div>
  <div class="container-fluid">
    <div class="row">
        <!-- Columna de lista de nodos -->
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0"><i class="bi bi-server me-2"></i>Lista de Nodos</h3>
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <input type="text" class="form-control" placeholder="Buscar nodo...">
                            <button class="btn btn-light" type="button"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">ID</th>
                                    <th width="35%">Nombre</th>
                                    <th width="15%">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nodos as $nodo)
                                <tr>
                                    <td class="fw-bold">{{$nodo->id}}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-pc-display me-2 text-primary"></i>
                                            <span>{{$nodo->nombre}}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <button wire:click="selectNodo({{ $nodo->id }})" 
                                                type="button" 
                                                class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-graph-up"></i> Ver
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna de monitor de nodo -->
        <div class="col-lg-6">
            @if($selectedNodoId)
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h3 class="card-title mb-0"><i class="bi bi-monitor me-2"></i>Monitor de Nodo: {{ $nodoNombre }}</h3>
                </div>
                <div class="card-body">
                    <!-- Información del sistema -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-clock-history me-2"></i>Tiempo de Encendido</h5>
                                    <p class="card-text display-6">12d 4h 35m</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="bi bi-cpu me-2"></i>Uso de CPU</h5>
                                    <div class="d-flex align-items-center">
                                        <div class="progress w-75 me-2" style="height: 20px;">
                                            <div class="progress-bar bg-warning progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" 
                                                 style="width: 45%;" 
                                                 aria-valuenow="45" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                            </div>
                                        </div>
                                        <span class="fw-bold">45%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón para cargar interfaces -->
                    <div class="d-grid gap-2 mb-4">
                        <button wire:click="loadInterfaces"
                                class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-repeat me-2"></i>Cargar Interfaces
                        </button>
                    </div>

                    <!-- Mensajes de error -->
                    @if($errorMessage)
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Error:</strong> {{ $errorMessage }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Tabla de interfaces -->
                    @if(!empty($interfaces))
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Interfaz</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Rx (Mbps)</th>
                                    <th>Tx (Mbps)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($interfaces as $interface)
                                @php
                                    $stats = collect($interfaceStats)->firstWhere('name', $interface['name']);
                                @endphp
                                <tr>
                                    <td>
                                        <i class="bi bi-ethernet me-2"></i>{{ $interface['name'] }}
                                    </td>
                                    <td>{{ $interface['type'] }}</td>
                                    <td>
                                      @if(strtolower($interface['running']) === 'true')
                                          <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Arriba</span>
                                      @else
                                          <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Caído</span>
                                      @endif
                                    </td>
                                    <td class="text-primary fw-bold">
                                        <i class="bi bi-arrow-down me-1"></i>{{ $stats['rx'] ?? '0.00' }}
                                    </td>
                                    <td class="text-success fw-bold">
                                        <i class="bi bi-arrow-up me-1"></i>{{ $stats['tx'] ?? '0.00' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            {{-- Para que se actualice cada 1seg --}}
                            <div wire:poll.1s="loadInterfaces">
                              <!-- El contenido de la tabla y las estadísticas se actualizará cada 5 segundos -->
                            </div>
                        </table>
                        
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-hdd-network display-4 text-muted"></i>
                        <h4 class="mt-3 text-muted">No hay datos de interfaces</h4>
                        <p class="text-muted">Selecciona un nodo y haz clic en "Cargar Interfaces"</p>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Última actualización: {{ now()->format('d/m/Y H:i:s') }}
                        <span wire:poll.1s class="float-end">
                            <i class="bi bi-arrow-clockwise me-1"></i>Actualizando cada 1s
                        </span>
                    </small>
                </div>
            </div>
            @else
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <i class="bi bi-pc-display-horizontal display-4 text-muted mb-3"></i>
                    <h4 class="text-muted mb-3">No hay nodo seleccionado</h4>
                    <p class="text-muted text-center">Selecciona un nodo de la lista para ver su monitorización</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Incluir Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</div>