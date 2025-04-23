<div>
    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <!-- Encabezado con datos del cliente -->
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                Asignar Contrato: <span class="fw-light">{{ $cliente->nombre }}</span>
                            </h5>
                            <span class="badge bg-light text-dark">ID: {{ $cliente->id }}</span>
                        </div>
                    </div>
    
                    <div class="card-body">
                        <!-- Info rápida del cliente -->
                        <div class="alert alert-light mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <strong>Dirección:</strong> {{ $cliente->direccion }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Estado:</strong> 
                                    <span class="badge bg-success">Activo</span>
                                </div>
                            </div>
                        </div>
    
                        <!-- Formulario Livewire -->
                        <form wire:submit.prevent="guardarContrato">
                            <div class="row g-3">
                                <!-- Selector de Nodo -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Nodo</label>
                                    <select class="form-select" wire:model="selectedNodeId" wire:change="changeNode" required>
                                        <option value="" selected disabled>Seleccionar nodo...</option>
                                        @foreach($nodos as $node)
                                            <option value="{{ $node->id }}">{{ $node->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <!-- Selector de Plan -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Plan</label>
                                    <select class="form-select" wire:model="plan_id" required>
                                        <option value="" selected disabled>Seleccionar plan...</option>
                                        @foreach($planes as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
    
                                <!-- Fechas -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha Inicio</label>
                                    <input type="date" class="form-control" wire:model="fecha_inicio" required>
                                </div>
    
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Fecha Fin</label>
                                    <input type="date" class="form-control" wire:model="fecha_fin" required>
                                </div>
    
                                <!-- Precio -->
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Precio</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="text" class="form-control" 
                                               wire:model="precio"
                                               pattern="^\d+(\.\d{1,3})?$"
                                               title="Formato: 99999.999"
                                               required>
                                    </div>
                                    <small class="text-muted">Ejemplo: 80.000</small>
                                </div>
    
                                <!-- Botones -->
                                <div class="col-12 mt-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('contratoIndex') }}" class="btn btn-outline-secondary">
                                            Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                            <span wire:loading.remove>Asignar Contrato</span>
                                            <span wire:loading>
                                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                Procesando...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

