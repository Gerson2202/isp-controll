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
            <div class="col-xl-4 col-md-6 mb-4">
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
            
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-start border-success border-5 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-success mb-1">
                                    Total Pagado
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    ${{ number_format($estadisticas['totalPagado'], 2) }}
                                </div>
                            </div>
                            <i class="fas fa-money-bill-wave fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-start border-warning border-5 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-warning mb-1">
                                    Facturas Pendientes
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $estadisticas['facturasPendientes'] }}
                                </div>
                            </div>
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
{{--             
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-start border-danger border-5 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-danger mb-1">
                                    Facturas Vencidas
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $estadisticas['facturasVencidas'] }}
                                </div>
                            </div>
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
       
        
        <!-- Tabla de Pagos por Usuario -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pagos Registrados por Usuario</h5>
                <button 
                    wire:click="$toggle('mostrarPagosPorUsuario')" 
                    class="btn btn-sm btn-primary"
                >
                    {{ $mostrarPagosPorUsuario ? 'Ocultar' : 'Mostrar' }}
                </button>
            </div>
            
            @if($mostrarPagosPorUsuario)
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th class="text-end">Cantidad de Pagos</th>
                                <th class="text-end">Total Recaudado</th>
                                
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($estadisticas['pagosPorUsuario'] as $usuario => $data)
                            <tr>
                                <td>{{ $usuario }}</td>
                                <td class="text-end">{{ $data['count'] }}</td>
                                <td class="text-end">${{ number_format($data['total'], 2) }}</td>
                                
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No hay pagos registrados en el período seleccionado</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="text-end">{{ collect($estadisticas['pagosPorUsuario'])->sum('count') }}</th>
                                <th class="text-end">${{ number_format(collect($estadisticas['pagosPorUsuario'])->sum('total'), 2) }}</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Modal para detalle de usuario -->
    @if($usuarioSeleccionado)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalle de pagos: {{ $usuarioSeleccionado }}</h5>
                    <button wire:click="cerrarModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Fecha Pago</th>
                                    <th>Cliente</th>
                                    <th>Contrato</th>
                                    <th class="text-end">Monto</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($detallePagosUsuario as $pago)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                    <td>{{ $pago->factura->contrato->cliente->nombre ?? 'N/A' }}</td>
                                    <td>Contrato #{{ $pago->factura->contrato->id ?? 'N/A' }}</td>
                                    <td class="text-end">${{ number_format($pago->monto, 2) }}</td>
                                    <td>{{ $pago->metodo_pago }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="cerrarModal" class="btn btn-secondary">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    
</div>