<div>
    <div>
        <!-- Barra de búsqueda -->
        <div class="mb-3">
            <input 
                type="text" 
                class="form-control" 
                placeholder="Buscar por número de factura..." 
                wire:model.lazy="search"
            >
        </div>
    
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mes</th> <!-- Nueva columna -->
                        <th wire:click="sortBy('numero_factura')" style="cursor: pointer;">
                            N° Factura
                            @if($sortField === 'numero_factura')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th wire:click="sortBy('fecha_emision')" style="cursor: pointer;">
                            Fecha Emisión
                            @if($sortField === 'fecha_emision')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th wire:click="sortBy('fecha_vencimiento')" style="cursor: pointer;">
                            Fecha Vencimiento
                            @if($sortField === 'fecha_vencimiento')
                                {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                            @endif
                        </th>
                        <th class="text-end">Monto Total</th>
                        <th class="text-end">Saldo Pendiente</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($facturas as $factura)
                    <tr 
                        wire:click="mostrarPagos({{ $factura->id }})"
                        style="cursor: pointer;"
                        class="{{ $factura->estado === 'pendiente' ? 'table-warning' : 'table-success' }}"
                    >
                        <td>{{ $factura->fecha_emision->translatedFormat('M Y') }}
                        <td>{{ $factura->numero_factura }}</td>
                        <td>{{ $factura->fecha_emision->format('d/m/Y') }}</td>
                        <td>{{ $factura->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="text-end">${{ number_format($factura->monto_total, 2) }}</td>
                        <td class="text-end">${{ number_format($factura->saldo_pendiente, 2) }}</td>
                        <td>
                            <span class="badge {{ $factura->estado === 'pendiente' ? 'bg-warning' : 'bg-success' }}">
                                {{ ucfirst($factura->estado) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
        <!-- Modal de Pagos -->
        <div class="modal fade" id="modalPagos" tabindex="-1" aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            Pagos de Factura #{{ $facturaSeleccionada->numero_factura ?? '' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(count($pagos) > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Método</th>
                                        <th>Monto</th>
                                        <th>Referencia</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pagos as $pago)
                                    <tr>
                                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                        <td>{{ ucfirst($pago->metodo_pago) }}</td>
                                        <td>${{ number_format($pago->monto, 2) }}</td>
                                        <td>{{ $pago->referencia }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info mb-0">
                            No se registran pagos para esta factura
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Paginación -->
        <div class="mt-3">
            {{ $facturas->links() }}
        </div>
    </div>
    <!-- Script para controlar el modal -->
    <script>
        // Cambia el listener del evento
        document.addEventListener('livewire:init', function() {
            Livewire.on('abrirModalPagos', () => {
                const modal = new bootstrap.Modal('#modalPagos');
                modal.show();
            });
        });
    </script>
</div>
