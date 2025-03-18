<!-- resources/views/livewire/cliente-form.blade.php -->
<div>
   

    <!-- Formulario -->
    <div class="d-flex justify-content-center align-items-center" >
        <div class="card col-12 col-md-12">
           
            <div class="card-header text-center">
                <h5>Registrar datos del Cliente</h5>
            </div>
             <!-- Mensaje de éxito -->
             @if($successMessage)
             <div class="alert alert-success alert-dismissible fade show mt-3 mb-3 mx-2" id="successMessage" role="alert">
                 {{ $successMessage }}
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
             </div>
             @endif
            <div class="card-body">
                <form wire:submit.prevent="save">
                    <div class="row">
                        <!-- Columna 1: Datos Personales -->
                        <div class="col-md-6">
                            <h6>Datos Personales</h6>
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" wire:model="nombre" class="form-control" id="nombre" required>
                                @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="cedula">Cédula</label>
                                <input type="text" wire:model="cedula" class="form-control" id="cedula" required>
                                @error('cedula') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="telefono">Teléfono</label>
                                <input type="text" wire:model="telefono" class="form-control" id="telefono" required>
                                @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
    
                        <!-- Columna 2: Datos Generales -->
                        <div class="col-md-6">
                            <h6>Datos Generales</h6>
    
                            <div class="form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" wire:model="direccion" class="form-control"  required>
                                @error('direccion') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" wire:model="email" class="form-control" id="email" required>
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select wire:model="estado" class="form-control" id="estado">
                                    <option value="activo">Activo</option>
                                    <option value="cortado">Cortado</option>
                                    <option value="suspendido">Suspendido</option>
                                </select>
                                @error('estado') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
    
                    <!-- Botón de enviar -->
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                    </div>
                </form>
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