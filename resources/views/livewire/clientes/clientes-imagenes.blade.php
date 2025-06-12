<div class="container-fluid">
    <!-- Toast Notification - Ahora dentro del contenedor principal -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="livewireToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-primary text-white">
                <strong class="me-auto">Sistema</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    <!-- Alertas y mensajes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @error('limite')
        <div class="alert alert-danger alert-dismissible fade show">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @enderror

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-camera me-2"></i>
                Galería de Fotos del Cliente :
                <a href="{{ route('clientes.show', $cliente->id) }}" class="text-decoration-none text-black fw-bold">
                    {{ $cliente->nombre }}
                </a>
            </h5>

            <span class="badge bg-light text-primary fs-6">
                {{ count($fotosSubidas) }}/{{ $maxFotos }} fotos
            </span>
        </div>
        
        <div class="card-body">
            <!-- Sección de fotos existentes -->
            @if(count($fotosSubidas) > 0)
                <div class="mb-4">
                    <h6 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-images me-2"></i>Fotos actuales
                    </h6>
                    <div class="row g-3">
                       @foreach($fotosSubidas as $foto)
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="position-relative">
                                        <a href="{{ asset('storage/' . $foto['ruta']) }}" target="_blank" class="d-block">
                                            <img src="{{ asset('storage/' . $foto['ruta']) }}" 
                                                class="card-img-top img-fluid rounded-top" 
                                                alt="{{ $foto['nombre_original'] }}"
                                                style="height: 180px; object-fit: cover; cursor: pointer;">
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted mb-1">
                                            <i class="fas fa-file-image me-1"></i>
                                            {{ Str::limit($foto['nombre_original'], 20) }}
                                        </p>
                                        <textarea wire:model.defer="descripciones.{{ $foto['id'] }}" 
                                                class="form-control form-control-sm mb-2" 
                                                rows="2"
                                                placeholder="Agregar descripción..."></textarea>
                                    </div>
                                    <div class="card-footer bg-white border-0 pt-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                Subida: {{ \Carbon\Carbon::parse($foto['created_at'])->format('d/m/Y') }}
                                            </small>
                                            <div class="btn-group" role="group">
                                                <button wire:click="guardarDescripcion({{ $foto['id'] }})" 
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Guardar descripción">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirmarEliminacion({{ $foto['id'] }})"
                                                        title="Eliminar imagen">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Este cliente no tiene fotos aún.
                </div>
            @endif

            <!-- Sección para subir nuevas fotos -->
            <div class="border-top pt-4 mt-4">
                <h6 class="border-bottom pb-2 mb-3">
                    <i class="fas fa-cloud-upload-alt me-2"></i>Subir nuevas fotos
                </h6>

                <!-- Previsualización de fotos temporales -->
                @if(count($fotosTemporales) > 0)
                    <div class="mb-4">
                        <p class="text-muted mb-2">Fotos a subir:</p>
                        <div class="row g-2">
                            @foreach($fotosTemporales as $index => $fotoTemp)
                                <div class="col-6 col-md-4 col-lg-3">
                                    <div class="card border-0 shadow-sm">
                                        <img src="{{ $fotoTemp->temporaryUrl() }}" 
                                             class="card-img-top" 
                                             style="height: 120px; object-fit: cover;">
                                        <div class="card-footer bg-white p-2 d-flex justify-content-end">
                                            <button wire:click="eliminarTemporal({{ $index }})" 
                                                    class="btn btn-outline-danger btn-sm"
                                                    title="Quitar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Formulario de subida -->
                <div class="mb-3">
                    <label for="fotos" class="form-label">
                        <i class="fas fa-file-image me-1"></i>Seleccionar fotos
                    </label>
                    <input type="file" 
                           wire:model="fotos" 
                           id="fotos" 
                           class="form-control" 
                           multiple
                           accept="image/*"
                           @if(count($fotosSubidas) >= $maxFotos) disabled @endif>
                    <div class="form-text">
                        Puedes seleccionar múltiples fotos. Formatos: JPG, PNG, etc. (Máx. 2MB cada una)
                    </div>
                </div>

                <!-- Contadores -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        @if(count($fotosSubidas) + count($fotosTemporales) > 0)
                            <span class="badge bg-primary me-2">
                                Actuales: {{ count($fotosSubidas) }}
                            </span>
                            <span class="badge bg-warning text-dark me-2">
                                Nuevas: {{ count($fotosTemporales) }}
                            </span>
                            <span class="badge bg-secondary">
                                Total: {{ count($fotosSubidas) + count($fotosTemporales) }}/{{ $maxFotos }}
                            </span>
                        @endif
                    </div>
                    @if(count($fotosSubidas) >= $maxFotos)
                        <span class="text-danger small">
                            <i class="fas fa-exclamation-triangle"></i> Límite alcanzado
                        </span>
                    @endif
                </div>

                <!-- Botones de acción -->
                <div class="d-flex justify-content-end gap-2">
                    <button wire:click="$refresh" 
                            class="btn btn-outline-secondary"
                            title="Recargar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button wire:click="guardarFotos" 
                            class="btn btn-primary"
                            @if(count($fotosSubidas) + count($fotosTemporales) === 0) disabled @endif>
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    textarea {
        resize: none;
    }
</style>
@endpush

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

<script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (data) => {
                toastr[data.type](data.message);
            });
        });
  </script>
    

@endpush