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
            {{-- <div class="col-xl-4 col-md-6 mb-4">
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
            </div>
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-start border-info border-5 shadow h-100 py-2">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="text-xs font-weight-bold text-info mb-1">
                                    Clientes Activos
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $estadisticas['clientesActivos'] }}
                                </div>
                            </div>
                            <i class="fas fa-users fa-2x text-info"></i>
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
                            @forelse($estadisticas['pagosPorUsuario'] as $userId => $data)
                            <tr>
                                <td>
                                    <a href="#"
                                    wire:click.prevent="verDetalleUsuario('{{ $userId }}')"
                                    class="fw-medium text-info"
                                    style="text-decoration: none; cursor: pointer;"
                                    onmouseover="this.style.textDecoration='underline';"
                                    onmouseout="this.style.textDecoration='none';"
                                    >
                                        {{ $data['nombre'] }}
                                    </a>
                                </td>
                                <td class="text-end">{{ $data['count'] }}</td>
                                <td class="text-end">${{ number_format($data['total'], 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay pagos registrados en el período seleccionado</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="text-end">{{ collect($estadisticas['pagosPorUsuario'])->sum('count') }}</th>
                                <th class="text-end">${{ number_format(collect($estadisticas['pagosPorUsuario'])->sum('total'), 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Modal para detalle de usuario -->
    <!-- Modal para detalle de usuario -->
@if($usuarioSeleccionado)
    @php
        $user = \App\Models\User::find($usuarioSeleccionado);
        $nombreUsuario = $user ? ($user->name ?? $user->email ?? 'Usuario #'.$usuarioSeleccionado) : 'Desconocido';
    @endphp
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Detalle de pagos: {{ $nombreUsuario }}</h5>
                    <button wire:click="cerrarModal" class="btn-close btn-close-white"></button>
                </div>
                <div class="modal-body">
                    <!-- Resumen de pagos -->
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title fw-bold text-primary mb-3">Resumen de Pagos</h6>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="border p-3 rounded bg-light">
                                        <h6 class="text-center text-success fw-bold">Efectivo</h6>
                                        <p class="text-center mb-1 fw-semibold">Cantidad: {{ $resumenPagos['efectivo']['cantidad'] }}</p>
                                        <p class="text-center mb-0 fw-bold">${{ number_format($resumenPagos['efectivo']['total'], 2) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border p-3 rounded bg-light">
                                        <h6 class="text-center text-info fw-bold">Transferencia</h6>
                                        <p class="text-center mb-1 fw-semibold">Cantidad: {{ $resumenPagos['transferencia']['cantidad'] }}</p>
                                        <p class="text-center mb-0 fw-bold">${{ number_format($resumenPagos['transferencia']['total'], 2) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="border p-3 rounded bg-light">
                                        <h6 class="text-center text-warning fw-bold">Tarjeta</h6>
                                        <p class="text-center mb-1 fw-semibold">Cantidad: {{ $resumenPagos['tarjeta']['cantidad'] }}</p>
                                        <p class="text-center mb-0 fw-bold">${{ number_format($resumenPagos['tarjeta']['total'], 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 p-3 bg-primary text-white rounded">
                                <h5 class="mb-0 text-end fw-bold">Total General: ${{ number_format($resumenPagos['total_general'], 2) }}</h5>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tabla de detalle -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Fecha Pago</th>
                                    <th>Cliente</th>
                                    <th class="text-end">Monto</th>
                                    <th>Método</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($detallePagosUsuario as $pago)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($pago->factura->contrato->cliente)
                                            <a href="{{ route('clientes.show', $pago->factura->contrato->cliente->id) }}" 
                                            target="_blank"
                                            class="text-decoration-none text-dark fw-medium"
                                            onmouseover="this.style.textDecoration='underline';" 
                                            onmouseout="this.style.textDecoration='none';"
                                            >
                                                {{ $pago->factura->contrato->cliente->nombre }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($pago->monto, 2) }}</td>
                                    <td>
                                        @php
                                            $badgeClass = [
                                                'efectivo' => 'bg-success',
                                                'transferencia' => 'bg-info',
                                                'tarjeta' => 'bg-warning'
                                            ][$pago->metodo_pago] ?? 'bg-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($pago->metodo_pago) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">No se encontraron pagos para este usuario en el período seleccionado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="cerrarModal" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cerrar
                    </button>
                    
                </div>
            </div>
        </div>
    </div>
@endif

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sin gráfica de ingresos por plan, solo mantener si tienes otros gráficos
    </script>
    @endpush
</div>