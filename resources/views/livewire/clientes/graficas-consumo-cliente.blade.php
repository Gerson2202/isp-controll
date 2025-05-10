<div>
    <div>
    <!-- Título -->
   <div class="card shadow-sm border-0">
    <!-- Card header -->
    <div class="card-header bg-white border-0 py-3">
        <h5 class="card-title mb-0">
    <span class="d-flex align-items-center">
        <i class="bi bi-graph-up me-2 text-primary"></i>
        <a href="{{ route('clientes.show', $cliente->id) }}" class="text-decoration-none">
            <span class="text-primary fw-semibold hover-underline">
                {{ $cliente->nombre }}
            </span>
        </a>
        <span class="badge ms-2" style="background-color: #6f42c1; color: white; font-weight: 500">
            {{ $cliente->contrato->plan->nombre }}
        </span>
    </span>
</h5>
    </div>
    
    <!-- Card body (gráfica) -->
    <div class="card-body p-0">
        <div class="p-4" style="height: 400px;">
            <canvas id="consumoChart" wire:ignore></canvas>
        </div>
    </div>
    
    <!-- Card footer (opcional) -->
   <div class="card-footer bg-light border-0 py-2">
    <div class="d-flex align-items-center justify-content-between">
        <span class="text-muted small">Datos en vivo</span>
        
        <div class="d-flex align-items-center">
            <span class="vr mx-2 opacity-25"></span>
            <span class="text-dark fw-medium">
                <i class="bi bi-ethernet me-1"></i>
                <strong>{{ $cliente->ip ?: 'Sin ip' }}</strong>
            </span>
            <span class="vr mx-2 opacity-25"></span>
        </div>
        
        <span class="text-muted small">@if($isLoading)<i class="bi bi-lightning-charge"></i>@endif</span>
    </div>
</div>
</div>

<style>
    .spin {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            let consumoChart = null;
            let intervalo = null;
            const $wire = @this;

            // Inicializar gráfica
            function initChart() {
                const ctx = document.getElementById('consumoChart');
                if (!ctx) return;

                consumoChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: [], // Inicialmente vacío
                        datasets: [
                            {
                                label: 'Subida (Mbps)',
                                data: [],
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                borderWidth: 2,
                                fill: true,
                                pointRadius: 1  // <-- Añade esta línea para ocultar puntos
                                
                            },
                            {
                                label: 'Bajada (Mbps)',
                                data: [],
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                borderWidth: 2,
                                fill: true,
                                pointRadius: 1  // <-- Añade esta línea para ocultar puntos
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 0 // Sin animación para actualizaciones rápidas
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Mbps'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }

            // Actualizar gráfica con datos acumulados
            function updateChart(labels, subidaData, bajadaData) {
                if (!consumoChart) return;

                // Actualizar etiquetas y datos
                consumoChart.data.labels = labels; // Etiquetas actualizadas (acumuladas)
                consumoChart.data.datasets[0].data = subidaData; // Datos de subida acumulados
                consumoChart.data.datasets[1].data = bajadaData; // Datos de bajada acumulados
                consumoChart.update(); // Actualiza la gráfica
            }

            // Escuchar evento para iniciar monitoreo
            $wire.on('iniciar-monitoreo', () => {
                // Limpiar intervalo existente
                if (intervalo) clearInterval(intervalo);

                // Iniciar nuevo intervalo para solicitar datos y actualizar la gráfica
                intervalo = setInterval(() => {
                    $wire.call('obtenerDatosConsumo') // Llama al servidor para obtener nuevos datos
                        .then(() => {
                            // Actualizar gráfica con los datos acumulados
                            updateChart($wire.get('labels'), $wire.get('subidaData'), $wire.get('bajadaData'));
                        });
                }, 1000); // Cada 1 segundo
            });

            // Inicializar gráfica
            initChart();

            // Iniciar monitoreo automáticamente
            $wire.dispatch('iniciar-monitoreo');

            // Limpiar al salir
            window.addEventListener('beforeunload', () => {
                if (intervalo) clearInterval(intervalo);
            });
        });
    </script>
@endpush
</div>
</div>

