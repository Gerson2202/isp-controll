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
                    @if($successMessage)
                    <div class="alert alert-success alert-dismissible fade show mb-3" id="successMessage" role="alert">
                        {{ $successMessage }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Filtro por nodos funcional -->
                    <div class="mb-3">
                        <label for="nodoFilter" class="form-label">Filtrar por nodo:</label>
                        <select class="form-select" id="nodoFilter" wire:model.live="nodo_id_Filtro">
                            <option value="">Todos los nodos</option>
                            @foreach($nodos as $nodo)
                                <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tabla con scroll -->
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover table-striped">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Velocidad</th>
                                    <th>Rehuso</th>
                                    <th>Nodo</th>
                                    <th>Descripción</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($filteredPlans as $plan)
                                <tr>
                                    <td><strong>{{ $plan->nombre }}</strong></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-download me-1"></i>{{ $plan->velocidad_bajada }} Mbps
                                            </span>
                                            <span class="badge bg-primary">
                                                <i class="fas fa-upload me-1"></i>{{ $plan->velocidad_subida }} Mbps
                                            </span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">{{ $plan->rehuso }}</span></td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $plan->nodo ? $plan->nodo->nombre : 'Sin nodo' }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ Str::limit($plan->descripcion, 40) }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button wire:click="editPlan({{ $plan->id }})" 
                                                    class="btn btn-outline-primary" 
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button x-data
                                                    @click.prevent="if (confirm('¿Estás seguro de eliminar este plan?')) { $wire.deletePlan({{ $plan->id }}) }" 
                                                    class="btn btn-outline-danger"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @if($plan->nodo)
                                                <button wire:click="activatePlan({{ $plan->id }})" 
                                                        wire:loading.attr="disabled"
                                                        class="btn btn-sm btn-{{ $currentPlanActivating == $plan->id ? 'warning' : 'success' }}"
                                                        title="Activar">
                                                    @if($currentPlanActivating == $plan->id)
                                                        <span class="spinner-border spinner-border-sm" role="status"></span>
                                                    @else
                                                        <i class="fas fa-power-off"></i>
                                                    @endif
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Sin nodo asignado">
                                                    <i class="fas fa-exclamation-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
                                    <select class="form-control shadow-sm" id="nodo_id" wire:model="nodo_id" required
                                            @if($planHasContracts) disabled @endif>
                                        <option value="">Seleccione un nodo</option>
                                        @foreach($nodos as $nodo)
                                            <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                        @endforeach
                                    </select>

                                    @if($planHasContracts)
                                    <div class="alert alert-warning alert-sm mt-2">
                                        <i class="fas fa-lock me-1"></i> No se puede modificar el nodo porque el plan tiene contratos asociados
                                    </div>
                                    @endif

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
