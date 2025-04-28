<div>
    <div class="container-fluid py-1">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-desktop me-2"></i>Detalles del Equipo</h4>
                <button class="btn btn-light" wire:click="mostrarModal">
                    <i class="fas fa-edit me-1"></i> Editar / Asignar
                </button>
            </div>
            
            <div class="card-body">
                
                @if($inventario)
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Modelo</label>
                                        <p class="fw-bold">{{ $inventario->modelo->nombre ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">MAC Address</label>
                                        <p class="fw-bold">{{ $inventario->mac }}</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted small mb-1">Descripción</label>
                                    <p class="fw-bold">{{ $inventario->descripcion }}</p>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-user-tag me-2"></i>Asignación</h5>
                                <div class="d-flex align-items-center">
                                    @if($inventario->cliente_id)
                                        <span class="badge bg-success me-2"><i class="fas fa-user"></i></span>
                                        <span>
                                            Asignado a cliente: 
                                            <a href="{{ route('clientes.show', $inventario->cliente_id) }}" class="text-success fw-bold" target="_blank" >
                                                {{ $inventario->cliente->nombre }}
                                            </a>
                                        </span>
                                    @elseif($inventario->nodo_id)
                                        <span class="badge bg-primary me-2"><i class="fas fa-network-wired"></i></span>
                                        <span class="text-primary fw-bold">Asignado a nodo: {{ $inventario->nodo->nombre }}</span>
                                    @else
                                        <span class="badge bg-secondary me-2"><i class="fas fa-exclamation-circle"></i></span>
                                        <span class="text-danger fw-bold">Equipo sin asignar</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted mb-3"><i class="fas fa-image me-2"></i>Imagen del Modelo</h6>
                                    @if (!empty($inventario->modelo->foto) && file_exists(public_path('storage/' . $inventario->modelo->foto)))
                                        <img src="{{ asset('storage/' . $inventario->modelo->foto) }}" 
                                             alt="Foto del modelo" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="max-height: 200px;">
                                    @else
                                        <div class="py-4 text-muted">
                                            <i class="fas fa-image fa-3x mb-2"></i>
                                            <p class="mb-0">No hay imagen disponible</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> No se encontró el inventario.
                    </div>
                @endif
            </div>
        </div>
    
        <!-- Modal -->
        <div class="modal fade @if($modalVisible) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar / Asignar Inventario</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="cerrarModal"></button>
                    </div>
                    
                    <div class="modal-body">    
                        <form wire:submit.prevent="guardar">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="modelo_id" class="form-label">Modelo</label>
                                    <select id="modelo_id" class="form-select" wire:model="modelo_id" required>
                                        <option value="">Seleccionar Modelo</option>
                                        @foreach($modelos as $modelo)
                                            <option value="{{ $modelo->id }}">{{ $modelo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="mac" class="form-label">MAC Address</label>
                                    <input type="text" id="mac" class="form-control" wire:model="mac" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea id="descripcion" class="form-control" wire:model="descripcion" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fecha" class="form-label">Fecha de asignación</label>
                                    <input type="date" id="fecha" class="form-control" wire:model="fecha" required>
                                </div>
                            </div>
                            
                            <hr class="my-4">
                            
                            <h6 class="mb-3 text-primary"><i class="fas fa-users me-2"></i>Asignación</h6>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cliente_id" class="form-label">Asignar a Cliente</label>
                                    <select id="cliente_id" class="form-select select2" wire:model="cliente_id">
                                        <option value="">Seleccionar Cliente</option>
                                        @if(!$nodo_id)
                                            <option value="" class="text-primary">Desvincular Cliente</option>
                                        @endif
                                        @foreach($clientes as $cliente)
                                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="nodo_id" class="form-label">Asignar a Nodo</label>
                                    <select id="nodo_id" class="form-select" wire:model="nodo_id">
                                        <option value="">Seleccionar Nodo</option>
                                        @if(!$cliente_id)
                                            <option value="" class="text-primary">Desvincular Nodo</option>
                                        @endif
                                        @foreach($nodos as $nodo)
                                            <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-outline-secondary me-3" wire:click="cerrarModal">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
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
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });
            });
        </script>
    @endpush

</div>