<div>
    <div class="card">
        <div class="card-header">
            <h4>Registrar nuevo equipo</h4>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success">
                    {{ session('message') }}
                </div>
            @endif
    
            <form wire:submit.prevent="guardar">
                <div class="mb-3">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" wire:model="modelo">
                    @error('modelo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
    
                <div class="mb-3">
                    <label for="mac" class="form-label">MAC Address</label>
                    <input type="text" class="form-control" id="mac" wire:model="mac">
                    @error('mac') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
    
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" wire:model="descripcion"></textarea>
                    @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
    
                <div class="mb-3">
                    <label for="foto" class="form-label">Foto</label>
                    <input type="file" class="form-control" id="foto" wire:model="foto">
                    @error('foto') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
    
                <button type="submit" class="btn btn-primary">Guardar Inventario</button>
            </form>
        </div>
    </div>
    
</div>
