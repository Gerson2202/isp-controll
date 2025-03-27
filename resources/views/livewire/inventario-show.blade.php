<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Detalles del Equipo</h4>
            <button class="btn btn-primary" wire:click="mostrarModal">Editar / Asignar a Equipo</button>
        </div>
        <div class="card-body">
            @if($successMessage)
                <div class="alert alert-success d-flex justify-content-between">
                    {{ $successMessage }}
                    <button type="button" class="close text-success" wire:click="$set('successMessage', null)">&times;</button>
                </div>
            @endif
            @if($inventario)
                <p><strong>Modelo:</strong> {{ $inventario->modelo->nombre ?? 'N/A' }}</p>
                <p><strong>MAC Address:</strong> {{ $inventario->mac }}</p>
                <p><strong>Descripción:</strong> {{ $inventario->descripcion }}</p>
                <p><strong>Asignado a:</strong>
                    @if($inventario->cliente_id)
                        <span class="text-success">
                            Asignado a cliente: 
                            <a href="{{ route('clientes.show', $inventario->cliente_id) }}" class="text-success font-weight-bold">
                                {{ $inventario->cliente->nombre }}
                            </a>
                        </span>
                    @elseif($inventario->nodo_id)
                        <span class="text-primary">Asignado a nodo: {{ $inventario->nodo->nombre }}</span>
                    @else
                        <span class="text-danger">Equipo aún sin asignar</span>
                    @endif
                </p>
                
                @if (!empty($inventario->modelo->foto) && file_exists(public_path('storage/' . $inventario->modelo->foto)))
                    <div class="text mt-3">
                        <img src="{{ asset('storage/' . $inventario->modelo->foto) }}" alt="Foto del modelo" class="img-thumbnail" style="max-width: 150px;">
                    </div>
                @else
                    <p class="text-muted">No hay imagen disponible.</p>
                @endif
            @else
                <p class="text-danger">No se encontró el inventario.</p>
            @endif
        </div>
    
        <!-- Modal -->
        <div class="modal fade @if($modalVisible) show d-block @endif" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar / Asignar Inventario</h5>
                        <button type="button" class="close text-danger" wire:click="cerrarModal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if($errorMessage)
                            <div class="alert alert-danger d-flex justify-content-between">
                                {{ $errorMessage }}
                                <button type="button" class="close text-danger" wire:click="$set('errorMessage', null)">&times;</button>
                            </div>
                        @endif
                        <form wire:submit.prevent="guardar">
                            <div class="form-group">
                                <label for="modelo_id">Modelo</label>
                                <select id="modelo_id" class="form-control" wire:model="modelo_id">
                                    <option value="">Seleccionar Modelo</option>
                                    @foreach($modelos as $modelo)
                                        <option value="{{ $modelo->id }}">{{ $modelo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="mac">MAC Address</label>
                                <input type="text" id="mac" class="form-control" wire:model="mac">
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción</label>
                                <textarea id="descripcion" class="form-control" wire:model="descripcion"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="cliente_id">Asignar a Cliente</label>
                                <select id="cliente_id" class="form-control select2" wire:model="cliente_id">
                                    <option value="">Seleccionar Cliente</option>
                                    @if(!$nodo_id)
                                        <option value="" class="text-primary">Desvincular Cliente</option>
                                    @endif
                                    @foreach($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="nodo_id">Asignar a Nodo</label>
                                <select id="nodo_id" class="form-control" wire:model="nodo_id">
                                    <option value="">Seleccionar Nodo</option>
                                    @if(!$cliente_id)
                                        <option value="" class="text-primary">Desvincular Nodo</option>
                                    @endif
                                    @foreach($nodos as $nodo)
                                        <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </form>
                    </div>
                    
    @push('scripts')
<script>
    function initSelect2() {
        $('#cliente_id').select2({
            width: '100%'
        }).on('change', function (e) {
            var data = $(this).val();
            Livewire.emit('setCliente', data);
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        initSelect2();
    });

    document.addEventListener("livewire:load", function() {
        initSelect2();
    });

    document.addEventListener("livewire:updated", function() {
        initSelect2();
    });
</script>
@endpush

                    
                </div>
            </div>

        </div>
    </div>
    

    
</div>
