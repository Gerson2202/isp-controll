<div>
    <div class="container-fluid py-3">

        {{-- Encabezado --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

                            <div class="d-flex align-items-center">
                                <a href="{{ route('MonitoreoIndex') }}" class="btn btn-light border me-3">
                                    <i class="bi bi-arrow-left"></i>
                                </a>

                                <div>
                                    <h2 class="mb-1 fw-bold">
                                        <i class="bi bi-pc-display-horizontal text-primary me-2"></i>
                                        {{ $nodo->nombre }}
                                    </h2>

                                    <small class="text-muted">
                                        Monitoreo en tiempo real del nodo
                                    </small>
                                </div>
                            </div>

                            <button wire:click="loadInitialData" class="btn btn-primary px-4"
                                wire:loading.attr="disabled">

                                <span wire:loading wire:target="loadInitialData"
                                    class="spinner-border spinner-border-sm me-2" role="status">
                                </span>

                                <i wire:loading.remove wire:target="loadInitialData" class="bi bi-arrow-repeat me-2">
                                </i>

                                Cargar información
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div wire:loading.delay class="text-center py-5">
            <div class="spinner-border text-primary" role="status"></div>

            <p class="text-muted mt-3 mb-0">
                Cargando información del nodo...
            </p>
        </div>

        @if ($dataLoaded && !$isLoading)

            {{-- Error --}}
            @if ($errorMessage)
                <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <strong>Error:</strong> {{ $errorMessage }}

                    <button type="button" class="btn-close" data-bs-dismiss="alert">
                    </button>
                </div>
            @endif

            {{-- Cards --}}
            <div class="row g-3 mb-4">

                {{-- Uptime --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm h-100 rounded-4 border-0 border-top border-4 border-primary">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <small class="text-muted d-block mb-1">
                                        Tiempo de Encendido
                                    </small>

                                    <h5 class="fw-bold mb-0">
                                        {{ $systemResources['formatted_uptime'] ?? 'N/A' }}
                                    </h5>
                                </div>

                                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- CPU --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm h-100 rounded-4 border-0 border-top border-4 border-primary">
                        <div class="card-body">

                            @php
                                $cpuLoad = (int) ($systemResources['cpu-load'] ?? 0);
                                $progressBarClass =
                                    $cpuLoad > 75 ? 'bg-danger' : ($cpuLoad >= 40 ? 'bg-warning' : 'bg-success');
                            @endphp

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div class="w-100">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">
                                            Uso de CPU
                                        </small>

                                        <strong>
                                            {{ $cpuLoad }}%
                                        </strong>
                                    </div>

                                    <div class="progress rounded-pill" style="height: 10px;">

                                        <div class="progress-bar {{ $progressBarClass }}"
                                            style="width: {{ $cpuLoad }}%">
                                        </div>

                                    </div>
                                </div>

                                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 ms-3">
                                    <i class="bi bi-cpu"></i>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                {{-- Memoria --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm h-100 rounded-4 border-0 border-top border-4 border-primary">
                        <div class="card-body">

                            @php
                                $totalMemory = (int) ($systemResources['total-memory'] ?? 0);
                                $freeMemory = (int) ($systemResources['free-memory'] ?? 0);
                                $usedMemory = $totalMemory > 0 ? $totalMemory - $freeMemory : 0;
                                $memoryUsagePercent = $totalMemory > 0 ? round(($usedMemory / $totalMemory) * 100) : 0;
                                $memoryBarClass =
                                    $memoryUsagePercent > 85
                                        ? 'bg-danger'
                                        : ($memoryUsagePercent >= 60
                                            ? 'bg-warning'
                                            : 'bg-success');
                            @endphp

                            <div class="d-flex justify-content-between align-items-start mb-3">

                                <div class="w-100">

                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted">
                                            Uso de Memoria
                                        </small>

                                        <strong>
                                            {{ $memoryUsagePercent }}%
                                        </strong>
                                    </div>

                                    <div class="progress rounded-pill" style="height: 10px;">

                                        <div class="progress-bar {{ $memoryBarClass }}"
                                            style="width: {{ $memoryUsagePercent }}%">
                                        </div>

                                    </div>

                                    <small class="text-muted d-block mt-2">
                                        {{ number_format($usedMemory / 1024, 2) }} MB /
                                        {{ number_format($totalMemory / 1024, 2) }} MB
                                    </small>

                                </div>

                                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 ms-3">
                                    <i class="bi bi-memory"></i>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                {{-- Temperatura --}}
                <div class="col-md-6 col-xl-3">
                    <div class="card shadow-sm h-100 rounded-4 border-0 border-top border-4 border-primary">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-start">

                                <div>
                                    <small class="text-muted d-block mb-1">
                                        Temperatura
                                    </small>

                                    <h5 class="fw-bold mb-1">
                                        {{ $systemHealth['temperature'] ?? 'N/A' }}
                                    </h5>

                                    @if (isset($systemHealth['voltage']))
                                        <small class="text-muted">
                                            <i class="bi bi-lightning-charge-fill me-1"></i>
                                            {{ $systemHealth['voltage'] }}
                                        </small>
                                    @endif
                                </div>

                                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2">
                                    <i class="bi bi-thermometer-half"></i>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

            </div>

            {{-- Interfaces --}}
            @if ($interfacesWithStats->isNotEmpty())

                <div class="card border-0 shadow-sm overflow-hidden">

                    <div class="card-header bg-dark border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">

                            <h5 class="mb-0 text-white">
                                <i class="bi bi-ethernet me-2"></i>
                                Interfaces y Tráfico
                            </h5>

                            <span class="badge bg-success">
                                Tiempo real
                            </span>

                        </div>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">

                            <table class="table table-hover align-middle mb-0">

                                <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">

                                    <tr>
                                        <th class="ps-4">Interfaz</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Rx</th>
                                        <th>Tx</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($interfacesWithStats as $interface)
                                        <tr wire:key="interface-{{ $interface['.id'] ?? $loop->index }}">

                                            <td class="ps-4">

                                                <div class="d-flex align-items-center">

                                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                        style="width: 38px; height: 38px;">

                                                        <i class="bi bi-ethernet"></i>

                                                    </div>

                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $interface['name'] ?? 'N/A' }}
                                                        </div>
                                                    </div>

                                                </div>

                                            </td>

                                            <td>
                                                <span class="badge bg-light text-dark border">
                                                    {{ $interface['type'] ?? 'N/A' }}
                                                </span>
                                            </td>

                                            <td>

                                                @if (isset($interface['running']) && strtolower($interface['running']) === 'true')
                                                    <span
                                                        class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                                                        <i class="bi bi-check-circle-fill me-1"></i>
                                                        Arriba
                                                    </span>
                                                @else
                                                    <span
                                                        class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
                                                        <i class="bi bi-x-circle-fill me-1"></i>
                                                        Caído
                                                    </span>
                                                @endif

                                            </td>

                                            <td>
                                                <span class="fw-bold text-primary">
                                                    <i class="bi bi-arrow-down me-1"></i>
                                                    {{ $interface['rx'] ?? '0.00' }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="fw-bold text-success">
                                                    <i class="bi bi-arrow-up me-1"></i>
                                                    {{ $interface['tx'] ?? '0.00' }}
                                                </span>
                                            </td>

                                        </tr>
                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <div class="card-footer bg-light border-0">

                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                            <small class="text-muted">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Actualizando tráfico cada 20 segundos
                            </small>

                            <small wire:poll.20s="pollInterfaceStats" class="text-success fw-semibold">

                                <i class="bi bi-broadcast-pin me-1"></i>
                                En tiempo real

                            </small>

                        </div>

                    </div>

                </div>
            @else
                @if (!$isLoading)
                    <div class="alert alert-info border-0 shadow-sm">
                        <i class="bi bi-info-circle-fill me-2"></i>

                        No hay interfaces disponibles o el nodo no está respondiendo.
                    </div>
                @endif

            @endif

        @endif

    </div>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">
</div>
