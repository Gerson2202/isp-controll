<div>
    <div class="container-fluid">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-file-invoice me-2"></i>Generar Facturas Mensuales</h5>
            </div>
            <div class="card-body">
                {{-- Poder selecionar mes y año libremente --}}
                  <div class="row mb-4">
                    <div class="col-md-6"> 
                        <label class="form-label">Mes</label>
                        <select wire:model="mes" class="form-select">
                            @php
                                $hoy = \Carbon\Carbon::now();
                                $mesActual = $hoy->month;
                                $diaActual = $hoy->day;
                                $mesInicio = $mesActual;
                                $mesesMostrar = 4;

                                for ($i = 0; $i < $mesesMostrar; $i++) {
                                    $mes = ($mesInicio + $i - 1) % 12 + 1;
                                    $anioDelMes = $hoy->copy()->addMonths($i)->year;
                                    $nombreMes = \Carbon\Carbon::createFromDate(null, $mes)->locale('es')->monthName;
                                    $deshabilitado = ($i === 0 && $diaActual > 25);
                            @endphp

                                <option value="{{ $mes }}"
                                    @if($deshabilitado) disabled @endif>
                                    {{ ucfirst($nombreMes) }} {{ $anioDelMes }}
                                    @if($deshabilitado) — no disponible (supera día 25) @endif
                                </option>

                            @php
                                }
                            @endphp
                        </select>
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
                    {{-- <div class="col-md-6">
                        <label class="form-label">Año</label>
                        <input type="number" wire:model="anio" class="form-control" 
                               min="{{ now()->year - 1 }}" max="{{ now()->year + 1 }}">
                    </div> --}}
                </div>    
                <!-- Selectores de Fecha -->
                  {{-- <div class="row mb-4">
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
                </div>    --}}
                
                <!-- Botón de Acción -->
                <button wire:click="generarFacturas" wire:loading.attr="disabled" class="btn btn-success">
                    <i class="fas fa-play-circle me-2"></i> Generar Facturas
                    <span wire:loading class="spinner-border spinner-border-sm ms-2"></span>
                </button>
                
                <button 
                    wire:loading.attr="disabled"
                    class="btn btn-danger"
                    onclick="confirmarEliminarFacturas('{{ $mes }}', '{{ $anio }}', '{{ $this->getId() }}')"
                    title="Eliminar todas las facturas del período {{ $mes }}/{{ $anio }}"
                >
                    <span wire:loading.remove>
                        <i class="fas fa-trash-alt me-1"></i> Eliminar facturas del periodo actual
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin me-1"></i> Eliminando...
                    </span>
                </button>
                
    
                <!-- Resultados -->
                @if(!empty($resultados))
                <div class="mt-4">
                    <!-- Mensaje general cuando no se generó ninguna factura -->
                    @if(!collect($resultados)->contains('estado', 'éxito'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            No se generaron facturas nuevas. Todos los contratos están al día o tienen pagos recientes.
                        </div>
                    @else
                        <div class="alert alert-primary d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ count($resultados) }}</strong> resultados de facturación procesados.
                            </div>
                            <button class="btn btn-sm btn-outline-light" data-bs-toggle="collapse" data-bs-target="#detalleResultados">
                                Ver Detalles
                            </button>
                        </div>
                    @endif

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
                                            <td>{{ $resultado['cliente'] ?? '--' }}</td>
                                            <td>
                                                @php
                                                    $estado = $resultado['estado'];
                                                    $badgeClass = match($estado) {
                                                        'éxito' => 'success',
                                                        'omitido' => 'warning',
                                                        'error' => 'danger',
                                                        'info' => 'info',
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
    <script>
        function confirmarEliminarFacturas(mes, anio, componentId) {
            Swal.fire({
                title: '¿Estás seguro?',
                html: `Se eliminarán <strong>TODAS</strong> las facturas del período <strong>${mes}/${anio}</strong>.<br><br>Esta acción no se puede deshacer.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Llama al método Livewire usando el ID del componente
                    Livewire.find(componentId).call('eliminarUltimoLote');
                }
            });
        }
    </script>
    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                toastr[data.type](data.message);
            });
        });
    </script>
    @endpush

</div>