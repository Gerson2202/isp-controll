<div class="container py-2">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="bi bi-clipboard-check me-2"></i> Cerrar Visita #{{ $visita->id }}
        </div>

        <div class="card-body bg-light">
            {{-- Información del cliente --}}
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-body">
                    @if ($visita->ticket?->cliente?->nombre)
                        <h5 class="text-primary">{{ $visita->ticket->cliente->nombre }}</h5>
                    @endif

                    @if ($visita->ticket?->tipo_reporte)
                        <p><strong>tipo de reporte:</strong> {{ $visita->ticket->tipo_reporte }}</p>
                    @endif

                    @if ($visita->ticket?->situacion)
                        <p><strong>Situación:</strong> {{ $visita->ticket->situacion }}</p>
                    @endif
                    <p><strong>Titulo de visita:</strong> {{ $visita->titulo ?? 'S/N' }}</p>
                    <p><strong>Descripcion de visita:</strong> {{ $visita->descripcion ?? 'S/N' }}</p>


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
                    <div class="row g-2 mb-2 align-items-end">
                        <div class="col-md-4">
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
                            <select wire:model="origenSeleccionado" class="form-select shadow-sm">
                                <option value="">Seleccione origen...</option>
                                <option value="usuario">Stock personal</option>
                                @foreach ($bodegas as $bodega)
                                    <option value="{{ $bodega->id }}">Bodega: {{ $bodega->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
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
                                    <th>Origen</th>
                                    <th>Cantidad</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($consumiblesUsados as $index => $item)
                                    <tr>
                                        <td>{{ $item['nombre'] }}</td>
                                        <td>
                                            {{ $item['origen'] === 'usuario' ? 'Stock personal' : 'Bodega: ' . ($item['bodega_nombre'] ?? 'N/A') }}
                                        </td>
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
                            {{-- Buscador --}}
                            <input type="text" wire:model.live.300ms="filtroInventario"
                                placeholder="🔍 Buscar por modelo, MAC, serial o bodega..."
                                class="form-control shadow-sm mb-2">

                            {{-- Contenedor con scroll horizontal --}}
                            <div class="border rounded shadow-sm bg-white"
                                style="max-height: 120px; overflow-y: auto; overflow-x: auto;">
                                <div class="list-group list-group-flush" style="min-width: 600px;">
                                    {{-- Ancho mínimo para scroll horizontal --}}
                                    {{-- Opción por defecto --}}
                                    <div class="list-group-item {{ !$inventarioSeleccionado ? 'active' : '' }}">
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="radio"
                                                wire:model="inventarioSeleccionado" value=""
                                                id="inventario_default">
                                            <label class="form-check-label w-100" for="inventario_default">
                                                <span class="text-muted">👆 Seleccione un equipo...</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Opciones de inventario --}}
                                    @foreach ($inventarios as $inv)
                                        @if (
                                            !$filtroInventario ||
                                                stripos($inv->modelo->nombre ?? '', $filtroInventario) !== false ||
                                                stripos($inv->mac ?? '', $filtroInventario) !== false ||
                                                stripos($inv->serial ?? '', $filtroInventario) !== false ||
                                                stripos($inv->bodega->nombre ?? '', $filtroInventario) !== false)
                                            <div
                                                class="list-group-item {{ $inventarioSeleccionado == $inv->id ? 'active' : '' }}">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="radio"
                                                        wire:model="inventarioSeleccionado" value="{{ $inv->id }}"
                                                        id="inventario_{{ $inv->id }}">
                                                    <label class="form-check-label w-100"
                                                        for="inventario_{{ $inv->id }}">
                                                        {{-- Contenido en columnas para mejor visualización --}}
                                                        <div class="row g-1 small">
                                                            <div class="col-12 col-sm-4">
                                                                <strong class="d-block text-truncate">
                                                                    🖥️ {{ $inv->modelo->nombre ?? 'Sin modelo' }}
                                                                </strong>
                                                            </div>
                                                            <div class="col-6 col-sm-3">
                                                                <span class="text-muted">MAC:</span>
                                                                <span class="d-block text-truncate font-monospace">
                                                                    {{ $inv->mac ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                            <div class="col-6 col-sm-3">
                                                                <span class="text-muted">SERIAL:</span>
                                                                <span class="d-block text-truncate font-monospace">
                                                                    {{ $inv->serial ?? 'N/A' }}
                                                                </span>
                                                            </div>
                                                            <div class="col-12 col-sm-2">
                                                                <span class="text-muted">📍</span>
                                                                <small class="d-block text-truncate">
                                                                    {{ $inv->bodega ? $inv->bodega->nombre : 'Personal' }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    {{-- Mensaje si no hay resultados --}}
                                    @if (
                                        $filtroInventario &&
                                            !collect($inventarios)->filter(function ($inv) use ($filtroInventario) {
                                                    return stripos($inv->modelo->nombre ?? '', $filtroInventario) !== false ||
                                                        stripos($inv->mac ?? '', $filtroInventario) !== false ||
                                                        stripos($inv->serial ?? '', $filtroInventario) !== false ||
                                                        stripos($inv->bodega->nombre ?? '', $filtroInventario) !== false;
                                                })->count())
                                        <div class="list-group-item text-center text-muted py-3">
                                            <i class="bi bi-search me-2"></i>
                                            No se encontraron equipos con "{{ $filtroInventario }}"
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Equipo seleccionado (resumen) --}}
                            @if ($inventarioSeleccionado)
                                @php
                                    $inventarioSeleccionadoObj = $inventarios->firstWhere(
                                        'id',
                                        $inventarioSeleccionado,
                                    );
                                @endphp
                                @if ($inventarioSeleccionadoObj)
                                    <div class="alert alert-success mt-2 p-2 small" role="alert">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>✅ Seleccionado:</strong>
                                                {{ $inventarioSeleccionadoObj->modelo->nombre ?? 'Sin modelo' }} |
                                                SERIAL: {{ $inventarioSeleccionadoObj->serial ?? 'N/A' }}
                                            </div>
                                            <button type="button" class="btn-close btn-sm"
                                                wire:click="$set('inventarioSeleccionado', '')"
                                                aria-label="Quitar selección"></button>
                                        </div>
                                    </div>
                                @endif
                            @endif
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

            @if ($visita->ticket)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-danger text-white fw-bold">
                        <i class="bi bi-arrow-left-right me-1"></i> Retiro de equipos del cliente
                    </div>
                    <div class="card-body">
                        <div class="row g-2 mb-2">
                            <div class="col-md-9">
                                <select wire:model.live="inventarioClienteSeleccionado" class="form-select shadow-sm">
                                    <option value="">Seleccione equipo a retirar...</option>
                                    @foreach ($inventariosCliente as $inv)
                                        <option value="{{ $inv->id }}">
                                            {{ $inv->modelo->nombre ?? 'Equipo' }} | MAC: {{ $inv->mac }} |
                                            SERIAL: {{ $inv->serial }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button wire:click="agregarRetiro" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-plus-circle"></i> Agregar retiro
                                </button>
                            </div>
                        </div>

                        @if (count($inventariosRetirados) > 0)
                            <table class="table table-sm table-striped align-middle shadow-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Serial</th>
                                        <th>Destino</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($inventariosRetirados as $index => $item)
                                        <tr>
                                            <td>{{ $item['nombre'] }}</td>
                                            <td>{{ $item['serial'] }}</td>
                                            <td>
                                                <select wire:model.live="destinoRetiro.{{ $item['id'] }}.tipo"
                                                    class="form-select form-select-sm">
                                                    <option value="">Seleccione destino...</option>
                                                    <option value="usuario">Para mí</option>
                                                    <option value="bodega">Enviar a bodega</option>
                                                </select>

                                                @if (isset($destinoRetiro[$item['id']]['tipo']) && $destinoRetiro[$item['id']]['tipo'] === 'bodega')
                                                    <select wire:model.live="destinoRetiro.{{ $item['id'] }}.id"
                                                        class="form-select form-select-sm mt-1">
                                                        <option value="">Seleccione bodega...</option>
                                                        @foreach (Auth::user()->bodegas as $bodega)
                                                            <option value="{{ $bodega->id }}">{{ $bodega->nombre }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <button wire:click="eliminarRetiro({{ $index }})"
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
            @endif


            {{-- Botón final --}}
            <div class="text-end mt-4">
                <button wire:click="cerrarVisita" class="btn btn-success px-4 shadow-sm">
                    <i class="fas fa-check-circle me-1"></i> Cerrar visita
                </button>
            </div>
        </div>
    </div>
</div>
