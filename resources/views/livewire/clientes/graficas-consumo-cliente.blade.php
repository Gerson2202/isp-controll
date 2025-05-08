<div>
    <div>
        @if(!$mostrarGraficas)
            <div class="text-center py-4">
                <button wire:click="cargarGraficas" class="btn btn-primary">
                    <i class="fas fa-chart-line me-2"></i> Mostrar Gráficas
                </button>
            </div>
        @else
            @if($isLoading)
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            @elseif($error)
                <div class="alert alert-danger">
                    {{ $error }}
                    <button wire:click="obtenerDatosConsumo" class="btn btn-sm btn-warning ms-2">
                        Reintentar
                    </button>
                </div>
            @else
                <div class="card">
                    <div class="card-body">
                        <div class="chart-container" style="height: 350px; width: 100%; min-height: 350px;">
                            <canvas id="graficaConsumo" wire:ignore></canvas>
                        </div>
                    </div>
                    <div class="card-footer text-center bg-light">
                        <small class="text-muted">
                            Actualizado: {{ end($datosConsumo)['timestamp'] }} | 
                            Subida: {{ end($datosConsumo)['subida'] }} Mbps | 
                            Bajada: {{ end($datosConsumo)['bajada'] }} Mbps
                        </small>
                        <button wire:click="resetearGraficas" class="btn btn-sm btn-outline-secondary ms-3">
                            <i class="fas fa-eye-slash me-1"></i> Ocultar
                        </button>
                    </div>
                </div>
            @endif
        @endif
    
        @push('scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    let consumoChart = null;

    // Función para inicializar o actualizar la gráfica
    function handleChart() {
        const ctx = document.getElementById('graficaConsumo');
        if (!ctx) return;

        const datos = {
            labels: @json(array_column($datosConsumo, 'timestamp')),
            datasets: [
                {
                    label: 'Bajada (Mbps)',
                    data: @json(array_column($datosConsumo, 'bajada')),
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 2,
                    tension: 0.1
                },
                {
                    label: 'Subida (Mbps)',
                    data: @json(array_column($datosConsumo, 'subida')),
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 2,
                    tension: 0.1
                }
            ]
        };

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 0 },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Mbps' } },
                x: { title: { display: true, text: 'Tiempo' } }
            }
        };

        if (!consumoChart) {
            consumoChart = new Chart(ctx, {
                type: 'line',
                data: datos,
                options: options
            });
        } else {
            consumoChart.data = datos;
            consumoChart.update();
        }
    }

    document.addEventListener('livewire:initialized', () => {
        // Manejar la gráfica inicialmente
        handleChart();

        // Actualizar cuando Livewire emite el evento
        Livewire.on('actualizarGraficas', () => {
            handleChart();
        });

        // Programar actualización automática
        Livewire.on('programarActualizacion', ({ intervalo }) => {
            setTimeout(() => {
                Livewire.dispatch('actualizarGraficas');
            }, intervalo);
        });
    });

    // Redibujar al cambiar tamaño de ventana
    window.addEventListener('resize', () => {
        if (consumoChart) {
            consumoChart.resize();
        }
    });
</script>
@endpush
    </div>
</div