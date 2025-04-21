<div>
    
   
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h4><strong>Nombre:</strong> <span class="text-success">{{$cliente->nombre}}</span></h4>
            </div>
            <div class="card-body">
                <p><strong>ID Cliente:</strong>{{$cliente->id}} </p>
                <p><strong>Nombre:</strong>{{$cliente->nombre}} </p>
                <p><strong>Dirección:</strong>{{$cliente->direccion}}</p>
               
                <form wire:submit.prevent="guardarContrato">
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
                
                    <!-- Fecha de inicio -->
                    <div class="mb-3">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                        <input type="date" class="form-control" wire:model="fecha_inicio" required>
                    </div>
                
                    <!-- Fecha de fin -->
                    <div class="mb-3">
                        <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                        <input type="date" class="form-control" wire:model="fecha_fin" required>
                    </div>
                
                    <!-- Precio -->
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio:</label>
                        <input type="text" class="form-control" wire:model="precio" pattern="^\d+(\.\d{1,3})?$" title="El precio debe tener hasta 3 decimales" required>
                        <small class="form-text text-muted">Ejemplo: 80.000</small>
                    </div>
                
                    <button type="submit" class="btn btn-primary">Asignar Contrato</button>
                    <a href="{{ route('contratoIndex') }}" class="btn btn-secondary">Cancelar</a>
                </form> 
            </div>
        </div>
        
    </div> 
</div>

