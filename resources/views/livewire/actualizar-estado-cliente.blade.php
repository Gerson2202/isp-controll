<div>
    <div>
      
    
        <form wire:submit.prevent="actualizarEstado">
            <div class="form-group">
                <label for="estado">Estado del Cliente</label>
                <select class="form-control" wire:model="estado">
                    <option value="activo">Activo</option>
                    <option value="cortado">Cortado</option>
                </select>
                @error('estado') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
    
            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        </form>
    </div>
    
</div>
