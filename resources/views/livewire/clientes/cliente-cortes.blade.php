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
                            wire:loading.attr="disabled"
                            class="btn btn-danger"
                            @if($procesando) disabled @endif
                            onclick="confirmarCorteMasivo()"
                        >
                            <span wire:loading.remove wire:target="iniciarCorteMasivo">
                                <i class="fas fa-power-off"></i> Cortar Pendientes
                            </span>
                            <span wire:loading wire:target="iniciarCorteMasivo">
                                <i class="fas fa-spinner fa-spin"></i> Procesando, espere un momento por favor...
                            </span>
                        </button>


                    </div>
                </div>
            </div>

            @if($procesando)
            <div class="card-body">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    Procesando corte masivo, por favor espere...
                </div>
            </div>
            @endif

            <div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light sticky-top bg-white">
                        <tr>
                            <th class="text-nowrap">Cliente</th>
                            <th class="text-nowrap d-none d-md-table-cell">N Factura</th>
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
                                    <div class="fw-bold">
                                        <a href="{{ route('clientes.show', $factura->contrato->cliente->id) }}" target="_blank"  class="text-decoration-none text-primary fw-bold">
                                            {{ $factura->contrato->cliente->nombre }}
                                        </a>
                                    </div>

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
                                        wire:loading.attr="disabled"
                                        class="btn btn-sm {{ $factura->contrato->cliente->estado == 'activo' ? 'btn-danger' : 'btn-primary' }}"
                                        title="{{ $factura->contrato->cliente->estado == 'activo' ? 'Cortar servicio' : 'Activar servicio' }}"
                                        onclick="confirmarCambioEstado({{ $factura->contrato->cliente->id }}, '{{ $this->getId() }}', '{{ $factura->contrato->cliente->estado }}')"
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
        function confirmarCorteMasivo() {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se cortarán todos los contratos pendientes. Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, cortar ahora',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Encuentra el componente Livewire que emitió la vista
                    let component = window.Livewire.find('{{ $this->getId() }}');
                    component.call('iniciarCorteMasivo');
                }
            });
        }
    </script>
    
    <script>
        function confirmarCambioEstado(clienteId, componentId, estadoActual) {
            const accion = estadoActual === 'activo' ? 'cortar' : 'activar';
            const titulo = estadoActual === 'activo' ? '¿Deseas cortar el servicio?' : '¿Deseas activar el servicio?';
            const texto = estadoActual === 'activo'
                ? 'El cliente perderá el acceso a internet inmediatamente.'
                : 'El cliente volverá a tener acceso a internet.';

            Swal.fire({
                title: titulo,
                text: texto,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: estadoActual === 'activo' ? '#d33' : '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    let component = window.Livewire.find(componentId);
                    component.call('cambiarEstado', clienteId);
                }
            });
        }
    </script>



</div>