<div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-md-8 col-lg-12">
                @if($isLoading)
                 <!-- Estado de carga -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                             <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando información del contrato...</p>
                    </div>
                @elseif(!$hasContract)
                     <!-- Estado cuando no hay contrato -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2">Cargando información del cliente...Cliente no tiene contrato</p>
                    </div>
                @else
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Modificar Nodo del Cliente</h5>
                        </div>
                        
                        <div class="card-body">
                            
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nodo actual:</label>
                                        <p class="form-control-plaintext">{{ $cliente->contrato->plan->nodo->nombre ?? 'sin nodo'}}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Plan actual:</label>
                                        <p class="form-control-plaintext">{{ $cliente->contrato->plan->nombre }}</p>
                                    </div>
                                </div>
                                <!-- Formulario normal cuando hay contrato -->
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
                                            <option value="" selected >Selecciona un plan</option>
                                            @foreach($planes as $plan)
                                                <option value="{{ $plan->id }}">{{ $plan->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
            
                                    <!-- Precio -->
                                    <div class="mb-4">
                                        <label for="precio" class="form-label fw-bold">Precio:</label>
                                        <input 
                                            type="number" 
                                            class="form-control @error('precio') is-invalid @enderror" 
                                            wire:model="precio"  
                                            min="0"
                                            required
                                        >
                                        @error('precio')
                                            <div class="invalid-feedback d-block">
                                                Número inválido
                                            </div>
                                        @enderror
                                    </div>
            
                                    <!-- Botones -->
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button 
                                            type="submit" 
                                            class="btn btn-primary me-md-2" 
                                            wire:loading.attr="disabled"
                                        >
                                            <span wire:loading.class="invisible">
                                                <i class="bi bi-save me-2"></i>Modificar Nodo
                                            </span>
                                            <span wire:loading>
                                                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                                Procesando...
                                            </span>
                                        </button>
                                    </div>
                                </form>
                            
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- Script Para manejo de Notificaciones Tosatar -->
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                toastr[data.type](data.message);
            });
        });
    </script>      
  @endpush

</div>