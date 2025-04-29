<div>
    <div class="container-fluid py-4">
        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Rango de fechas</label>
                        <select 
                            wire:model.live="rangoFechas" 
                            class="form-select"
                        >
                            <option value="mes_actual">Mes Actual</option>
                            <option value="mes_pasado">Mes Pasado</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    
                    @if($rangoFechas === 'personalizado')
                    <div class="col-md-4">
                        <label class="form-label">Fecha Inicio</label>
                        <input 
                            type="date" 
                            wire:model.live="fechaInicio" 
                            class="form-control"
                        >
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Fin</label>
                        <input 
                            type="date" 
                            wire:model.live="fechaFin" 
                            class="form-control"
                        >
                    </div>
                    @endif
                </div>
            </div>
        </div>
    
        <!-- Tarjetas de Métricas -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-primary border-5 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-primary mb-1">
                                    Total Facturado
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($estadisticas['totalFacturado'], 2) }}
                                </div>
                            </div>
                            <i class="fas fa-file-invoice fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Repite para otras métricas (Total Pagado, Pendientes, etc.) -->
        </div>
    
        <!-- Gráficos -->
        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            Ingresos por Plan
                        </h6>
                    </div>
                    <div class="card-body">
                        <canvas id="ingresosPorPlanChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('livewire:load', function () {
            const ingresosData = @json($estadisticas['ingresosPorPlan']);
            
            // Verifica si hay datos
            if (Object.keys(ingresosData).length > 0) {
                new Chart(
                    document.getElementById('ingresosPorPlanChart'),
                    {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(ingresosData),
                            datasets: [{
                                data: Object.values(ingresosData),
                                backgroundColor: [
                                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'
                                ]
                            }]
                        }
                    }
                );
            } else {
                console.warn('No hay datos de ingresos por plan');
            }
        });
    </script>
    @endpush
</div>
