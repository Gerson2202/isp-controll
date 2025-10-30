<div class="container py-2">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Movimiento de consumibles  </h5>
        </div>

        <div class="card-body">
            {{-- Mensaje de éxito --}}
            @if (session()->has('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form wire:submit.prevent="realizarMovimiento" class="row g-3" wire:click.outside="cerrarResultados">
                {{-- Buscador de Consumibles --}}
                <div class="col-12 position-relative">
                    <label class="form-label fw-semibold">Buscar Consumible</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" wire:model.live="searchConsumible" class="form-control"
                            placeholder="Escriba el nombre del consumible..." autocomplete="off">
                    </div>

                    {{-- Dropdown de resultados consumibles --}}
                    @if ($mostrarResultadosConsumible && $searchConsumible && $consumibles->count() > 0)
                        <div class="border mt-1 rounded shadow-sm position-absolute w-100 bg-white"
                            style="z-index: 1050; max-height: 240px; overflow-y: auto;">
                            @foreach ($consumibles as $item)
                                <button type="button"
                                    class="list-group-item list-group-item-action border-0 text-start w-100"
                                    wire:click="selectConsumible({{ $item->id }}, '{{ addslashes($item->nombre) }}')">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $item->nombre }}</span>
                                        <small class="text-muted">ID: {{ $item->id }}</small>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    @endif

                    @error('consumible_id')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Sección Origen --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo de Origen</label>
                    <select wire:model.live="origen_tipo" class="form-select">
                        <option value="">Seleccione tipo...</option>
                        <option value="bodega">Bodega</option>
                        <option value="usuario">Usuario</option>
                        <option value="cliente">Cliente</option>
                        <option value="nodo">Nodo</option>
                    </select>
                    @error('origen_tipo')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Origen</label>

                    @if ($origen_tipo === 'cliente')
                        {{-- Buscador de Clientes para Origen --}}
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" wire:model.live="searchClienteOrigen" class="form-control"
                                    placeholder="Buscar cliente..." autocomplete="off">
                            </div>

                            {{-- Dropdown de resultados clientes origen --}}
                            @if ($mostrarResultadosClienteOrigen && $searchClienteOrigen && count($clientesOrigen) > 0)
                                <div class="border mt-1 rounded shadow-sm position-absolute w-100 bg-white"
                                    style="z-index: 1050; max-height: 240px; overflow-y: auto;">
                                    @foreach ($clientesOrigen as $cliente)
                                        <button type="button"
                                            class="list-group-item list-group-item-action border-0 text-start w-100"
                                            wire:click="selectClienteOrigen({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}')">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $cliente->nombre }}</span>
                                                <small class="text-muted">{{ $cliente->id ?? 'Sin código' }}</small>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <select wire:model="origen_id" class="form-select" {{ empty($origenes) ? 'disabled' : '' }}>
                            <option value="">Seleccione origen...</option>
                            @foreach ($origenes as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nombre ?? $item->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('origen_id')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Sección Destino --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo de Destino</label>
                    <select wire:model.live="destino_tipo" class="form-select">
                        <option value="">Seleccione tipo...</option>
                        <option value="bodega">Bodega</option>
                        <option value="usuario">Usuario</option>
                        <option value="cliente">Cliente</option>
                        <option value="nodo">Nodo</option>
                    </select>
                    @error('destino_tipo')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Destino</label>

                    @if ($destino_tipo === 'cliente')
                        {{-- Buscador de Clientes para Destino --}}
                        <div class="position-relative">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" wire:model.live="searchClienteDestino" class="form-control"
                                    placeholder="Buscar cliente..." autocomplete="off">
                            </div>

                            {{-- Dropdown de resultados clientes destino --}}
                            @if ($mostrarResultadosClienteDestino && $searchClienteDestino && count($clientesDestino) > 0)
                                <div class="border mt-1 rounded shadow-sm position-absolute w-100 bg-white"
                                    style="z-index: 1050; max-height: 240px; overflow-y: auto;">
                                    @foreach ($clientesDestino as $cliente)
                                        <button type="button"
                                            class="list-group-item list-group-item-action border-0 text-start w-100"
                                            wire:click="selectClienteDestino({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}')">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>{{ $cliente->nombre }}</span>
                                                <small class="text-muted">{{ $cliente->id ?? 'Sin código' }}</small>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        <select wire:model="destino_id" class="form-select" {{ empty($destinos) ? 'disabled' : '' }}>
                            <option value="">Seleccione destino...</option>
                            @foreach ($destinos as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->nombre ?? $item->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif

                    @error('destino_id')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Cantidad --}}
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Cantidad</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-box"></i>
                        </span>
                        <input type="number" wire:model="cantidad" min="1" class="form-control"
                            placeholder="Ingrese cantidad">
                    </div>
                    @error('cantidad')
                        <div class="text-danger small mt-1">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Botón de envío --}}
                <div class="col-12 text-end mt-4">
                    <button type="submit" class="btn btn-primary px-4" wire:loading.attr="disabled"
                        wire:target="realizarMovimiento">
                        <span wire:loading.remove wire:target="realizarMovimiento">
                            <i class="bi bi-box-arrow-right me-2"></i>Registrar Movimiento
                        </span>
                        <span wire:loading wire:target="realizarMovimiento">
                            <i class="bi bi-arrow-repeat spinner me-2"></i>Procesando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
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
