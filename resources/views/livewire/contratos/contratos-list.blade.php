<div>
    <!-- Barra de búsqueda -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-center">
                <div class="col-md-6">
                    <input type="text" class="form-control" 
                           placeholder="Buscar por cliente, tecnología o estado..."
                           wire:model.lazy="search">
                </div>
                <div class="col-md-3">
                    <select class="form-select" wire:model="perPage">
                        <option value="10">10 registros</option>
                        <option value="25">25 registros</option>
                        <option value="50">50 registros</option>
                    </select>
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
                            <th wire:click="sortBy('fecha_inicio')" style="cursor: pointer;">
                                Inicio
                                @if($sortField === 'fecha_inicio')
                                    {{ $sortDirection === 'asc' ? '↑' : '↓' }}
                                @endif
                            </th>
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
                            <td>{{ date('d/m/Y', strtotime($contrato->fecha_inicio)) }}</td>
                            <td>{{ $contrato->fecha_fin ? date('d/m/Y', strtotime($contrato->fecha_fin)) : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $contrato->estado === 'activo' ? 'success' : ($contrato->estado === 'inactivo' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($contrato->estado) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary"
                                        wire:click="openEditModal({{ $contrato->id }})">
                                    Editar
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-3">
                {{ $contratos->links() }}
            </div>
        </div>
    </div>

    <!-- Modal de edición -->
    <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Editar Contrato #{{ $contratoId ?? '' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updateContrato">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Cliente</label>
                                <select class="form-select" wire:model="cliente_id" required>
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
                                <select class="form-select" wire:model="plan_id" required>
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
                                <input type="text" class="form-control" wire:model="tecnologia" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select class="form-select" wire:model="estado" required>
                                    <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ $estado == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ $estado == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" wire:model="fecha_inicio" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" wire:model="fecha_fin">
                            </div>
                        </div>
                        <div class="modal-footer mt-4">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('showModal', () => {
            const modal = new bootstrap.Modal(document.getElementById('editModal'));
            modal.show();
        });
        
        Livewire.on('hideModal', () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
            if (modal) {
                modal.hide();
            }
        });
    });
</script>
@endpush