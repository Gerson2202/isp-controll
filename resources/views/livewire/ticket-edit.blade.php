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
            @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
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
                
                     <!-- Botón para abrir el modal -->
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#agendarModal">
                    Agendar Visita
                </button>
               
            @endif
        </div>
    </div>
    <div>
       
    
        <!-- Modal -->
        <div class="modal fade" id="agendarModal" tabindex="-1" aria-labelledby="agendarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agendarModalLabel">Agendar Visita</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="agendar">
                            <div class="mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio</label>
                                <input type="datetime-local" wire:model="fecha_inicio" class="form-control" id="fecha_inicio" required>
                                @error('fecha_inicio') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="fecha_cierre" class="form-label">Fecha de Cierre</label>
                                <input type="datetime-local" wire:model="fecha_cierre" class="form-control" id="fecha_cierre" required>
                                @error('fecha_cierre') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <input type="text" wire:model="descripcion" class="form-control" id="descripcion">
                                @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                           
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Registrar Visita</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</div>
