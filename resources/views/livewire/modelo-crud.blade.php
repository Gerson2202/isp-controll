<div>
    <div class="row">
        <!-- Card para el Formulario -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $isEdit ? 'Editar Modelo' : 'Agregar Modelo' }}</h4>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Modelo</label>
                            <input type="text" class="form-control" id="nombre" wire:model="nombre">
                            @error('nombre')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" wire:model="foto"
                                wire:loading.attr="disabled">

                            <!-- Icono de carga mientras se sube la imagen -->
                            <div wire:loading wire:target="foto" class="mt-2">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <span class="text-muted ms-2">Subiendo imagen...</span>
                            </div>

                            @error('foto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                            @if ($foto)
                                <div class="mt-2">
                                    <img src="{{ $foto->temporaryUrl() }}" class="img-thumbnail" width="100"
                                        alt="Vista previa">
                                    <small class="d-block text-muted">Vista previa</small>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled"
                                wire:target="save">
                                <span wire:loading.remove wire:target="save">
                                    {{ $isEdit ? 'Actualizar' : 'Agregar' }} Modelo
                                </span>
                                <span wire:loading wire:target="save">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Procesando...
                                </span>
                            </button>

                            @if ($isEdit)
                                <button type="button" class="btn btn-secondary" wire:click="resetInputFields">
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card para los Modelos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Modelos</h4>
                        <div class="w-50">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" placeholder="Buscar modelos..."
                                    wire:model.live="search">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Loading state durante la búsqueda -->
                    {{-- <div wire:loading wire:target="search" class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        <p class="text-muted mt-2">Buscando modelos...</p>
                    </div> --}}

                    <div wire:loading.remove wire:target="search">
                        @if ($modelos->count())
                            <div style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th>Foto</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($modelos as $modelo)
                                            <tr>
                                                <td>{{ $modelo->id }}</td>
                                                <td>{{ $modelo->nombre }}</td>
                                                <td>
                                                    @if ($modelo->foto)
                                                        <img src="{{ asset('storage/' . $modelo->foto) }}"
                                                            width="50" class="img-thumbnail"
                                                            alt="{{ $modelo->nombre }}">
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-warning"
                                                            wire:click="edit({{ $modelo->id }})"
                                                            wire:loading.attr="disabled">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-danger"
                                                            wire:click="delete({{ $modelo->id }})"
                                                            wire:confirm="¿Estás seguro de eliminar este modelo?"
                                                            wire:loading.attr="disabled">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            {{-- Paginación --}}
                            <div class="mt-4 d-flex justify-content-center">
                                {{ $modelos->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                                <p class="text-muted">
                                    @if ($search)
                                        No se encontraron modelos para "{{ $search }}"
                                    @else
                                        No hay modelos registrados
                                    @endif
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', (data) => {
                    if (typeof toastr !== 'undefined') {
                        toastr[data.type](data.message);
                    } else {
                        alert(data.message);
                    }
                });
            });
        </script>
    @endpush
</div>
