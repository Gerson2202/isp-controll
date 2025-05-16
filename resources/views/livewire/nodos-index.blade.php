<div class="container-fluid min-vh-100 d-flex flex-column">

    <div class="card text-center">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <!-- Pestaña 1 -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="planes-tab" data-bs-toggle="tab" href="#planes" role="tab" aria-controls="planes" aria-selected="true">Nodos</a>
                </li>
                <!-- Pestaña 2 -->
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="link-tab" data-bs-toggle="tab" href="#link" role="tab" aria-controls="link" aria-selected="false">Agregar Nodo</a>
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
                <!-- Mostrar mensaje de éxito -->
            @if($successMessage)
            <div class="alert alert-success alert-dismissible fade show" id="successMessage" role="alert">
                {{ $successMessage }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif 
                <div class="tab-pane fade show active" id="planes" role="tabpanel" aria-labelledby="planes-tab">
                    <div class="row">
                         @foreach($nodos as $nodo)
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Nodo {{ $nodo->nombre }}</h5>
                                        <a href="{{ route('nodos.show', $nodo->id) }}" class="btn btn-outline-info btn-sm">
                                            <i class="fas fa-eye"></i> Ver detalle
                                        </a>
                                    </div>

                                    <div class="card-body">
                                        <p><strong>Ip:</strong> {{ $nodo->ip }}</p>
                                        <p><strong>Latitud:</strong> {{ $nodo->latitud }}</p>
                                        <p><strong>longitud:</strong> {{ $nodo->longitud }} </p>
                                        <p><strong>Puerto Api:</strong> {{ $nodo->puerto_api }} </p>
                                        <p><strong>Coordenadas:</strong> 
                                            <a href="https://www.google.com/maps?q={{ $nodo->latitud }},{{ $nodo->longitud }}" target="_blank">
                                                Ver en Google Maps
                                            </a>
                                        </p>
                                        
                                    </div>
                                     <div class="card-footer">
                                     <button wire:click="editNodo({{ $nodo->id }})" class="btn btn-primary">Actualizar</button>
                                    {{-- <button wire:click="deletePlan({{ $plan->id }})" class="btn btn-danger">Eliminar</button> --}}
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
                            <form wire:submit.prevent="AgregarNodo">
                                <div class="form-group">
                                    <label for="nombre">Nombre</label>
                                    <input type="text" class="form-control" id="nombre" wire:model="nombre" required>
                                </div>
                                <div class="form-group">
                                    <label for="ip">ip</label>
                                    <input type="text" class="form-control" id="ip" wire:model="ip" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" required></input>
                                </div>
                                <div class="form-group">
                                    <label for="precio">Latitud</label>
                                    <input type="number" class="form-control" id="latitud" wire:model="latitud" step="any" required>
                                </div>
                                <div class="form-group">
                                    <label for="velocidad_bajada">longitud</label>
                                    <input type="number" class="form-control" id="longitud" wire:model="longitud" step="any" required>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="velocidad_subida">puerto_api</label>
                                    <input type="number" class="form-control" id="puerto_api" wire:model="puerto_api" required>
                                </div> --}}
                                {{-- <div class="form-group">
                                    <label for="precio">User</label>
                                    <input type="text" class="form-control" id="user" wire:model="user" step="any" required>
                                </div>
                                <div class="form-group">
                                    <label for="velocidad_bajada">Contraseña</label>
                                    <input type="text" class="form-control" id="pass" wire:model="pass" step="any" required>
                                </div> --}}
                                 <div class="modal-footer">
                                    <button  type="submit" class="btn btn-primary">Agregar</button>
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
    
    
     
    <!-- Modal Editar Nodo -->
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
                    <form wire:submit.prevent="updateNodo">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" wire:model="nombre" required>
                        </div>
                        <div class="form-group">
                            <label for="ip">ip</label>
                            <input type="text" class="form-control" id="ip" wire:model="ip" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$" required></input>
                        </div>
                        <div class="form-group">
                            <label for="precio">Latitud</label>
                            <input type="number" class="form-control" id="latitud" wire:model="latitud" step="any" required>
                        </div>
                        <div class="form-group">
                            <label for="velocidad_bajada">longitud</label>
                            <input type="number" class="form-control" id="longitud" wire:model="longitud" step="any" required>
                        </div>
                         {{-- <div class="form-group">
                            <label for="precio">User</label>
                            <input type="text" class="form-control" id="user" wire:model="user" step="any" required>
                        </div>
                        <div class="form-group">
                            <label for="velocidad_bajada">Contraseña</label>
                            <input type="text" class="form-control" id="pass" wire:model="pass" step="any" required>
                        </div> --}}
                        
                        <div class="form-group">
                            <label for="velocidad_subida">puerto_api</label>
                            <input type="number" class="form-control" id="puerto_api" wire:model="puerto_api" required>
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
