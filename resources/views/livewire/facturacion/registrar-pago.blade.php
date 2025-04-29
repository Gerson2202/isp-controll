<div>
    <div class="container-fuid">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-money-bill-wave me-2"></i>Registrar Pago</h5>
            </div>
            <div class="card-body">
                <!-- Buscador de facturas -->
                <div class="mb-4">
                    <label class="form-label">Buscar Factura</label>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="N° Factura o Cliente">
                </div>
    
                <!-- Facturas pendientes -->
                @if($facturas->count())
                    <table class="table">
                        <thead>
                            <tr>
                                <th>N° Factura</th>
                                <th>Cliente</th>
                                <th>Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($facturas as $factura)
                            <tr>
                                <td>{{ $factura->numero_factura }}</td>
                                <td>{{ $factura->contrato->cliente->nombre }}</td>
                                <td>${{ number_format($factura->monto_total, 2) }}</td>
                                <td>
                                    <button wire:click="seleccionarFactura({{ $factura->id }})" 
                                            class="btn btn-sm btn-success">
                                        Registrar Pago
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $facturas->links() }}
                    </div>
                @else
                    <div class="alert alert-warning">
                        No se encontraron facturas pendientes
                    </div>
                @endif
    
                <!-- Modal para registrar pago -->
                @if($facturaSeleccionada)
                    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Registrar Pago</h5>
                                    <button type="button" class="btn-close" aria-label="Close" wire:click="cerrarModal"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="registrarPago">
                                        <div class="mb-3">
                                            <label>Monto</label>
                                            <input type="number" wire:model="monto" 
                                                class="form-control" 
                                                max="{{ $facturaSeleccionada->saldo_pendiente }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Método de Pago</label>
                                            <select wire:model="metodo_pago" class="form-select">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="tarjeta">Tarjeta</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Fecha de Pago</label>
                                            <input type="date" wire:model="fecha_pago" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            Confirmar Pago
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
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