<div class="container mt-2">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Movimiento de Equipos</h5>
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
                <!-- Buscar Inventario -->
                <div class="mb-3">
                    <label class="form-label">Buscar Equipo</label>
                    <input type="text" class="form-control @error('inventario_id') is-invalid @enderror"
                        wire:model.live="searchInventario" placeholder="Buscar por MAC, Serial o Descripción...">
                    @error('inventario_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    @if ($searchInventario && count($inventarios) > 0)
                        <div class="mt-2 border rounded p-2 bg-light">
                            <small class="text-muted">Resultados de búsqueda:</small>
                            @foreach ($inventarios as $inventario)
                                <div class="form-check mt-1">
                                    <input class="form-check-input" type="radio" wire:model.live="inventario_id"
                                        value="{{ $inventario->id }}" id="inv_{{ $inventario->id }}">
                                    <label class="form-check-label" for="inv_{{ $inventario->id }}">
                                        <strong>MAC:</strong> {{ $inventario->mac }} |
                                        <strong>Serial:</strong> {{ $inventario->serial }}<br>
                                        <small>{{ $inventario->descripcion }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @elseif($searchInventario && count($inventarios) === 0)
                        <div class="mt-2">
                            <small class="text-danger">No se encontraron equipos.</small>
                        </div>
                    @endif
                </div>

                @if ($inventario_id)
                    @php
                        $inventarioSeleccionado = \App\Models\Inventario::with([
                            'bodega',
                            'user',
                            'nodo',
                            'cliente',
                        ])->find($inventario_id);
                    @endphp
                    <div class="alert alert-info">
                        <strong>Equipo seleccionado:</strong><br>
                        MAC: {{ $inventarioSeleccionado->mac }}<br>
                        Serial: {{ $inventarioSeleccionado->serial }}<br>
                        Descripción: {{ $inventarioSeleccionado->descripcion }}<br>
                        <strong>Ubicación actual:</strong>
                        @if ($inventarioSeleccionado->bodega)
                            Bodega: {{ $inventarioSeleccionado->bodega->nombre }}
                        @elseif($inventarioSeleccionado->user)
                            Usuario: {{ $inventarioSeleccionado->user->name }}
                        @elseif($inventarioSeleccionado->nodo)
                            Nodo: {{ $inventarioSeleccionado->nodo->nombre }}
                        @elseif($inventarioSeleccionado->cliente)
                            Cliente: {{ $inventarioSeleccionado->cliente->nombre }}
                        @else
                            Sin asignar
                        @endif
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Tipo de Movimiento</label>
                    <select class="form-select" wire:model.live="tipo_movimiento">
                        <option value="traslado">Traslado</option>
                        {{-- <option value="asignacion">Asignación</option>
                        <option value="entrada">Entrada</option>
                        <option value="salida">Salida</option> --}}
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nueva Ubicación</label>
                    <select class="form-select @error('nuevo_tipo_asignacion') is-invalid @enderror"
                        wire:model.live="nuevo_tipo_asignacion">
                        <option value="">Seleccionar tipo</option>
                        <option value="bodega">Bodega</option>
                        <option value="usuario">Usuario</option>
                        <option value="nodo">Nodo</option>
                        <option value="cliente">Cliente</option>
                    </select>
                    @error('nuevo_tipo_asignacion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Búsquedas para nueva ubicación -->
                @if ($nuevo_tipo_asignacion === 'bodega')
                    <div class="mb-3">
                        <label class="form-label">Buscar Bodega</label>
                        <input type="text" class="form-control @error('nueva_bodega_id') is-invalid @enderror"
                            wire:model.live="searchBodega" placeholder="Escribe para buscar bodegas...">
                        @error('nueva_bodega_id')
                            <div class="invalid-feedback">Debes seleccionar una bodega</div>
                        @enderror

                        @if ($searchBodega && count($bodegas) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($bodegas as $bodega)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio" wire:model.live="nueva_bodega_id"
                                            value="{{ $bodega->id }}" id="bodega_nueva_{{ $bodega->id }}">
                                        <label class="form-check-label" for="bodega_nueva_{{ $bodega->id }}">
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

                        @if ($nueva_bodega_id)
                            @php $bodegaSeleccionada = \App\Models\Bodega::find($nueva_bodega_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Bodega seleccionada:</strong> {{ $bodegaSeleccionada->nombre }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($nuevo_tipo_asignacion === 'usuario')
                    <div class="mb-3">
                        <label class="form-label">Buscar Usuario</label>
                        <input type="text" class="form-control @error('nuevo_user_id') is-invalid @enderror"
                            wire:model.live="searchUsuario" placeholder="Escribe para buscar usuarios...">
                        @error('nuevo_user_id')
                            <div class="invalid-feedback">Debes seleccionar un usuario</div>
                        @enderror

                        @if ($searchUsuario && count($usuarios) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($usuarios as $usuario)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio" wire:model.live="nuevo_user_id"
                                            value="{{ $usuario->id }}" id="user_nuevo_{{ $usuario->id }}">
                                        <label class="form-check-label" for="user_nuevo_{{ $usuario->id }}">
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

                        @if ($nuevo_user_id)
                            @php $usuarioSeleccionado = \App\Models\User::find($nuevo_user_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Usuario seleccionado:</strong> {{ $usuarioSeleccionado->name }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($nuevo_tipo_asignacion === 'nodo')
                    <div class="mb-3">
                        <label class="form-label">Buscar Nodo</label>
                        <input type="text" class="form-control @error('nuevo_nodo_id') is-invalid @enderror"
                            wire:model.live="searchNodo" placeholder="Escribe para buscar nodos...">
                        @error('nuevo_nodo_id')
                            <div class="invalid-feedback">Debes seleccionar un nodo</div>
                        @enderror

                        @if ($searchNodo && count($nodos) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($nodos as $nodo)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio"
                                            wire:model.live="nuevo_nodo_id" value="{{ $nodo->id }}"
                                            id="nodo_nuevo_{{ $nodo->id }}">
                                        <label class="form-check-label" for="nodo_nuevo_{{ $nodo->id }}">
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

                        @if ($nuevo_nodo_id)
                            @php $nodoSeleccionado = \App\Models\Nodo::find($nuevo_nodo_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Nodo seleccionado:</strong> {{ $nodoSeleccionado->nombre }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($nuevo_tipo_asignacion === 'cliente')
                    <div class="mb-3">
                        <label class="form-label">Buscar Cliente</label>
                        <input type="text" class="form-control @error('nuevo_cliente_id') is-invalid @enderror"
                            wire:model.live="searchCliente" placeholder="Escribe para buscar clientes...">
                        @error('nuevo_cliente_id')
                            <div class="invalid-feedback">Debes seleccionar un cliente</div>
                        @enderror

                        @if ($searchCliente && count($clientes) > 0)
                            <div class="mt-2 border rounded p-2 bg-light">
                                <small class="text-muted">Resultados de búsqueda:</small>
                                @foreach ($clientes as $cliente)
                                    <div class="form-check mt-1">
                                        <input class="form-check-input" type="radio"
                                            wire:model.live="nuevo_cliente_id" value="{{ $cliente->id }}"
                                            id="cliente_nuevo_{{ $cliente->id }}">
                                        <label class="form-check-label" for="cliente_nuevo_{{ $cliente->id }}">
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

                        @if ($nuevo_cliente_id)
                            @php $clienteSeleccionado = \App\Models\Cliente::find($nuevo_cliente_id); @endphp
                            <div class="mt-2 alert alert-success py-2">
                                <small><strong>Cliente seleccionado:</strong>
                                    {{ $clienteSeleccionado->nombre }}</small>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mb-3">
                    <label class="form-label">Descripción</label>
                    <textarea class="form-control @error('descripcion') is-invalid @enderror" wire:model.live="descripcion"
                        rows="3" placeholder="Describe el motivo del movimiento..."></textarea>
                    @error('descripcion')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" {{ !$inventario_id ? 'disabled' : '' }}>
                    <i class="fas fa-exchange-alt"></i> Registrar Movimiento
                </button>
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
