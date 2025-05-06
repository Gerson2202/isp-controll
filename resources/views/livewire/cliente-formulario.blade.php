<!-- resources/views/livewire/cliente-form.blade.php -->
<div>
    <!-- Formulario -->
    <div class="d-flex justify-content-center align-items-center" >
        <div class="card col-12 col-md-12">
           
            <div class="card-header text-center">
                <h5>Registrar Cliente</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <!-- Columna 1: Datos Personales -->
                        <div class="col-md-6">
                            <div class="card-header bg-info-light mb-3">
                                <h5 class="mb-0"><i class="fas fa-user-circle me-2 text-info"></i>Datos Personales</h5>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="nombre" class="form-label">
                                    <i class="fas fa-user me-2 text-primary"></i>Nombre completo
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                    <input type="text" wire:model="nombre" class="form-control" id="nombre" required>
                                </div>
                                @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                
                            <div class="form-group mb-3">
                                <label for="cedula" class="form-label">
                                    <i class="fas fa-id-card me-2 text-primary"></i>Cédula
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card text-muted"></i></span>
                                    <input type="number" wire:model="cedula" class="form-control" id="cedula" required>
                                </div>
                                @error('cedula') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                
                            <div class="form-group mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-2 text-primary"></i>Teléfono
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone text-muted"></i></span>
                                    <input type="text" wire:model="telefono" class="form-control" id="telefono" required>
                                </div>
                                @error('telefono') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                
                        <!-- Columna 2: Datos Generales -->
                        <div class="col-md-6">
                            <div class="card-header bg-warning-light mb-3">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2 text-warning"></i>Datos Generales</h5>
                            </div>
                
                            <div class="form-group mb-3">
                                <label for="direccion" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>Dirección
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-map-marker-alt text-muted"></i></span>
                                    <input type="text" wire:model="direccion" class="form-control" id="direccion" required>
                                </div>
                                @error('direccion') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2 text-primary"></i>Email
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                                    <input type="email" wire:model="email" class="form-control" id="email" required>
                                </div>
                                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                
                            <div class="form-group mb-3">
                                <label for="estado" class="form-label">
                                    <i class="fas fa-power-off me-2 text-primary"></i>Estado
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-power-off text-muted"></i></span>
                                    <select wire:model="estado" class="form-control" id="estado" disabled>
                                        <option value="suspendido" selected>Suspendido</option>
                                    </select>
                                </div>
                                @error('estado') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                
                    <!-- Botón de enviar -->
                    <div class="form-group text-center mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i>Guardar Cliente
                        </button>
                    </div>
                </form>
                
                <style>
                    .bg-info-light {
                        background-color: #d1ecf1; /* Color azul claro */
                    }
                    .bg-warning-light {
                        background-color: #fff3cd; /* Color amarillo claro */
                    }
                </style>
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