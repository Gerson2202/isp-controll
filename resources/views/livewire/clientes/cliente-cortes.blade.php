<div>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body border-bottom">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar Cliente</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input 
                                type="text" 
                                id="search" 
                                wire:model.live.debounce.500ms="search" 
                                placeholder="Nombre del cliente..."
                                class="form-control"
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label for="filterEstado" class="form-label">Estado Factura</label>
                        <select 
                            id="filterEstado" 
                            wire:model.live="filterEstado" 
                            class="form-select"
                        >
                            <option value="">Todos</option>
                            <option value="pendiente">Pendientes</option>
                            <option value="pagada">Pagadas</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filterMikrotik" class="form-label">Estado Mikrotik</label>
                        <select 
                            id="filterMikrotik" 
                            wire:model.live="filterMikrotik" 
                            class="form-select"
                        >
                            <option value="">Todos</option>
                            <option value="activo">Activos</option>
                            <option value="cortado">Cortados</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-grid">
                        <button 
                            onclick="confirmarCorteSweet()"
                            wire:loading.attr="disabled"
                            class="btn btn-danger"
                        >
                            Cortar Clientes Pendientes
                        </button>
                    </div>
                </div>
            </div>


           <div class="card">
                <div class="card-body">
                    @if($procesandoCorteMasivo)
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between">
                            <span>
                                <i class="fas fa-sync-alt fa-spin me-2"></i>
                                Procesando corte masivo:
                            </span>
                            <strong>
                                {{ $clientesProcesados }} de {{ $totalClientes }} clientes procesados
                            </strong>
                        </div>
                        <!-- Polling para procesar el siguiente chunk -->
                        <div wire:poll.500ms="procesarChunk"></div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light sticky-top bg-white">
                        <tr>
                            <th class="text-nowrap">Cliente</th>
                            <th class="text-nowrap d-none d-md-table-cell">N° Factura</th>
                            <th class="text-nowrap d-none d-md-table-cell">Vencimiento</th>
                            <th class="text-nowrap d-none d-md-table-cell">Monto</th>
                            <th class="text-nowrap d-none d-md-table-cell">Saldo</th>
                            <th class="text-nowrap d-table-cell">Estado</th>
                            <th class="text-nowrap d-table-cell">Mikrotik</th>
                            <th class="text-nowrap d-table-cell">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facturas as $factura)
                            <tr>
                                <td class="text-nowrap">
                                    <div class="fw-bold">{{ $factura->contrato->cliente->nombre }}</div>
                                    <div class="d-md-none small text-muted">
                                        <div>Vence: {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}</div>
                                        <div>Monto: ${{ number_format($factura->monto_total, 2) }}</div>
                                    </div>
                                </td>
                                <td class="text-nowrap d-none d-md-table-cell">{{ $factura->numero_factura }}</td>
                                <td class="text-nowrap d-none d-md-table-cell">
                                    {{ \Carbon\Carbon::parse($factura->fecha_vencimiento)->format('d/m/Y') }}
                                </td>
                                <td class="text-nowrap d-none d-md-table-cell">${{ number_format($factura->monto_total, 2) }}</td>
                                <td class="text-nowrap d-none d-md-table-cell">${{ number_format($factura->saldo_pendiente, 2) }}</td>
                                <td class="text-nowrap d-table-cell">
                                    <span class="badge {{ $factura->estado == 'pagada' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($factura->estado) }}
                                    </span>
                                </td>
                                <td class="text-nowrap d-table-cell">
                                    <span class="badge {{ $factura->contrato->cliente->estado == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($factura->contrato->cliente->estado) }}
                                    </span>
                                </td>
                                <td class="text-nowrap d-table-cell">
                                    <button 
                                        wire:click="cambiarEstado({{ $factura->contrato->cliente->id }})"
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm {{ $factura->contrato->cliente->estado == 'activo' ? 'btn-danger' : 'btn-primary' }}"
                                        title="{{ $factura->contrato->cliente->estado == 'activo' ? 'Cortar servicio' : 'Activar servicio' }}"
                                    >
                                        <span wire:loading.remove>
                                            <i class="fas {{ $factura->contrato->cliente->estado == 'activo' ? 'fa-power-off' : 'fa-plug' }}"></i>
                                            <span class="d-none d-sm-inline ms-1">{{ $factura->contrato->cliente->estado == 'activo' ? 'Cortar' : 'Activar' }}</span>
                                        </span>
                                        <span wire:loading>
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    No se encontraron facturas
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer small text-muted">
                Mostrando {{ $facturas->count() }} registros
            </div>
        </div>
    </div>
    <script>
        function confirmarCorteSweet() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Deseas cortar todos los clientes pendientes?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cortar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.iniciarCorteMasivo();
                }
            });
        }
    </script>   
</div>