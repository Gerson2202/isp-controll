<div>
    
        <!-- Notificaciones -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    
        @if ($errors->has('activation'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ $errors->first('activation') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    <div class="card text-center">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <!-- Pestaña 1 -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="planes-tab" data-bs-toggle="tab" href="#planes" role="tab" aria-controls="planes" aria-selected="true">Planes</a>
                </li>
                <!-- Pestaña 2 -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="link-tab" data-bs-toggle="tab" href="#link" role="tab" aria-controls="link" aria-selected="false">Agregar Plan</a>
                </li>
                <!-- Pestaña 3 -->
                {{-- <li class="nav-item" role="presentation">
                    <a class="nav-link" id="disabled-tab" data-bs-toggle="tab" href="#disabled" role="tab" aria-controls="disabled" aria-selected="false">Disabled</a>
                </li> --}}
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                <!-- Contenido Planes (Pestaña activa por defecto) -->
                <div class="tab-pane fade show active" id="planes" role="tabpanel" aria-labelledby="planes-tab">
                    <div class="row">
                          <!-- Mostrar mensaje de éxito -->
                          @if($successMessage)
                          <div class="alert alert-success alert-dismissible fade show" id="successMessage" role="alert">
                              {{ $successMessage }}
                              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                          @endif  
                        @foreach($plans as $plan)
                            <div class="col-md-3">
                                <div class="card">
                                  
                                    <div class="card-header">
                                        <h5 class="card-title">{{ $plan->nombre }} <strong>Nodo:{{ $plan->nodo ? $plan->nodo->nombre : 'Ninguno' }} </strong></h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <i class="fas fa-upload text-primary"></i>
                                                <h5>{{ $plan->velocidad_subida }} Mbps</h5>
                                                <small>Subida</small>
                                            </div>
                                            <div class="col-6">
                                                <i class="fas fa-download text-success"></i>
                                                <h5>{{ $plan->velocidad_bajada }} Mbps</h5>
                                                <small>Bajada</small>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <p class="small"> <strong>Descripción: </strong>{{ $plan->descripcion }}</p>
                                        <p><strong>Rehuso:</strong> {{ $plan->rehuso }}</p>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between">
                                            <div class="btn-group">
                                                <button wire:click="editPlan({{ $plan->id }})" 
                                                        class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button wire:click="deletePlan({{ $plan->id }})" 
                                                        class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            @if($plan->nodo)
                                            <button wire:click="activatePlan({{ $plan->id }})" 
                                                    wire:loading.attr="disabled"
                                                    class="btn btn-sm btn-{{ $currentPlanActivating == $plan->id ? 'warning' : 'success' }}">
                                                @if($currentPlanActivating == $plan->id)
                                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                                    Activando...
                                                @else
                                                    <i class="fas fa-power-off"></i> Activar
                                                @endif
                                            </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fas fa-exclamation-circle"></i> Sin nodo
                                                </button>
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- <a href="#" class="btn btn-primary">Ver más planes</a> --}}
                </div>
                <!-- Contenido Link (Pestaña 2) -->
                <div class="tab-pane fade" id="link" role="tabpanel" aria-labelledby="link-tab">
                    <!-- Card que contiene el formulario, sin bordes -->
                    <div class="card mx-auto border-0" style="max-width: 500px;">
                        <div class="card-header">
                            <h5 class="card-title">Crear Nuevo Plan</h5>
                        </div>
                        <div class="card-body">
                            <!-- Mostrar mensaje de éxito si existe -->
                            @if (session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif
                
                            <!-- Formulario dentro del card con Livewire -->
                            <form wire:submit.prevent="submitPlan">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" wire:model="nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control" id="descripcion" wire:model="descripcion" required></textarea>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="precio">Precio</label>
                                    <input type="number" class="form-control" id="precio" wire:model="precio"  placeholder="0.000" step="0.001" required                                </div>
                                </div> --}}
                                    <div class="form-group">
                                    <label for="velocidad_bajada">Velocidad de bajada</label>
                                    <input type="number" class="form-control" id="velocidad_bajada" wire:model="velocidad_bajada" required>
                                </div>
                                <div class="form-group">
                                    <label for="velocidad_subida">Velocidad de subida</label>
                                    <input type="number" class="form-control" id="velocidad_subida" wire:model="velocidad_subida" required>
                                </div>
                                <div class="mb-3">
                                    <label for="rehuso" class="form-label">Rehuso</label>
                                    <select class="form-control" id="rehuso" wire:model="rehuso" required>
                                        <option value="">Selecciona un rehuso</option>
                                        <option value="1:1">1:1</option>
                                        <option value="1:2">1:2</option>
                                        <option value="1:4">1:4</option>
                                        <option value="1:6">1:6</option>
                                    </select>
                                    @error('rehuso') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                                        <!-- Seleccionar Nodo -->
                                <div class="mb-3">
                                    <label for="nodo_id" class="form-label">Nodo</label>
                                    <select class="form-select" id="nodo_id" wire:model="nodo_id" required>
                                        <option value="" disabled selected>Seleccione un nodo</option>                                        <option value="">Selecciona un rehuso</option>
                                        <option value="">Seleccionar nodo</option>
                                        @foreach($nodos as $nodo)
                                            <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button  type="submit" class="btn btn-info">Agregar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                
                
                <!-- Contenido Disabled (Pestaña 3) -->
                {{-- <div class="tab-pane fade" id="disabled" role="tabpanel" aria-labelledby="disabled-tab">
                    <h5 class="card-title">Disabled</h5>
                    <p class="card-text">Este enlace está deshabilitado.</p>
                    <a href="#" class="btn btn-secondary" disabled>Enlace deshabilitado</a>
                </div> --}}
            </div>
        </div>
    </div>
    
     
    <!-- Modal Editar Planes -->
    <div class="modal fade @if($showModal) show @endif" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: @if($showModal) block @else none @endif;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Título del Modal</h5>
                    <button wire:click="hide" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="updatePlan">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" wire:model="nombre">
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" wire:model="descripcion"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="velocidad_bajada">Velocidad de bajada</label>
                            <input type="number" class="form-control" id="velocidad_bajada" wire:model="velocidad_bajada">
                        </div>
                        <div class="form-group">
                            <label for="velocidad_subida">Velocidad de subida</label>
                            <input type="number" class="form-control" id="velocidad_subida" wire:model="velocidad_subida">
                        </div>
                        <div class="form-group">
                            <label for="rehuso">Rehuso</label>
                            <input type="text" class="form-control" id="rehuso" wire:model="rehuso">
                        </div>
                        <!-- Nodo -->
                        <div class="mb-3">
                            <label for="nodo_id" class="form-label">Nodo</label>
                            <select class="form-select" id="nodo_id" wire:model="nodo_id" required>
                                <option value="">Seleccione un nodo</option>
                                @foreach($nodos as $nodo)
                                    <option value="{{ $nodo->id }}" {{ $nodo->id == $nodo_id ? 'selected' : '' }}>
                                        {{ $nodo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button  type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button wire:click="hide" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

      <!-- Script de Bootstrap para manejar el modal -->
      @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('livewire:load', function () {
                // Manejo de modales
                @this.on('showModal', () => {
                    $('#exampleModal').modal('show');
                });
        
                @this.on('hideModal', () => {
                    $('#exampleModal').modal('hide');
                });
        
            });
        </script>        
      @endpush
      <!-- Script de JavaScript para manejar el mensaje de éxito -->  
</div>
