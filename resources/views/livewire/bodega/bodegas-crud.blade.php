<div>
    <!-- Encabezado mejorado -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-light rounded shadow-sm">
        <div>
            <h4 class="mb-0 text-muted ">
                <i class="fas fa-warehouse me-2 text-primary"></i>
                Administra las bodegas del sistema
            </h4>
        </div>
        <button wire:click="openModal" class="btn btn-primary d-flex align-items-center">
            <i class="fas fa-plus-circle me-2"></i>Nueva Bodega
        </button>
    </div>

    <!-- Tabla mejorada -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">
                                <i class="fas fa-signature me-2 text-muted"></i>Nombre
                            </th>
                            <th>
                                <i class="fas fa-tag me-2 text-muted"></i>Tipo
                            </th>
                            <th>
                                <i class="fas fa-map-marker-alt me-2 text-muted"></i>Ubicación
                            </th>
                            <th>
                                <i class="fas fa-align-left me-2 text-muted"></i>Descripción
                            </th>
                            <th><i class="fas fa-users me-2 text-muted"></i>Usuarios</th>

                            <th class="text-center pe-4">
                                <i class="fas fa-cogs me-2 text-muted"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bodegas as $bodega)
                            <tr class="border-bottom">
                                <td class="ps-4 fw-semibold">{{ $bodega->nombre }}</td>
                                <td>
                                    <span class="badge bg-primary rounded-pill">
                                        {{ ucfirst($bodega->tipo) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                        {{ $bodega->ubicacion }}
                                    </span>
                                </td>
                                <td>
                                    @if ($bodega->descripcion)
                                        <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                            title="{{ $bodega->descripcion }}">
                                            {{ $bodega->descripcion }}
                                        </span>
                                    @else
                                        <span class="text-muted fst-italic">Sin descripción</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($bodega->users->count())
                                        @foreach ($bodega->users as $user)
                                            <span class="badge bg-info text-dark">{{ $user->name }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted fst-italic">Sin usuarios</span>
                                    @endif
                                </td>

                                <td class="text-center pe-4">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button wire:click="edit({{ $bodega->id }})"
                                            class="btn btn-warning d-flex align-items-center" title="Editar bodega">
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                        {{-- <button wire:click="delete({{ $bodega->id }})" 
                                                class="btn btn-outline-danger d-flex align-items-center"
                                                title="Eliminar bodega">
                                            <i class="fas fa-trash-alt me-1"></i>Eliminar
                                        </button> --}}
                                        {{-- RUTA VIEJA --}}
                                        {{-- <a href="{{ route('bodega.show', $bodega->id) }}" 
                                        class="btn btn-primary d-flex align-items-center" 
                                        title="Ver Bodega">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a> --}}
                                        <a href="{{ route('inventario.detalle', ['bodega', $bodega->id]) }}"
                                            class="btn btn-primary d-flex align-items-center" title="Ver Bodega">
                                            <i class="fas fa-eye me-1"></i>Ver
                                        </a>
                                    </div>
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal mejorado -->
    <div class="modal fade @if ($modal) show d-block @endif" tabindex="-1" role="dialog"
        @if ($modal) style="background: rgba(0,0,0,0.5);" @endif>
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-warehouse me-2"></i>
                        {{ $bodega_id ? 'Editar Bodega' : 'Nueva Bodega' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-signature me-1 text-primary"></i>Nombre
                            </label>
                            <input type="text" wire:model="nombre" class="form-control"
                                placeholder="Ingrese el nombre de la bodega" required>
                            @error('nombre')
                                <div class="text-danger small mt-1">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i>Tipo
                            </label>
                            <select wire:model="tipo" class="form-select">
                                <option value="general">General</option>
                                <option value="vehiculo">Vehículo</option>
                                <option value="evento">Evento</option>
                                <option value="temporal">Temporal</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>Ubicación
                        </label>
                        <input type="text" wire:model="ubicacion" class="form-control"
                            placeholder="Ingrese la ubicación de la bodega">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left me-1 text-primary"></i>Descripción
                        </label>
                        <textarea wire:model="descripcion" class="form-control" rows="3" placeholder="Agregue una descripción opcional"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-users me-1 text-primary"></i>Usuarios asignados
                        </label>

                        @if (!empty($usuariosSeleccionados))
                            <div class="mb-2">
                                @foreach ($usuariosDisponibles->whereIn('id', $usuariosSeleccionados) as $user)
                                    <span class="badge bg-info text-dark me-1 mb-1">
                                        <i class="fas fa-user me-1"></i>{{ $user->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        <select wire:model="usuariosSeleccionados" class="form-select" multiple size="8">
                            @foreach ($usuariosDisponibles as $user)
                                <option value="{{ $user->id }}">
                                    👤 {{ $user->name }} — {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                </div>
                <div class="modal-footer border-0">
                    <button type="button" wire:click="closeModal"
                        class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" wire:click="save" class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
