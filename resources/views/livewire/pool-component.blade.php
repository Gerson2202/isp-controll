<div>
    <div>
        <!-- Mensaje de éxito -->
        @if($successMessage)
        <div class="alert alert-success alert-dismissible fade show" id="successMessage" role="alert">
            {{ $successMessage }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif  
    
        <!-- Card para Formulario de agregar pool -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Formulario para Crear Pool</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="store">
                    <div class="mb-3">
                        <label for="nodo_id" class="form-label">Nodo</label>
                        <select wire:model="nodo_id" id="nodo_id" class="form-control" required>
                            <option value="">Seleccione un nodo</option>
                            @foreach($nodos as $nodo)
                                <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('nodo_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Pool</label>
                        <input type="text" wire:model="nombre" id="nombre" class="form-control" required />
                        @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="start_ip" class="form-label">IP de inicio</label>
                        <input type="text" wire:model="start_ip" id="start_ip" class="form-control" required />
                        @error('start_ip') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="end_ip" class="form-label">IP final</label>
                        <input type="text" wire:model="end_ip" id="end_ip" class="form-control" required/>
                        @error('end_ip') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea wire:model="descripcion" id="descripcion" class="form-control" required></textarea>
                        @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
    
                    <button type="submit" class="btn btn-primary">Crear Pool</button>
                </form>
            </div>
        </div>
    
        <!-- Card para Listado de Pools -->
        <div class="card mt-4">
            <div class="card-header">
                <h5>Listado de Pools</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Nodo</th>
                            <th>IP de Inicio</th>
                            <th>IP Final</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pools as $pool)
                            <tr>
                                <td>{{ $pool->nombre }}</td>
                                <td>{{ $pool->nodo->nombre ?? 'no disponible'}}</td>
                                <td>{{ $pool->start_ip }}</td>
                                <td>{{ $pool->end_ip }}</td>
                                <td>{{ $pool->descripcion ?? 'sin descripcion' }}</td>
                                <td>
                                    <button wire:click="edit({{ $pool->id }})" class="btn btn-warning btn-sm">Editar</button>
                                    <button wire:click="delete({{ $pool->id }})" class="btn btn-danger btn-sm">Eliminar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    
        <!-- Modal para editar un pool -->
        <div class="modal fade @if($showModal) show @endif" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: @if($showModal) block @else none @endif;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPoolModalLabel">Editar Pool</h5>
                        <button wire:click="hide" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="update">
                            <div class="mb-3">
                                <label for="nodo_id" class="form-label">Nodo</label>
                                <select wire:model="nodo_id" id="nodo_id" class="form-control" required>
                                    <option value="">Seleccione un nodo </option>
                                    @foreach($nodos as $nodo)
                                        <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('nodo_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del Pool</label>
                                <input type="text" wire:model="nombre" id="nombre" class="form-control" required />
                                @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="start_ip" class="form-label">IP de inicio</label>
                                <input type="text" wire:model="start_ip" id="start_ip" class="form-control" required/>
                                @error('start_ip') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="end_ip" class="form-label">IP final</label>
                                <input type="text" wire:model="end_ip" id="end_ip" class="form-control" required />
                                @error('end_ip') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea wire:model="descripcion" id="descripcion" class="form-control" required></textarea>
                                @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
    
                            <button type="submit" class="btn btn-primary">Actualizar Pool</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
   
    
</div>
