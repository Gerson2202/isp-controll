<div>
    <div class="row">
        <!-- Card para el Formulario -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>{{ $isEdit ? 'Editar Modelo' : 'Agregar Modelo' }}</h4>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div id="message" class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="closeMessage()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
    
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Modelo</label>
                            <input type="text" class="form-control" id="nombre" wire:model="nombre">
                            @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
    
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" wire:model="foto">
                            @error('foto') <span class="text-danger">{{ $message }}</span> @enderror
    
                            @if ($foto)
                                <img src="{{ $foto->temporaryUrl() }}" class="mt-2" width="100">
                            @endif
                        </div>
    
                        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Actualizar' : 'Agregar' }} Modelo</button>
                    </form>
                </div>
            </div>
        </div>
    
        <!-- Card para los Modelos -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Modelos</h4>
                </div>
                <div class="card-body">
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table">
                            <thead>
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
                                                <img src="{{ asset('storage/' . $modelo->foto) }}" width="50" alt="Foto">
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $modelo->id }})">Editar</button>
                                            {{-- <button class="btn btn-danger btn-sm" wire:click="delete({{ $modelo->id }})">Eliminar</button> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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


