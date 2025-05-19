<div>
    <div class="container-fluid">
        {{-- Fila para la lista de nodos --}}
        <div class="row mb-4"> {{-- Añadimos un margen inferior para separar --}}
            <div class="col-12"> {{-- La lista de nodos ocupa todo el ancho --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0"><i class="bi bi-server me-2"></i>Lista de Nodos</h3>
                            {{-- Implementación básica de búsqueda (requiere lógica en el componente) --}}
                            <div class="input-group input-group-sm" style="width: 200px;">
                                <input type="text" class="form-control" placeholder="Buscar nodo..." wire:model.live="search"> {{-- wire:model.live para búsqueda reactiva --}}
                                <button class="btn btn-light" type="button"><i class="bi bi-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="35%">Nombre</th>
                                        <th width="15%">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Filtrar nodos si se implementa la búsqueda en el componente --}}
                                    @foreach($nodos as $nodo)
                                    <tr wire:key="nodo-{{ $nodo->id }}"> {{-- Añadir wire:key para mejor rendimiento de Livewire --}}
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
                                                    class="btn btn-sm btn-outline-primary"
                                                    {{-- Deshabilitar si ya está seleccionado --}}
                                                    @if($selectedNodoId == $nodo->id) disabled @endif
                                                    >
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
        </div>

        {{-- Fila para el monitor de nodo (aparece debajo de la lista de nodos) --}}
        <div class="row">
            <div class="col-12"> {{-- El monitor de nodo ocupa todo el ancho --}}
                @if($selectedNodoId)
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h3 class="card-title mb-0"><i class="bi bi-monitor me-2"></i>Monitor de Nodo: {{ $nodoNombre }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 mb-4">
                            <button wire:click="loadInitialData"
                                    class="btn btn-primary btn-lg"
                                    wire:loading.attr="disabled"> {{-- Deshabilitar mientras carga --}}
                                <span wire:loading wire:target="loadInitialData" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                <i wire:loading.remove wire:target="loadInitialData" class="bi bi-arrow-repeat me-2"></i>
                                Cargar Datos
                            </button>
                        </div>

                        {{-- Indicador de carga principal --}}
                        <div wire:loading.delay class="text-center my-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="text-muted mt-2">Cargando datos del nodo...</p>
                        </div>

                        {{-- Mostrar contenido solo si no está cargando y hay datos --}}
                        @if(!$isLoading && ($interfacesWithStats->isNotEmpty() || !empty($systemResources) || !empty($systemHealth) || $errorMessage))

                        <div class="row mb-4"> {{-- Esta fila interna sigue usando col-md-6 para layout en pantallas medianas/grandes --}}
                            {{-- uptime --}}
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <h6 class="card-title mb-2">
                                            <i class="bi bi-clock-history me-2"></i>Tiempo de Encendido
                                        </h6>
                                        <p class="mb-0 fw-semibold">
                                            {{ $systemResources['formatted_uptime'] ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            {{-- cpu --}}
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <h6 class="card-title mb-2">
                                            <i class="bi bi-cpu me-2"></i>Uso de CPU
                                        </h6>
                                        <div class="position-relative">
                                            @php
                                                // Obtener el porcentaje de CPU o usar 0 como valor por defecto
                                                $cpuLoad = (int)($systemResources['cpu-load'] ?? 0);

                                                // Determinar el color según el porcentaje
                                                $progressBarClass = 'bg-success'; // Por defecto verde
                                                $alertMessage = '';

                                                if ($cpuLoad >= 40 && $cpuLoad <= 75) {
                                                    $progressBarClass = 'bg-warning'; // Amarillo
                                                } elseif ($cpuLoad > 75) {
                                                    $progressBarClass = 'bg-danger'; // Rojo
                                                    $alertMessage = '¡Alerta! CPU supera el 75% de uso';
                                                }
                                            @endphp

                                            <div class="progress" style="height: 16px;">
                                                <div
                                                    class="progress-bar {{ $progressBarClass }} progress-bar-striped progress-bar-animated"
                                                    role="progressbar"
                                                    style="width: {{ $cpuLoad }}%;"
                                                    aria-valuenow="{{ $cpuLoad }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>

                                            <span class="fw-bold text-center position-absolute w-100"
                                                  style="top: 50%; transform: translateY(-50%); left: 0;
                                                         color: {{ $cpuLoad > 50 ? 'white' : 'inherit' }};">
                                                {{ $cpuLoad }}%
                                            </span>

                                            @if($cpuLoad > 75)
                                                <div class="mt-2 alert alert-danger py-1 mb-0 text-center">
                                                    <i class="bi bi-exclamation-triangle-fill"></i> {{ $alertMessage }}
                                                </div>
                                            @endif

                                            @if(!isset($systemResources['cpu-load']))
                                                <div class="mt-2 alert alert-warning py-1 mb-0 text-center">
                                                    <i class="bi bi-exclamation-circle"></i> Datos de CPU no disponibles
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Temperatura y voltaje --}}
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <h6 class="card-title mb-2">
                                            <i class="bi bi-thermometer-half me-2"></i>Temperatura y Voltaje
                                        </h6>
                                        <p class="mb-0 fw-semibold">Temperatura: <strong>{{ $systemHealth['temperature'] ?? 'N/A' }}</strong> </p>
                                        <p class="mb-0 fw-semibold">Voltaje: <strong>{{ $systemHealth['voltage'] ?? 'N/A' }}</strong></p>
                                        @if(!isset($systemHealth['temperature']) && !isset($systemHealth['voltage']))
                                            <div class="mt-2 alert alert-warning py-1 mb-0 text-center">
                                                <i class="bi bi-exclamation-circle"></i> Datos de salud no disponibles
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                             {{-- Memoria --}}
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-body p-3 d-flex flex-column">
                                        <h6 class="card-title mb-2">
                                            <i class="bi bi-memory me-2"></i>Uso de Memoria
                                        </h6>
                                        @php
                                            $totalMemory = (int)($systemResources['total-memory'] ?? 0);
                                            $freeMemory = (int)($systemResources['free-memory'] ?? 0);
                                            $usedMemory = $totalMemory > 0 ? $totalMemory - $freeMemory : 0;
                                            $memoryUsagePercent = $totalMemory > 0 ? round(($usedMemory / $totalMemory) * 100) : 0;

                                            $memoryBarClass = 'bg-success';
                                            if ($memoryUsagePercent >= 60 && $memoryUsagePercent <= 85) {
                                                $memoryBarClass = 'bg-warning';
                                            } elseif ($memoryUsagePercent > 85) {
                                                $memoryBarClass = 'bg-danger';
                                            }
                                        @endphp
                                         <div class="position-relative">
                                            <div class="progress" style="height: 16px;">
                                                <div
                                                    class="progress-bar {{ $memoryBarClass }} progress-bar-striped progress-bar-animated"
                                                    role="progressbar"
                                                    style="width: {{ $memoryUsagePercent }}%;"
                                                    aria-valuenow="{{ $memoryUsagePercent }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="fw-bold text-center position-absolute w-100"
                                                  style="top: 50%; transform: translateY(-50%); left: 0;
                                                         color: {{ $memoryUsagePercent > 50 ? 'white' : 'inherit' }};">
                                                {{ $memoryUsagePercent }}%
                                            </span>
                                         </div>
                                        <p class="mb-0 mt-2 fw-semibold text-muted">
                                            Usada: {{ $usedMemory }} KB / Total: {{ $totalMemory }} KB
                                        </p>
                                        @if($totalMemory === 0)
                                            <div class="mt-2 alert alert-warning py-1 mb-0 text-center">
                                                <i class="bi bi-exclamation-circle"></i> Datos de memoria no disponibles
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errorMessage)
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error:</strong> {{ $errorMessage }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        {{-- Usar interfacesWithStats que ya tiene las estadísticas combinadas --}}
                        @if($interfacesWithStats->isNotEmpty())
                        <h5 class="mt-4 mb-3"><i class="bi bi-ethernet me-2"></i>Interfaces y Tráfico</h5>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th>Interfaz</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Rx (Mbps)</th>
                                        <th>Tx (Mbps)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Iterar sobre la propiedad computada --}}
                                    @foreach($interfacesWithStats as $interface)
                                    <tr wire:key="interface-{{ $interface['.id'] ?? $loop->index }}"> {{-- Usar .id o index como key --}}
                                        <td>
                                            <i class="bi bi-ethernet me-2"></i>{{ $interface['name'] ?? 'N/A' }}
                                        </td>
                                        <td>{{ $interface['type'] ?? 'N/A' }}</td>
                                        <td>
                                            @if(isset($interface['running']) && strtolower($interface['running']) === 'true')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Arriba</span>
                                            @else
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Caído</span>
                                            @endif
                                        </td>
                                        <td class="text-primary fw-bold">
                                            <i class="bi bi-arrow-down me-1"></i>{{ $interface['rx'] ?? '0.00' }}
                                        </td>
                                        <td class="text-success fw-bold">
                                            <i class="bi bi-arrow-up me-1"></i>{{ $interface['tx'] ?? '0.00' }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @else
                        {{-- Mensaje si no hay interfaces después de cargar --}}
                         @if(!$isLoading)
                            <div class="text-center py-5">
                                <i class="bi bi-hdd-network display-4 text-muted"></i>
                                <h4 class="mt-3 text-muted">No hay datos de interfaces</h4>
                                <p class="text-muted">Asegúrate de que el nodo esté accesible y tenga interfaces configuradas.</p>
                            </div>
                         @endif
                        @endif

                        @endif {{-- Fin del if(!$isLoading && ($interfacesWithStats->isNotEmpty() ...)) --}}

                    </div>
                    <div class="card-footer bg-light">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Última actualización: {{ now()->format('d/m/Y H:i:s') }}
                            {{-- wire:poll solo para actualizar estadísticas de interfaz --}}
                            {{-- Intervalo de actualización cambiado a 10 segundos --}}
                            <span wire:poll.10s="pollInterfaceStats" class="float-end">
                                <i class="bi bi-arrow-clockwise me-1"></i>Actualizando tráfico cada 10s
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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</div>
