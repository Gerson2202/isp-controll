<div>
    
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <form wire:submit.prevent="save" enctype="multipart/form-data" class="mb-4">
        <div class="mb-3">
            <label for="fotos" class="form-label">Selecciona una o varias fotos</label>
            <input type="file" id="fotos" wire:model="fotos" multiple accept="image/*" class="form-control @error('fotos.*') is-invalid @enderror">
            @error('fotos.*') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
        @if($fotos)
            <div class="row mb-3">
                @foreach($fotos as $index => $foto)
                    <div class="col-md-3 col-6 mb-2 text-center">
                        <div class="card">
                            <img src="{{ $foto->temporaryUrl() }}" class="card-img-top img-fluid rounded" alt="Previsualización">
                            <div class="card-body p-2">
                                <input type="text" wire:model="descripcion.{{ $index }}" class="form-control form-control-sm" placeholder="Descripción">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <button type="submit" class="btn btn-primary">Subir Imágenes</button>
    </form>

     <hr>
    <h6 class="mb-3">Imágenes guardadas:</h6>
    <div class="row">
        @forelse ($fotosGuardadas as $foto)
            <div class="col-md-3 col-6 mb-4 text-center">
                <div class="card h-100">
                    <img src="{{ asset('storage/' . $foto->ruta) }}" alt="Foto" class="card-img-top img-fluid rounded" style="max-height: 180px; object-fit: cover;">
                    <div class="card-body p-2">
                        <div class="small text-muted mb-1">{{ $foto->nombre_original }}</div>
                        <div class="small">{{ $foto->descripcion }}</div>
                        <button 
                            type="button"
                            class="btn btn-danger btn-sm mt-2"
                            onclick="confirmarEliminacion({{ $foto->id }})">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <span class="text-muted">No hay imágenes para esta visita.</span>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                @this.eliminarFoto(id);
            }
        });
    }
</script>
@endpush