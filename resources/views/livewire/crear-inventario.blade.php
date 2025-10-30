<div class="container mt-2">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Formulario de registro</h5>
        </div>
        <div class="card-body">
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="mac" class="form-label">MAC *</label>
                            <input type="text" class="form-control @error('mac') is-invalid @enderror" id="mac"
                                wire:model.live="mac">
                            @error('mac')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="serial" class="form-label">Serial</label>
                            <input type="text" class="form-control" id="serial" wire:model.live="serial">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="modelo_id" class="form-label">Modelo *</label>
                            <select class="form-select @error('modelo_id') is-invalid @enderror" id="modelo_id"
                                wire:model.live="modelo_id">
                                <option value="">Seleccionar Modelo</option>
                                @foreach ($modelos as $modelo)
                                    <option value="{{ $modelo->id }}">{{ $modelo->nombre }}</option>
                                @endforeach
                            </select>
                            @error('modelo_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" wire:model.live="fecha">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción *</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" wire:model.live="descripcion"
                        rows="3"></textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Selector de tipo de asignación -->
                <div class="mb-3">
                    <label class="form-label">Asignar a *</label>
                    <select class="form-select @error('tipo_asignacion') is-invalid @enderror"
                        wire:model.live="tipo_asignacion">
                        <option value="">Seleccionar tipo</option>
                        <option value="bodega">Bodega</option>
                        <option value="usuario">Usuario</option>
                        <option value="nodo">Nodo</option>
                        <option value="cliente">Cliente</option>
                    </select>
                    @error('tipo_asignacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Búsquedas dinámicas según el tipo seleccionado -->
                @if ($tipo_asignacion === 'bodega')
                    <div class="mb-3">
                        <label class="form-label">Buscar Bodega *</label>
                        <input type="text" class="form-control @error('bodega_id') is-invalid @enderror"
                            wire:model.live="searchBodega" placeholder="Escribe para buscar bodegas...">
                        @error('bodega_id')
                            <div class="invalid-feedback">Debes seleccionar una bodega</div>
                        @enderror

                        @if ($searchBodega && count($bodegas) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($bodegas as $bodega)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio" wire:model.live="bodega_id"
                                            value="{{ $bodega->id }}" id="bodega_{{ $bodega->id }}">
                                        <label class="form-check-label" for="bodega_{{ $bodega->id }}">
                                            {{ $bodega->nombre }}
                                            @if ($bodega->direccion)
                                                <small class="text-muted"> - {{ $bodega->direccion }}</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($searchBodega && count($bodegas) === 0)
                            <div class="mt-2">
                                <small class="text-danger">No se encontraron bodegas con ese nombre.</small>
                            </div>
                        @endif

                        @if ($bodega_id)
                            @php $bodegaSeleccionada = \App\Models\Bodega::find($bodega_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Bodega seleccionada:</strong> {{ $bodegaSeleccionada->nombre }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($tipo_asignacion === 'usuario')
                    <div class="mb-3">
                        <label class="form-label">Buscar Usuario *</label>
                        <input type="text" class="form-control @error('user_id') is-invalid @enderror"
                            wire:model.live="searchUsuario" placeholder="Escribe para buscar usuarios...">
                        @error('user_id')
                            <div class="invalid-feedback">Debes seleccionar un usuario</div>
                        @enderror

                        @if ($searchUsuario && count($usuarios) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($usuarios as $usuario)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio" wire:model.live="user_id"
                                            value="{{ $usuario->id }}" id="user_{{ $usuario->id }}">
                                        <label class="form-check-label" for="user_{{ $usuario->id }}">
                                            {{ $usuario->name }}
                                            <small class="text-muted"> - {{ $usuario->email }}</small>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($searchUsuario && count($usuarios) === 0)
                            <div class="mt-2">
                                <small class="text-danger">No se encontraron usuarios con ese nombre.</small>
                            </div>
                        @endif

                        @if ($user_id)
                            @php $usuarioSeleccionado = \App\Models\User::find($user_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Usuario seleccionado:</strong> {{ $usuarioSeleccionado->name }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($tipo_asignacion === 'nodo')
                    <div class="mb-3">
                        <label class="form-label">Buscar Nodo *</label>
                        <input type="text" class="form-control @error('nodo_id') is-invalid @enderror"
                            wire:model.live="searchNodo" placeholder="Escribe para buscar nodos...">
                        @error('nodo_id')
                            <div class="invalid-feedback">Debes seleccionar un nodo</div>
                        @enderror

                        @if ($searchNodo && count($nodos) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($nodos as $nodo)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio" wire:model.live="nodo_id"
                                            value="{{ $nodo->id }}" id="nodo_{{ $nodo->id }}">
                                        <label class="form-check-label" for="nodo_{{ $nodo->id }}">
                                            {{ $nodo->nombre }}
                                            @if ($nodo->ubicacion)
                                                <small class="text-muted"> - {{ $nodo->ubicacion }}</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($searchNodo && count($nodos) === 0)
                            <div class="mt-2">
                                <small class="text-danger">No se encontraron nodos con ese nombre.</small>
                            </div>
                        @endif

                        @if ($nodo_id)
                            @php $nodoSeleccionado = \App\Models\Nodo::find($nodo_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Nodo seleccionado:</strong> {{ $nodoSeleccionado->nombre }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($tipo_asignacion === 'cliente')
                    <div class="mb-3">
                        <label class="form-label">Buscar Cliente *</label>
                        <input type="text" class="form-control @error('cliente_id') is-invalid @enderror"
                            wire:model.live="searchCliente" placeholder="Escribe para buscar clientes...">
                        @error('cliente_id')
                            <div class="invalid-feedback">Debes seleccionar un cliente</div>
                        @enderror

                        @if ($searchCliente && count($clientes) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($clientes as $cliente)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio" wire:model.live="cliente_id"
                                            value="{{ $cliente->id }}" id="cliente_{{ $cliente->id }}">
                                        <label class="form-check-label" for="cliente_{{ $cliente->id }}">
                                            {{ $cliente->nombre }}
                                            @if ($cliente->telefono)
                                                <small class="text-muted"> - Tel: {{ $cliente->telefono }}</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @elseif($searchCliente && count($clientes) === 0)
                            <div class="mt-2">
                                <small class="text-danger">No se encontraron clientes con ese nombre.</small>
                            </div>
                        @endif

                        @if ($cliente_id)
                            @php $clienteSeleccionado = \App\Models\Cliente::find($cliente_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Cliente seleccionado:</strong>
                                    {{ $clienteSeleccionado->nombre }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Equipo
                    </button>
                </div>
            </form>
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
