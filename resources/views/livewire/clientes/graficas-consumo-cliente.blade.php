<div>
    <div>
    <!-- Título -->
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Gráficas de Consumo - {{ $cliente->nombre }}</h1>

    <!-- Mensajes de estado -->
    @if($isLoading)
        <div class="p-4 mb-4 bg-blue-100 text-blue-800 rounded">Obteniendo datos...</div>
    @endif

    @if($error)
        <div class="p-4 mb-4 bg-red-100 text-red-800 rounded">{{ $error }}</div>
    @endif

    <!-- Contenedor de gráfica -->
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="h-96">
            <canvas id="consumoChart" wire:ignore></canvas>
        </div>
    </div>

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
                        labels: @json($labels),
                        datasets: [
                            {
                                label: 'Subida (Mbps)',
                                data: @json($subidaData),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.4,
                                borderWidth: 2,
                                fill: true
                            },
                            {
                                label: 'Bajada (Mbps)',
                                data: @json($bajadaData),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                borderWidth: 2,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 0
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

            // Actualizar gráfica
           function updateChart() {
                if (!consumoChart) return;

                consumoChart.data.labels = @json($labels); // Etiquetas actualizadas
                consumoChart.data.datasets[0].data = @json($subidaData); // Datos de subida actualizados
                consumoChart.data.datasets[1].data = @json($bajadaData); // Datos de bajada actualizados
                consumoChart.update(); // Actualiza la gráfica
            }

            // Escuchar evento para iniciar monitoreo
            $wire.on('iniciar-monitoreo', () => {
                // Limpiar intervalo existente
                if (intervalo) clearInterval(intervalo);

                // Iniciar nuevo intervalo
                intervalo = setInterval(updateChart, 1000);

                // Primera actualización inmediata
                updateChart();
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

