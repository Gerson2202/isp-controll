<div class="card mx-auto border-0 shadow-sm" style="max-width: 900px;">
    <div class="card-header bg-light">
        <h5 class="mb-0 text-primary">
            <i class="fas fa-plus-circle me-2"></i>Crear Nuevo Plan
        </h5>
    </div>

    <div class="card-body">

        @if (session()->has('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="submitPlan">
            <div class="row">

                <!-- COLUMNA IZQUIERDA -->
                <div class="col-md-6">

                    <div class="mb-3">
                        <label>Nombre</label>
                        <input type="text" class="form-control" wire:model="nombre" required>
                    </div>

                    <div class="mb-3">
                        <label>Descripción</label>
                        <textarea class="form-control" wire:model="descripcion" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label>Bajada</label>
                            <input type="number" class="form-control" wire:model="velocidad_bajada" required>
                        </div>
                        <div class="col-6">
                            <label>Subida</label>
                            <input type="number" class="form-control" wire:model="velocidad_subida" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label>Rehuso</label>
                        <select class="form-control" wire:model="rehuso" required>
                            <option value="">Seleccione</option>
                            <option>1:1</option>
                            <option>1:2</option>
                            <option>1:4</option>
                            <option>1:6</option>
                        </select>
                    </div>

                    <div class="mt-3">
                        <label>Nodo</label>
                        <select class="form-control" wire:model="nodo_id" required>
                            <option value="">Seleccione</option>
                            @foreach ($nodos as $nodo)
                                <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <!-- COLUMNA DERECHA -->
                <div class="col-md-6 border-start">

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" wire:model.live="usar_rafaga">
                        <label class="form-check-label">Activar Ráfaga</label>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <label>Ráfaga Bajada</label>
                            <input type="number" class="form-control" wire:model.live="rafaga_max_bajada"
                                @disabled(!$usar_rafaga)>
                        </div>
                        <div class="col-6">
                            <label>Ráfaga Subida</label>
                            <input type="number" class="form-control" wire:model.live="rafaga_max_subida"
                                @disabled(!$usar_rafaga)>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <label>Velocidad media bajada</label>
                            <input type="number" class="form-control" wire:model="velocidad_media_bajada"
                                @disabled(!$usar_rafaga)>
                        </div>
                        <div class="col-6">
                            <label>Velocidad media subida</label>
                            <input type="number" class="form-control" wire:model="velocidad_media_subida"
                                @disabled(!$usar_rafaga)>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-6">
                            <label>Tiempo Bajada (s)</label>
                            <input type="number" class="form-control" wire:model.live="tiempo_input_bajada"
                                min="1" @disabled(!$usar_rafaga)>
                        </div>

                        <div class="col-6">
                            <label>Tiempo Subida (s)</label>
                            <input type="number" class="form-control" wire:model.live="tiempo_input_subida"
                                min="1" @disabled(!$usar_rafaga)>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-6">
                            <label>Burst Time Bajada</label>
                            <input type="text" class="form-control bg-light" value="{{ $burst_time_bajada }}"
                                readonly>
                        </div>

                        <div class="col-6">
                            <label>Burst Time Subida</label>
                            <input type="text" class="form-control bg-light" value="{{ $burst_time_subida }}"
                                readonly>
                        </div>
                    </div>


                </div>
            </div>

            <hr>

            <button class="btn btn-primary w-100">
                <i class="fas fa-save me-2"></i>Guardar Plan
            </button>
        </form>
    </div>
</div>
