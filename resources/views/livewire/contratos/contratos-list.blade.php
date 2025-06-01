<div>
    <div class="container-fluid min-vh-100 d-flex flex-column">

        <!-- Barra de búsqueda -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <div class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <input 
                        type="text" 
                        class="form-control" 
                        placeholder="Buscar por cliente, tecnología o estado..."
                        wire:model.live="search">
                    </div>
                </div>
            </div>
        </div>
    
        <!-- Tabla de contratos -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('cliente_id')" style="cursor: pointer;">
                                    Cliente 
                                    @if($sortField === 'cliente_id') 
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>
                                <th>Plan</th>
                                <th wire:click="sortBy('tecnologia')" style="cursor: pointer;">
                                    Tecnología
                                    @if($sortField === 'tecnologia')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>
                                 <th wire:click="sortBy('precio')" style="cursor: pointer;">
                                    Precio
                                    @if($sortField === 'precio')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>
                                {{-- <th wire:click="sortBy('fecha_inicio')" style="cursor: pointer;">
                                    Inicio
                                    @if($sortField === 'fecha_inicio')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th> --}}
                                <th wire:click="sortBy('fecha_fin')" style="cursor: pointer;">
                                    Fin
                                    @if($sortField === 'fecha_fin')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>
                                <th wire:click="sortBy('estado')" style="cursor: pointer;">
                                    Estado
                                    @if($sortField === 'estado')
                                        {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contratos as $contrato)
                            <tr>
                                <td>{{ $contrato->cliente->nombre }}</td>
                                <td>{{ $contrato->plan->nombre }}</td>
                                <td>{{ ucfirst($contrato->tecnologia) }}</td>
                                <td>{{ number_format($contrato->precio, 0, ',', '.') }}</td>
                                {{-- <td>{{ date('d/m/Y', strtotime($contrato->fecha_inicio)) }}</td> --}}
                                <td>{{ $contrato->fecha_fin ? date('d/m/Y', strtotime($contrato->fecha_fin)) : 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $contrato->estado === 'activo' ? 'success' : ($contrato->estado === 'cancelado' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($contrato->estado) }}
                                    </span>
                                </td>
                                <td>
                                
                                    <button wire:click="openEditModal({{ $contrato->id }})" class="btn btn-sm btn-primary">Editar</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <!-- Paginación -->
                    <div class="mt-3">
                    {{ $contratos->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
                    </div>
                </div>
    
            </div>
        </div>
    
        <!-- Modal de edición -->
        <div wire:ignore.self class="modal fade" id="modalCliente" tabindex="-1" aria-labelledby="modalClienteLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">Editar Contrato #{{ $contratoId ?? '' }}</h5>
                            <button wire:click="hide" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="updateContrato">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Cliente</label>
                                        <select class="form-select" wire:model="cliente_id" disabled>
                                            <option value="">Seleccionar cliente</option>
                                            @foreach($clientes as $cliente)
                                                <option value="{{ $cliente->id }}" {{ $cliente->id == $cliente_id ? 'selected' : '' }}>
                                                    {{ $cliente->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Plan</label>
                                        <select class="form-select" wire:model="plan_id" disabled>
                                            <option value="">Seleccionar plan</option>
                                            @foreach($planes as $plan)
                                                <option value="{{ $plan->id }}" {{ $plan->id == $plan_id ? 'selected' : '' }}>
                                                    {{ $plan->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tecnología</label>
                                        <select class="form-select" wire:model="tecnologia" required>
                                            <option value="">Seleccionar tecnología</option>
                                            <option value="Radioenlace">Radioenlace</option>
                                            <option value="Fibra óptica">Fibra óptica</option>
                                        </select>
                                        @error('tecnologia') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" wire:model="estado" required>
                                            <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                            <option value="cancelado" {{ $estado == 'cancelado' ? 'selected' : '' }}>cancelado</option>
                                            <option value="suspendido" {{ $estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                        </select>
                                        <small class="text-danger">Los estados diferente de <strong>activo</strong> no se tendran en cuenta para facturacion.</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha Inicio</label>
                                        <input type="date" class="form-control" wire:model="fecha_inicio" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Fecha Fin</label>
                                        <input type="date" class="form-control" wire:model="fecha_fin" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-semibold">Precio</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" class="form-control" 
                                                wire:model="precio"
                                                required>
                                        </div>
                                        {{-- <small class="text-muted">Ejemplo: 80.000</small> --}}
                                    </div>
                                </div>
                                <div class="modal-footer mt-4">
                                    <button wire:click="hide" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-primary">
                                        <span wire:loading.remove wire:target="updateContrato">Guardar</span>
                                        <span wire:loading wire:target="updateContrato" class="spinner-border spinner-border-sm"></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
        </div>
        
    </div>
    {{-- --------- --}}
    
     <!-- Script para abrir el modal -->
    <script>
        window.addEventListener('abrir-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalCliente'));
            modal.show();
        });

        window.addEventListener('cerrar-modal', () => {
            const modalEl = document.getElementById('modalCliente');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) {
                modal.hide();
            }
        });
    </script>
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                toastr[data.type](data.message);
            });
        });
    </script>

    @push('scripts')
    
    @endpush
</div>