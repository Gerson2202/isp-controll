<div class="container-fluid py-4">
    <div class="card">    
         <div class="card-header bg-primary text-white">
             <h5 class="card-title mb-0">Cambiar estado del cliente</h5>
         </div>
        <div class="card-body">
            <form wire:submit.prevent="actualizarEstado">
                <div class="mb-3">
                    <label>Estado actual:</label>
                    <span class="badge bg-{{ $cliente->estado == 'activo' ? 'success' : 'danger' }}">
                        {{ ucfirst($cliente->estado) }}
                    </span>
                </div>

                <div class="form-group mb-3">
                    <label for="estado">Nuevo Estado</label>
                    <select class="form-control" wire:model="estado" required>
                        <option value="">Seleccione un estado</option>
                        <option value="activo">Activo</option>
                        <option value="cortado">Cortado</option>
                    </select>
                    @error('estado') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading.class="invisible">
                            <i class="fas fa-sync-alt me-1"></i> Actualizar Estado
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Procesando...
                        </span>
                    </button>
                    
                    <div wire:loading wire:target="actualizarEstado">
                        <span class="text-muted small">Actualizando en router...</span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>