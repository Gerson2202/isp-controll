<div>
    <!-- Mostrar mensaje de éxito si el ticket se crea correctamente -->
    @if($successMessage)
        <div class="alert alert-success alert-dismissible fade show" id="successMessage" role="alert">
             {{ $successMessage }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
   @endif

    <!-- Formulario para crear un ticket -->
    <form wire:submit.prevent="crearTicket">
        @csrf
        <!-- Tipo de reporte -->
        <div class="form-group">
            <label for="tipo_reporte">Tipo de reporte</label>
            <select wire:model="tipo_reporte" id="tipo_reporte" class="form-control" required>
                <option value="">Seleccione un tipo de reporte</option>
                <option value="Sin internet">Sin internet</option>
                <option value="Intermitencia">Intermitencia</option>
                <option value="Cableado estructurado">Cableado estructurado</option>
                <option value="Traslado">Traslado</option>
                <option value="Cambio de contraseña">Cambio de contraseña</option>
                <option value="Error en factura">Error en factura</option>
                <option value="Otros">Otros</option>
            </select>
            @error('tipo_reporte') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Situación -->
        <div class="form-group">
            <label for="situacion">Situación</label>
            <textarea wire:model="situacion" id="situacion" class="form-control" required></textarea>
            @error('situacion') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <!-- Estado -->
        <div class="form-group">
            <label for="estado">Estado</label>
            <input type="text" wire:model="estado" id="estado" class="form-control" readonly value="Abierto">
        </div>

        <button type="submit" class="btn btn-primary">Crear Ticket</button>
    </form>
</div>
