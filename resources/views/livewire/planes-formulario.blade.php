<div class="container-fluid min-vh-100 d-flex flex-column">

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
                          <div class="col-md-3 col-sm-6 mb-4">
                              <div class="card h-100 shadow-sm">
                                  <div class="card-header py-2 bg-light">
                                      <h6 class="card-title mb-0 d-flex justify-content-between align-items-center">
                                          <span>{{ $plan->nombre }}</span>
                                          <span class="badge bg-info">{{ $plan->nodo ? $plan->nodo->nombre : 'Sin nodo' }}</span>
                                      </h6>
                                  </div>
                                  
                                  <div class="card-body p-3">
                                      <div class="d-flex justify-content-around mb-3">
                                          <div class="text-center">
                                              <i class="fas fa-upload text-primary mb-1"></i>
                                              <h6 class="mb-0">{{ $plan->velocidad_subida }} Mbps</h6>
                                              <small class="text-muted">Subida</small>
                                          </div>
                                          <div class="text-center">
                                              <i class="fas fa-download text-success mb-1"></i>
                                              <h6 class="mb-0">{{ $plan->velocidad_bajada }} Mbps</h6>
                                              <small class="text-muted">Bajada</small>
                                          </div>
                                      </div>
                                      
                                      <div class="border-top pt-2">
                                          <p class="small text-muted mb-1"><strong>Descripción:</strong> {{ Str::limit($plan->descripcion, 50) }}</p>
                                          <p class="small text-muted mb-0"><strong>Rehuso:</strong> {{ $plan->rehuso }}</p>
                                      </div>
                                  </div>
                                  
                                  <div class="card-footer py-2 bg-white">
                                      <div class="d-flex justify-content-between align-items-center">
                                          <div class="btn-group btn-group-sm">
                                              <button wire:click="editPlan({{ $plan->id }})" 
                                                      class="btn btn-outline-primary">
                                                  <i class="fas fa-edit"></i>
                                              </button>
                                              <button 
                                                    x-data
                                                    @click.prevent="if (confirm('¿Estás seguro de que deseas eliminar este plan?')) { $wire.deletePlan({{ $plan->id }}) }" 
                                                    class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                               </button>
                                          </div>
                                          
                                          @if($plan->nodo)
                                              <button wire:click="activatePlan({{ $plan->id }})" 
                                                      wire:loading.attr="disabled"
                                                      class="btn btn-sm btn-{{ $currentPlanActivating == $plan->id ? 'warning' : 'success' }}">
                                                  @if($currentPlanActivating == $plan->id)
                                                      <span class="spinner-border spinner-border-sm" role="status"></span>
                                                      <span class="d-none d-md-inline">Activando...</span>
                                                  @else
                                                      <i class="fas fa-power-off"></i>
                                                      <span class="d-none d-md-inline">Activar</span>
                                                  @endif
                                              </button>
                                          @else
                                              <button class="btn btn-sm btn-outline-secondary" disabled>
                                                  <i class="fas fa-exclamation-circle"></i>
                                                  <span class="d-none d-md-inline">Sin nodo</span>
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
                    <!-- Card con diseño mejorado pero misma estructura -->
                    <div class="card mx-auto border-0 shadow-sm" style="max-width: 500px;">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0 text-primary">
                                <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Plan
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Mensaje de éxito (igual que antes) -->
                            @if (session()->has('message'))
                                <div class="alert alert-success">
                                    {{ session('message') }}
                                </div>
                            @endif
                
                            <!-- Formulario con MISMOS bindings y validaciones originales -->
                            <form wire:submit.prevent="submitPlan">
                                <!-- Campo Nombre (mismo wire:model y validación) -->
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">Nombre</label>
                                    <input type="text" class="form-control shadow-sm" id="nombre" wire:model="nombre" required>
                                </div>
                
                                <!-- Campo Descripción (idéntico al original) -->
                                <div class="form-group mb-3">
                                    <label for="descripcion" class="form-label">Descripción</label>
                                    <textarea class="form-control shadow-sm" id="descripcion" wire:model="descripcion" required></textarea>
                                </div>
                
                                <!-- Campos de Velocidad (misma estructura) -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="velocidad_bajada" class="form-label">Velocidad de bajada</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control shadow-sm" id="velocidad_bajada" wire:model="velocidad_bajada" min="1" required>
                                                <span class="input-group-text">Mbps</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="velocidad_subida" class="form-label">Velocidad de subida</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control shadow-sm" id="velocidad_subida" wire:model="velocidad_subida" min="1" required>
                                                <span class="input-group-text">Mbps</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                
                                <!-- Campo Rehuso (exactamente igual que el original) -->
                                <div class="form-group mb-3">
                                    <label for="rehuso" class="form-label">Rehuso</label>
                                    <select class="form-control shadow-sm" id="rehuso" wire:model="rehuso" required>
                                        <option value="">Selecciona un rehuso</option>
                                        <option value="1:1">1:1</option>
                                        <option value="1:2">1:2</option>
                                        <option value="1:4">1:4</option>
                                        <option value="1:6">1:6</option>
                                    </select>
                                    @error('rehuso') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                
                                <!-- Campo Nodo (misma estructura) -->
                                <div class="form-group mb-4">
                                    <label for="nodo_id" class="form-label">Nodo</label>
                                    <select class="form-control shadow-sm" id="nodo_id" wire:model="nodo_id" required>
                                        <option value="" disabled selected>Seleccione un nodo</option>
                                        @foreach($nodos as $nodo)
                                            <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                
                                <!-- Botón (mismo wire:submit) -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Agregar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
    
     
    <!-- Modal Editar Planes -->
    <div class="modal fade @if($showModal) show @endif" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: @if($showModal) block @else none @endif;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Plan
                    </h5>
                    <button wire:click="hide" type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                    <form wire:submit.prevent="updatePlan">
                        <!-- Campos del formulario -->
                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <!-- Nombre -->
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Nombre
                                    </label>
                                    <input type="text" class="form-control shadow-sm" id="nombre" wire:model="nombre">
                                </div>
                                
                                <!-- Descripción -->
                                <div class="form-group mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>Descripción
                                    </label>
                                    <textarea class="form-control shadow-sm" id="descripcion" wire:model="descripcion" rows="3"></textarea>
                                </div>
                                
                                <!-- Rehuso -->
                                <div class="form-group mb-3">
                                    <label for="rehuso" class="form-label">
                                        <i class="fas fa-exchange-alt me-1"></i>Rehuso
                                    </label>
                                    <select class="form-control shadow-sm" id="rehuso" wire:model="rehuso">
                                        <option value="">Seleccione un rehuso</option>
                                        <option value="1:1">1:1</option>
                                        <option value="1:2">1:2</option>
                                        <option value="1:4">1:4</option>
                                        <option value="1:6">1:6</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Velocidad de Bajada -->
                                <div class="form-group mb-3">
                                    <label for="velocidad_bajada" class="form-label">
                                        <i class="fas fa-download me-1"></i>Velocidad de bajada
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control shadow-sm" id="velocidad_bajada" 
                                            wire:model="velocidad_bajada" min="0">
                                        <span class="input-group-text">Mbps</span>
                                    </div>
                                </div>
                                
                                <!-- Velocidad de Subida -->
                                <div class="form-group mb-3">
                                    <label for="velocidad_subida" class="form-label">
                                        <i class="fas fa-upload me-1"></i>Velocidad de subida
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control shadow-sm" id="velocidad_subida" 
                                            wire:model="velocidad_subida" min="0">
                                        <span class="input-group-text">Mbps</span>
                                    </div>
                                </div>
                                
                                <!-- Nodo -->
                                <div class="form-group mb-3">
                                    <label for="nodo_id" class="form-label">
                                        <i class="fas fa-server me-1"></i>Nodo
                                    </label>
                                <select class="form-control shadow-sm" id="nodo_id" wire:model="nodo_id" required>
                                        <option value="">Seleccione un nodo</option>
                                        @foreach($nodos as $nodo)
                                            <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>
                        </div>
                        
                        <!-- Pie del Modal -->
                        <div class="modal-footer">
                            <button wire:click="hide" type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit"
                                    onclick="return confirm('¿Estás seguro de que deseas actualizar el plan?')"
                                    wire:loading.attr="disabled"
                                    wire:target="updatePlan"
                                    class="btn btn-primary">
                                <span wire:loading.remove wire:target="updatePlan">
                                    <i class="fas fa-save me-1"></i>Actualizar
                                </span>
                                <span wire:loading wire:target="updatePlan">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Procesando...
                                </span>
                            </button>

                        </div>
                        <!-- Deshabilitar formulario durante carga -->
                        <div wire:loading.class="pe-none" wire:target="updatePlan">
                        </div>
                    </form>
                </div>
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
