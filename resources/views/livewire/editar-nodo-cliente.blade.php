<div>
    <div class="container-fluid py-4">
        <div class="row ">
            <div class="col-md-8 col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Modificar Nodo del Cliente</h5>
                    </div>
                    
                    <div class="card-body">
                        <form wire:submit.prevent="actualizarContrato">
                            @csrf
                            
                            <!-- Selector de Nodos -->
                            <div class="mb-3">
                                <label for="node" class="form-label fw-bold">Nodo:</label>
                                <select id="node" class="form-select" wire:model="selectedNodeId" wire:change="changeNode" required>
                                    <option value="" selected disabled>Selecciona un nodo</option>
                                    @foreach($nodos as $node)
                                        <option value="{{ $node->id }}">{{ $node->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
    
                            <!-- Selector de Planes -->
                            <div class="mb-3">
                                <label for="plan_id" class="form-label fw-bold">Plan:</label>
                                <select id="plan_id" class="form-select" wire:model="plan_id" required>
                                    <option value="" selected disabled>Selecciona un plan</option>
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
    
                            <!-- Precio -->
                            <div class="mb-4">
                                <label for="precio" class="form-label fw-bold">Precio:</label>
                                <input type="text" class="form-control" wire:model="precio" 
                                       pattern="^\d+(\.\d{1,3})?$" title="El precio debe tener hasta 3 decimales" required>
                                <div class="form-text">Ejemplo: 80.000</div>
                            </div>
    
                            <!-- Botones -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary me-md-2">
                                    <i class="bi bi-save me-2"></i>Modificar Nodo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
