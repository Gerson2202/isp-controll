<div class="card shadow-lg border-0">
    <!-- Header mejorado -->
    <div class="card-header bg-gradient-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">
                    <i class="fas fa-boxes me-2"></i>Gestión de Consumibles
                </h4>
                <p class="mb-0 opacity-75">Administra los consumibles y su inventario</p>
            </div>
            <div class="btn-group">
                <button wire:click="openModalConsumible" class="btn btn-light d-flex align-items-center">
                    <i class="fas fa-plus-circle me-2"></i>Nuevo Consumible
                </button>
                <button wire:click="openModalStock" class="btn btn-warning text-white d-flex align-items-center">
                    <i class="fas fa-layer-group me-2"></i>Agregar Stock
                </button>
            </div>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- Mensaje de éxito mejorado -->
        @if (session()->has('message'))
            <div class="alert alert-success d-flex align-items-center alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2 fs-5"></i>
                <div class="flex-grow-1">{{ session('message') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" wire:model.live="search" class="form-control"
                    placeholder="Buscar consumible por nombre...">
            </div>
        </div>

        <!-- Tabla mejorada -->
        <div class="table-responsive rounded-3 border" style="max-height: 800px; overflow-y: auto;">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3">
                            <i class="fas fa-cube me-2 text-primary"></i>Nombre
                        </th>
                        <th class="py-3">
                            <i class="fas fa-balance-scale me-2 text-primary"></i>Unidad
                        </th>
                        <th class="py-3">
                            <i class="fas fa-align-left me-2 text-primary"></i>Descripción
                            {{-- </th>
                        <th class="py-3">
                            <i class="fas fa-boxes me-2 text-primary"></i>Stock Total
                        </th> --}}
                        <th class="text-center pe-4 py-3">
                            <i class="fas fa-cogs me-2 text-primary"></i>Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consumibles as $c)
                        <tr class="border-bottom">
                            <td class="ps-4 fw-semibold align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary rounded-circle p-2 me-3">
                                        <i class="fas fa-cube text-white fs-6"></i>
                                    </div>
                                    {{ $c->nombre }}
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-secondary rounded-pill fs-6 px-3 py-2">
                                    {{ $c->unidad }}
                                </span>
                            </td>
                            <td class="align-middle">
                                @if ($c->descripcion)
                                    <span class="text-truncate d-inline-block" style="max-width: 200px;"
                                        title="{{ $c->descripcion }}">
                                        {{ $c->descripcion }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic">Sin descripción</span>
                                @endif
                            </td>
                            {{-- <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold fs-5 text-primary me-2">
                                        {{ $c->stocks->sum('cantidad') }}
                                    </span>
                                    <small class="text-muted">{{ $c->unidad }}</small>
                                </div>
                            </td> --}}
                            <td class="text-center pe-4 align-middle">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button wire:click="editConsumible({{ $c->id }})"
                                        class="btn btn-outline-primary d-flex align-items-center"
                                        title="Editar consumible">
                                        <i class="fas fa-edit me-1"></i>Editar
                                    </button>
                                    {{-- <button wire:click="deleteConsumible({{ $c->id }})" 
                                            class="btn btn-outline-danger d-flex align-items-center"
                                            title="Eliminar consumible">
                                        <i class="fas fa-trash me-1"></i>Eliminar
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Consumible  -->
    <div class="modal fade @if ($modalConsumible) show d-block @endif" tabindex="-1"
        @if ($modalConsumible) style="background: rgba(0,0,0,0.5);" @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-primary text-white py-3">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas {{ $consumible_id ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                        {{ $consumible_id ? 'Editar Consumible' : 'Nuevo Consumible' }}
                    </h5>
                    <button type="button" wire:click="closeModalConsumible" class="btn-close btn-close-white"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-signature me-1 text-primary"></i>Nombre del Consumible
                        </label>
                        <input type="text" wire:model="nombre" class="form-control form-control-lg"
                            placeholder="Ingrese el nombre del consumible" required>
                        @error('nombre')
                            <div class="text-danger small mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-balance-scale me-1 text-primary"></i>Unidad de Medida
                        </label>
                        <select wire:model="unidad" class="form-select form-select-lg" required>
                            <option value="">Seleccione una unidad</option>
                            <option value="metros">Metros</option>
                            <option value="kilometros">Kilómetros</option>
                            <option value="pies">ft</option>
                            <option value="rollo">Rollo</option>
                            <option value="rollo">Carreto</option>
                            <option value="rollo">paquete</option>
                            <option value="und">Und</option>
                            <option value="und">otro</option>
                        </select>
                        @error('unidad')
                            <div class="text-danger small mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left me-1 text-primary"></i>Descripción
                        </label>
                        <textarea wire:model="descripcion" class="form-control" rows="3"
                            placeholder="Agregue una descripción opcional del consumible"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" wire:click="closeModalConsumible"
                        class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" wire:click="saveConsumible"
                        class="btn btn-primary d-flex align-items-center px-4">
                        <i class="fas fa-save me-2"></i>Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Stock  -->
    <div class="modal fade @if ($modalStock) show d-block @endif" tabindex="-1"
        @if ($modalStock) style="background: rgba(0,0,0,0.5);" @endif>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-warning text-dark py-3">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-layer-group me-2"></i>Agregar Stock
                    </h5>
                    <button type="button" wire:click="closeModalStock" class="btn-close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-cube me-1 text-warning"></i>Consumible
                        </label>

                        <!-- Campo de búsqueda -->
                        <input type="text" wire:model.live="searchConsumible"
                            class="form-control form-control-lg @error('consumible_id') is-invalid @enderror"
                            placeholder="Buscar consumible...">

                        <!-- Resultados de búsqueda -->
                        @if (!empty($consumibles_filtrados))
                            <ul class="list-group position-absolute w-100 mt-1 shadow"
                                style="z-index: 10; max-height: 200px; overflow-y: auto;">
                                @foreach ($consumibles_filtrados as $consumible)
                                    <li wire:click="seleccionarConsumible({{ $consumible->id }})"
                                        class="list-group-item list-group-item-action" style="cursor:pointer;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>{{ $consumible->nombre }}</span>
                                            <span class="badge bg-secondary">{{ $consumible->unidad }}</span>
                                        </div>
                                        @if ($consumible->descripcion)
                                            <small
                                                class="text-muted">{{ \Illuminate\Support\Str::limit($consumible->descripcion, 50) }}</small>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @error('consumible_id')
                            <div class="text-danger small mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-hashtag me-1 text-warning"></i>Cantidad
                        </label>
                        <input type="number" wire:model="cantidad" class="form-control form-control-lg"
                            min="1" placeholder="Ingrese la cantidad">
                        @error('cantidad')
                            <div class="text-danger small mt-1">
                                <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt me-1 text-warning"></i>Tipo de Ubicación
                            </label>
                            <select wire:model.live="ubicacion_tipo" class="form-select">
                                <option value="">Seleccionar tipo</option>
                                <option value="bodega">Bodega</option>
                                <option value="cliente">Cliente</option>
                                <option value="nodo">Nodo</option>
                                <option value="usuario">Usuario</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-4">
                            @if ($ubicacion_tipo == 'cliente')
                                <!-- Buscador en vivo para clientes -->
                                <label class="form-label fw-semibold">Cliente</label>
                                <input type="text" wire:model.live="search_cliente" class="form-control"
                                    placeholder="Buscar cliente...">

                                @if (!empty($clientes_filtrados))
                                    <ul class="list-group position-absolute w-100 mt-1 shadow"
                                        style="z-index: 10; max-height: 200px; overflow-y: auto;">
                                        @foreach ($clientes_filtrados as $cliente)
                                            <li wire:click="seleccionarCliente({{ $cliente->id }})"
                                                class="list-group-item list-group-item-action"
                                                style="cursor:pointer;">
                                                {{ $cliente->nombre }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            @else
                                <!-- Selector tradicional para los demás -->
                                <label class="form-label fw-semibold">Ubicación Específica</label>
                                <select wire:model="ubicacion_id" class="form-select"
                                    {{ !$ubicacion_tipo ? 'disabled' : '' }}>
                                    <option value="">Seleccione ubicación</option>

                                    @if ($ubicacion_tipo == 'bodega')
                                        @foreach ($bodegas as $b)
                                            <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                                        @endforeach
                                    @elseif($ubicacion_tipo == 'nodo')
                                        @foreach ($nodos as $n)
                                            <option value="{{ $n->id }}">{{ $n->nombre }}</option>
                                        @endforeach
                                    @elseif($ubicacion_tipo == 'usuario')
                                        @foreach ($usuarios as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" wire:click="closeModalStock"
                        class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="button" wire:click="saveStock"
                        class="btn btn-warning text-white d-flex align-items-center px-4">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Stock
                    </button>
                </div>
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
