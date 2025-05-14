<div>
    <div class="container-fluid min-vh-100 d-flex flex-column">
         <!-- Formulario para Crear Pool -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Crear Nuevo Pool</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="save">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nodo_id" class="form-label">Nodo</label>
                            <select wire:model="nodo_id" id="nodo_id" class="form-select" required>
                                <option value="">Seleccione un nodo</option>
                                @foreach($nodos as $nodo)
                                    <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('nodo_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre del Pool</label>
                            <input type="text" wire:model="nombre" id="nombre" class="form-control" required>
                            @error('nombre') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="start_ip" class="form-label">IP de inicio</label>
                            <input type="text" wire:model="start_ip" id="start_ip" class="form-control" placeholder="Ej: 192.168.1.1" required>
                            @error('start_ip') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="end_ip" class="form-label">IP final</label>
                            <input type="text" wire:model="end_ip" id="end_ip" class="form-control" placeholder="Ej: 192.168.1.254" required>
                            @error('end_ip') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-12">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea wire:model="descripcion" id="descripcion" class="form-control" rows="2"></textarea>
                            @error('descripcion') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    {{ $pool_id ? 'Actualizar Pool' : 'Guardar Pool' }}
                                </span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Procesando...
                                </span>
                            </button>

                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Listado de Pools -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Listado de Pools</h5>
            </div>
            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Nodo</th>
                                <th>Rango IP</th>
                                <th>Descripción</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pools as $pool)
                                <tr>
                                    <td>{{ $pool->nombre }}</td>
                                    <td>{{ $pool->nodo->nombre ?? 'N/A' }}</td>
                                    <td>{{ $pool->start_ip }} - {{ $pool->end_ip }}</td>
                                    <td>{{ $pool->descripcion ?: 'Sin descripción' }}</td>
                                    <td class="text-end">
                                        <button wire:click="edit({{ $pool->id }})" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $pool->id }})" class="btn btn-sm btn-danger" 
                                            wire:confirm="¿Está seguro de eliminar este pool?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No hay pools registrados</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

         @push('scripts')
            <script>
                document.addEventListener('livewire:init', () => {
                    Livewire.on('showModal', () => {
                        const modal = new bootstrap.Modal(document.getElementById('editModal'));
                        modal.show();
                    });
                    
                    Livewire.on('hideModal', () => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
                        modal?.hide();
                    });
                    
                    Livewire.on('notify', (data) => {
                        toastr[data.type](data.message);
                    });
                });
            </script>
         @endpush
    </div>   
</div>