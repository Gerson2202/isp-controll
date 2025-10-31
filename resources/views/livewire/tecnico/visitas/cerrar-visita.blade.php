<div class="container py-3">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-clipboard-check me-2"></i> Cerrar Visita #{{ $visita->id }}
        </div>

        <div class="card-body bg-light">
            {{-- Información del cliente --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    <h5 class="text-primary">{{ $visita->ticket->cliente->nombre }}</h5>
                    <p><strong>Teléfono:</strong> {{ $visita->ticket->cliente->telefono }}</p>
                    <p><strong>Situación:</strong> {{ $visita->ticket->situacion }}</p>
                </div>
            </div>

            {{-- Solución aplicada --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-primary">
                    <i class="bi bi-tools me-1"></i> Solución aplicada
                </label>
                <textarea wire:model="solucion" class="form-control shadow-sm" rows="3" required></textarea>
                @error('solucion')
                    <small class="text-danger">'{{ $message }}'</small>
                @enderror
            </div>

            {{-- Fotos --}}
            <div class="mb-4">
                <label class="form-label fw-semibold text-primary">
                    <i class="bi bi-camera me-1"></i> Fotos de la visita
                </label> <input type="file" wire:model="fotos" multiple class="form-control">

                {{-- Error si alguna imagen no es válida --}}
                @error('fotos.*')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror

                {{-- Error si supera el máximo permitido --}}
                @error('fotos')
                    <small class="text-danger d-block">{{ $message }}</small>
                @enderror
            </div>

            @if ($fotos)
                <div class="d-flex flex-wrap mt-2">
                    @foreach ($fotos as $foto)
                        <img src="{{ $foto->temporaryUrl() }}" class="m-1 rounded shadow-sm border border-light"
                            width="120">
                    @endforeach
                </div>
            @endif

            <hr class="my-4">

            {{-- Consumibles --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="bi bi-basket me-1"></i> Consumibles usados
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <select wire:model="consumibleSeleccionado" class="form-select shadow-sm">
                                <option value="">Seleccione consumible...</option>
                                @foreach ($consumibles as $item)
                                    <option value="{{ $item->consumible_id }}">
                                        {{ $item->consumible->nombre }}
                                        (Stock: {{ $item->cantidad }})
                                        (Und: {{ $item->consumible->unidad }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" wire:model="cantidadConsumible" class="form-control shadow-sm"
                                placeholder="Cantidad" min="1">
                        </div>
                        <div class="col-md-3">
                            <button wire:click="agregarConsumible" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Agregar
                            </button>
                        </div>
                    </div>

                    @if (count($consumiblesUsados) > 0)
                        <table class="table table-sm table-striped align-middle shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumiblesUsados as $index => $item)
                                    <tr>
                                        <td>{{ $item['nombre'] }}</td>
                                        <td>{{ $item['cantidad'] }}</td>
                                        <td class="text-end">
                                            <button wire:click="eliminarConsumible({{ $index }})"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Error de validación --}}
            @if (session('error'))
                <div class="alert alert-danger shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Inventarios --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning fw-bold text-dark">
                    <i class="bi bi-hdd-network me-1"></i> Equipos usados (Inventario)
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-2">
                        <div class="col-md-9">
                            <select wire:model="inventarioSeleccionado" class="form-select shadow-sm">
                                <option value="">Seleccione equipo...</option>
                                @foreach ($inventarios as $inv)
                                    <option value="{{ $inv->id }}">
                                        {{ $inv->modelo->nombre }} | MAC: {{ $inv->mac }} | SERIAL:
                                        {{ $inv->serial }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button wire:click="agregarInventario" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Agregar
                            </button>
                        </div>
                    </div>

                    @if (count($inventariosUsados) > 0)
                        <table class="table table-sm table-striped align-middle shadow-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Equipo</th>
                                    <th>Serial</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($inventariosUsados as $index => $item)
                                    <tr>
                                        <td>{{ $item['nombre'] }}</td>
                                        <td>{{ $item['serial'] }}</td>
                                        <td class="text-end">
                                            <button wire:click="eliminarInventario({{ $index }})"
                                                class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Botón final --}}
            <div class="text-end mt-4">
                <button wire:click="cerrarVisita" class="btn btn-success px-4 shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Cerrar visita
                </button>
            </div>
        </div>
    </div>
</div>
