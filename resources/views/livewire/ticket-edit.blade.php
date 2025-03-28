<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Editar Ticket</h4>
        </div>
        <div class="card-body">
            <!-- Mostrar mensaje de éxito si se actualizó correctamente -->
            @if($showMessage)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Ticket actualizado con éxito a <strong>cerrado</strong>!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Formulario para editar el ticket -->
            <form wire:submit.prevent="updateTicket">
                <div class="mb-3">
                    <label for="tipo_reporte" class="form-label">Tipo de Reporte</label>
                    <input type="text" class="form-control" id="tipo_reporte" wire:model="tipo_reporte" @if($isUpdated) disabled @endif required>
                    @error('tipo_reporte') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="situacion" class="form-label">Situación</label>
                    <input type="text" class="form-control" id="situacion" wire:model="situacion" @if($isUpdated) disabled @endif required>
                    @error('situacion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="solucion" class="form-label">Solución</label>
                    <textarea class="form-control" id="solucion" wire:model="solucion" @if($isUpdated) disabled @endif required></textarea>
                    @error('solucion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                @if(!$isUpdated)
                    <button type="submit" class="btn btn-success">Actualizar</button>
                @endif
            </form>

            <!-- Botón para agendar visita (Solo visible si no está actualizado) -->
            @if(!$isUpdated)
                <form wire:submit.prevent="agendarVisita" class="mt-3">
                    <button type="submit" class="btn btn-warning">Agendar Visita al Sitio</button>
                </form>
            @endif
        </div>
    </div>
</div>
