<div>
    <div class="container-fluid">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-file-invoice me-2"></i>Generar Facturas Mensuales</h5>
            </div>
            <div class="card-body">
                {{-- Poder selecionar mes y año libremente --}}
                {{-- <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Mes</label>
                        <select wire:model="mes" class="form-select">
                            @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}">
                                {{ \Carbon\Carbon::createFromFormat('!m', $month)->locale('es')->monthName }}
                            </option>                            
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Año</label>
                        <input type="number" wire:model="anio" class="form-control" 
                               min="{{ now()->year - 1 }}" max="{{ now()->year + 1 }}">
                    </div>
                </div> --}}
                <!-- Selectores de Fecha -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Mes</label>
                        <select class="form-select" disabled>
                            @php
                                $currentMonth = now()->month;
                                $monthName = \Carbon\Carbon::createFromFormat('!m', $currentMonth)->locale('es')->monthName;
                            @endphp
                            <option value="{{ $currentMonth }}">{{ ucfirst($monthName) }}</option>
                        </select>
                        <input type="hidden" wire:model="mes" value="{{ $currentMonth }}">
                    </div>
                
                    <div class="col-md-6">
                        <label class="form-label">Año</label>
                        <select class="form-select" disabled>
                            @php
                                $currentYear = now()->year;
                            @endphp
                            <option value="{{ $currentYear }}">{{ $currentYear }}</option>
                        </select>
                        <input type="hidden" wire:model="anio" value="{{ $currentYear }}">
                    </div>
                </div>
                
                <!-- Botón de Acción -->
                <button wire:click="generarFacturas" wire:loading.attr="disabled" class="btn btn-success">
                    <i class="fas fa-play-circle me-2"></i> Generar Facturas
                    <span wire:loading class="spinner-border spinner-border-sm ms-2"></span>
                </button>
                
    
                <!-- Resultados -->
                @if(!empty($resultados))
                    <div class="mt-4">
                        <div class="alert alert-primary d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ count($resultados) }}</strong> resultados de facturación procesados.
                            </div>
                            <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#detalleResultados">
                                Ver Detalles
                            </button>
                        </div>

                        <div class="collapse show" id="detalleResultados">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table id="tablaResultados" class="table table-hover align-middle table-bordered text-sm">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Cliente</th>
                                            <th>Estado</th>
                                            <th>Mensaje</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resultados as $resultado)
                                            <tr>
                                                <td>{{ $resultado['cliente'] }}</td>
                                                <td>
                                                    @php
                                                        $estado = $resultado['estado'];
                                                        $badgeClass = match($estado) {
                                                            'éxito' => 'success',
                                                            'omitido' => 'warning',
                                                            'error' => 'danger',
                                                            default => 'secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeClass }}">{{ ucfirst($estado) }}</span>
                                                </td>
                                                <td>{{ $resultado['mensaje'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>