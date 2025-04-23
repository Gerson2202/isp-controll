<div>
    <div>
        <div class="container-fluid">
            <div class="card">
                
                <div class="card-body">
                    <form wire:submit.prevent="actualizarContrato">
                        @csrf
                    
                        <div>
                            <!-- Selector de Nodos -->
                            <div>
                                <label for="node">Selecciona un nodo:</label>
                                <select id="node" wire:model="selectedNodeId" wire:change="changeNode" required>
                                    <option value="">Selecciona un nodo</option>
                                    @foreach($nodos as $node)
                                        <option value="{{ $node->id }}">{{ $node->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
    
                            <!-- Selector de Planes -->
                            <div>
                                <label for="plan">Selecciona un plan:</label>
                                <select id="plan_id" wire:model="plan_id" required >
                                    <option value="">Selecciona un plan</option>
                                    @foreach($planes as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        
                        </div>
                    
                        <!-- Precio -->
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio:</label>
                            <input type="text" class="form-control" wire:model="precio" pattern="^\d+(\.\d{1,3})?$" title="El precio debe tener hasta 3 decimales" required>
                            <small class="form-text text-muted">Ejemplo: 80.000</small>
                        </div>
                    
                        <button type="submit" class="btn btn-primary">Modificar Nodo</button>
                    </form> 
                </div>
            </div>
            
        </div> 
    </div>
    
    
</div>
