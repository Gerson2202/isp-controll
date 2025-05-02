<div>
    <div class="container-fluid p-4">
        <div class="card">
            <!-- Encabezado y Filtros (mantener igual que antes) -->
            
            <!-- Tabla con scroll -->
            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light sticky-top bg-white">
                        <tr>
                            <th class="text-nowrap">Cliente</th>
                            <th class="text-nowrap d-none d-md-table-cell">N° Factura</th>
                            <th class="text-nowrap d-none d-md-table-cell">Vencimiento</th>
                            <th class="text-nowrap d-none d-md-table-cell">Monto</th>
                            <th class="text-nowrap d-none d-md-table-cell">Saldo</th>
                            <th class="text-nowrap">Estado Factura</th>
                            <th class="text-nowrap">Estado Mikrotik</th>
                            <th class="text-nowrap">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <!-- Columna Cliente -->
                                <td class="text-nowrap">
                                    <div class="fw-bold">{{ $factura->contrato->cliente->nombre }}</div>
                                    <div class="d-md-none small text-muted">
                                        <div>Factura: {{ $factura->numero_factura }}</div>
                                        <div>Vence: {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}</div>
                                        <div>Monto: ${{ number_format($factura->monto_total, 2) }}</div>
                                        <div>Saldo: ${{ number_format($factura->saldo_pendiente, 2) }}</div>
                                    </div>
                                </td>
                                
                                <!-- Columnas para desktop - Ocultas en móvil -->
                                <td class="text-nowrap d-none d-md-table-cell">{{ $factura->numero_factura }}</td>
                                <td class="text-nowrap d-none d-md-table-cell">
                                    {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}
                                </td>
                                <td class="text-nowrap d-none d-md-table-cell">${{ number_format($factura->monto_total, 2) }}</td>
                                <td class="text-nowrap d-none d-md-table-cell">${{ number_format($factura->saldo_pendiente, 2) }}</td>
                                
                                <!-- Estado Factura -->
                                <td class="text-nowrap">
                                    <span class="badge {{ $factura->estado == 'pagada' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($factura->estado) }}
                                    </span>
                                </td>
                                
                                <!-- Estado Mikrotik -->
                                <td class="text-nowrap">
                                    <span class="badge {{ $factura->contrato->cliente->estado == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($factura->contrato->cliente->estado) }}
                                    </span>
                                </td>
                                
                                <!-- Acción - Siempre visible en móvil y desktop -->
                                <td class="text-nowrap">
                                    <button 
                                        wire:click="cambiarEstado({{ $factura->contrato->cliente->id }})"
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm {{ $factura->contrato->cliente->estado == 'activo' ? 'btn-danger' : 'btn-primary' }}"
                                        title="{{ $factura->contrato->cliente->estado == 'activo' ? 'Cortar servicio' : 'Activar servicio' }}"
                                    >
                                        <span wire:loading.remove>
                                            @if($factura->contrato->cliente->estado == 'activo')
                                                <i class="fas fa-power-off"></i>
                                                <span class="d-none d-sm-inline ms-1">Cortar</span>
                                            @else
                                                <i class="fas fa-plug"></i>
                                                <span class="d-none d-sm-inline ms-1">Activar</span>
                                            @endif
                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin"></i>
                                            <span class="d-none d-sm-inline ms-1">Procesando...</span>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No se encontraron facturas para los filtros seleccionados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Contador de resultados -->
            <div class="card-footer small text-muted">
                Mostrando {{ $facturas->count() }} registros
            </div>
        </div>
    </div>
</div>