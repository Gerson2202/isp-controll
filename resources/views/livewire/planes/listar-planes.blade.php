<div>


    <div>
        <!-- Filtro por nodos funcional -->
        <div class="mb-3">
            <label for="nodoFilter" class="form-label">Filtrar por nodo:</label>
            <select class="form-select" id="nodoFilter" wire:model.live="nodo_id_Filtro">
                <option value="">Todos los nodos</option>
                @foreach ($nodos as $nodo)
                    <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                @endforeach
            </select>
        </div>

        <!-- Tabla con scroll -->
        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-hover table-striped">
                <thead class="sticky-top bg-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Velocidad</th>
                        <th>Rehuso</th>
                        <th>Nodo</th>
                        <th>Descripción</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($filteredPlans as $plan)
                        <tr>
                            <td><strong>{{ $plan->nombre }}</strong></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-download me-1"></i>{{ $plan->velocidad_bajada }} Mbps
                                    </span>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-upload me-1"></i>{{ $plan->velocidad_subida }} Mbps
                                    </span>
                                </div>
                            </td>
                            <td><span class="badge bg-info">{{ $plan->rehuso }}</span></td>
                            <td>
                                <span
                                    class="badge bg-secondary">{{ $plan->nodo ? $plan->nodo->nombre : 'Sin nodo' }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ Str::limit($plan->descripcion, 40) }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <button wire:click="editPlan({{ $plan->id }})" class="btn btn-outline-primary"
                                        title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button x-data
                                        @click.prevent="if (confirm('¿Estás seguro de eliminar este plan?')) { $wire.deletePlan({{ $plan->id }}) }"
                                        class="btn btn-outline-danger" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @if ($plan->nodo)
                                        <button wire:click="activatePlan({{ $plan->id }})"
                                            wire:loading.attr="disabled"
                                            class="btn btn-sm btn-{{ $currentPlanActivating == $plan->id ? 'warning' : 'success' }}"
                                            title="Activar">
                                            @if ($currentPlanActivating == $plan->id)
                                                <span class="spinner-border spinner-border-sm" role="status"></span>
                                            @else
                                                <i class="fas fa-power-off"></i>
                                            @endif
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-secondary" disabled
                                            title="Sin nodo asignado">
                                            <i class="fas fa-exclamation-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <!-- Modal Editar Planes -->
    <div class="modal fade @if ($showModal) show @endif" tabindex="-1"
        aria-labelledby="exampleModalLabel" aria-hidden="true"
        style="display: @if ($showModal) block @else none @endif;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Encabezado del Modal -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <i class="fas fa-edit me-2"></i>Editar Plan
                    </h5>
                    <button wire:click="hide" type="button" class="close text-white" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Cuerpo del Modal -->
                <div class="modal-body">
                    <form wire:submit.prevent="updatePlan">
                        <!-- Campos del formulario -->
                        <div class="row">
                            <!-- Columna Izquierda -->
                            <div class="col-md-6">
                                <!-- Nombre -->
                                <div class="form-group mb-3">
                                    <label for="nombre" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Nombre
                                    </label>
                                    <input type="text" class="form-control shadow-sm" id="nombre"
                                        wire:model="nombre">
                                </div>

                                <!-- Descripción -->
                                <div class="form-group mb-3">
                                    <label for="descripcion" class="form-label">
                                        <i class="fas fa-align-left me-1"></i>Descripción
                                    </label>
                                    <textarea class="form-control shadow-sm" id="descripcion" wire:model="descripcion" rows="3"></textarea>
                                </div>

                                <!-- Rehuso -->
                                <div class="form-group mb-3">
                                    <label for="rehuso" class="form-label">
                                        <i class="fas fa-exchange-alt me-1"></i>Rehuso
                                    </label>
                                    <select class="form-control shadow-sm" id="rehuso" wire:model="rehuso">
                                        <option value="">Seleccione un rehuso</option>
                                        <option value="1:1">1:1</option>
                                        <option value="1:2">1:2</option>
                                        <option value="1:4">1:4</option>
                                        <option value="1:6">1:6</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Columna Derecha -->
                            <div class="col-md-6">
                                <!-- Velocidad de Bajada -->
                                <div class="form-group mb-3">
                                    <label for="velocidad_bajada" class="form-label">
                                        <i class="fas fa-download me-1"></i>Velocidad de bajada
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control shadow-sm" id="velocidad_bajada"
                                            wire:model="velocidad_bajada" min="0">
                                        <span class="input-group-text">Mbps</span>
                                    </div>
                                </div>

                                <!-- Velocidad de Subida -->
                                <div class="form-group mb-3">
                                    <label for="velocidad_subida" class="form-label">
                                        <i class="fas fa-upload me-1"></i>Velocidad de subida
                                    </label>
                                    <div class="input-group">
                                        <input type="number" class="form-control shadow-sm" id="velocidad_subida"
                                            wire:model="velocidad_subida" min="0">
                                        <span class="input-group-text">Mbps</span>
                                    </div>
                                </div>

                                <!-- Nodo -->
                                <div class="form-group mb-3">
                                    <label for="nodo_id" class="form-label">
                                        <i class="fas fa-server me-1"></i>Nodo
                                    </label>
                                    <select class="form-control shadow-sm" id="nodo_id" wire:model="nodo_id"
                                        required @if ($planHasContracts) disabled @endif>
                                        <option value="">Seleccione un nodo</option>
                                        @foreach ($nodos as $nodo)
                                            <option value="{{ $nodo->id }}">{{ $nodo->nombre }}</option>
                                        @endforeach
                                    </select>

                                    @if ($planHasContracts)
                                        <div class="alert alert-warning alert-sm mt-2">
                                            <i class="fas fa-lock me-1"></i> No se puede modificar el nodo porque el
                                            plan
                                            tiene contratos asociados
                                        </div>
                                    @endif

                                </div>
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
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $burst_time_bajada }}" readonly>
                                </div>

                                <div class="col-6">
                                    <label>Burst Time Subida</label>
                                    <input type="text" class="form-control bg-light"
                                        value="{{ $burst_time_subida }}" readonly>
                                </div>
                            </div>


                        </div>

                        <!-- Pie del Modal -->
                        <div class="modal-footer">
                            <button wire:click="hide" type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </button>
                            <button type="submit"
                                onclick="return confirm('¿Estás seguro de que deseas actualizar el plan?')"
                                wire:loading.attr="disabled" wire:target="updatePlan" class="btn btn-primary">
                                <span wire:loading.remove wire:target="updatePlan">
                                    <i class="fas fa-save me-1"></i>Actualizar
                                </span>
                                <span wire:loading wire:target="updatePlan">
                                    <i class="fas fa-spinner fa-spin me-1"></i>Procesando...
                                </span>
                            </button>

                        </div>
                        <!-- Deshabilitar formulario durante carga -->
                        <div wire:loading.class="pe-none" wire:target="updatePlan">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Para manejo de Notificaciones Tosatar -->
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });
            });
        </script>
    @endpush
</div>
