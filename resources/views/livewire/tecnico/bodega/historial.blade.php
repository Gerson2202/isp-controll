<div class="mt-3">
    <h4 class="mt-4 mb-3"><i class="fas fa-history me-2"></i>Historial de movimientos</h4>

    {{-- =================== TABLA EQUIPOS =================== --}}
    <h5 class="mt-3"><i class="fas fa-server me-2"></i>Equipos</h5>

    {{-- Buscador Equipos --}}
    <div class="mb-3">
        <input type="text" wire:model.live="buscarEquipos" class="form-control"
            placeholder="Buscar por modelo, MAC o serial...">
    </div>

    <div class="table-responsive mb-4">
        <table class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Equipo</th>
                    <th>Mac</th>
                    <th>Serial</th>
                    <th>Tipo Movimiento</th>
                    <th>De</th>
                    <th>Para</th>
                    <th>Realizado por</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientosInventario as $mov)
                    <tr>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $mov->inventario->modelo->nombre ?? 'N/A' }}</td>
                        <td>{{ $mov->inventario->mac ?? 'N/A' }}</td>
                        <td>{{ $mov->inventario->serial ?? 'N/A' }}</td>
                        <td>
                            <span
                                class="badge 
                                @if ($mov->tipo_movimiento == 'entrada') bg-success
                                @elseif($mov->tipo_movimiento == 'salida') bg-danger
                                @elseif($mov->tipo_movimiento == 'traslado') bg-warning text-dark
                                @else bg-primary @endif">
                                {{ ucfirst($mov->tipo_movimiento) }}
                            </span>
                        </td>
                        <td>
                            @if ($mov->userAnterior)
                                Usuario: {{ $mov->userAnterior->name }}
                            @elseif($mov->bodegaAnterior)
                                Bodega: {{ $mov->bodegaAnterior->nombre }}
                            @elseif($mov->clienteAnterior)
                                Cliente: {{ $mov->clienteAnterior->nombre }}
                            @elseif($mov->nodoAnterior)
                                Nodo: {{ $mov->nodoAnterior->nombre }}
                            @elseif($mov->visitaAnterior)
                                Visita #{{ $mov->visitaAnterior->id }}
                            @else
                                Registro bodega
                            @endif
                        </td>
                        <td>
                            @if ($mov->userNuevo)
                                Usuario: {{ $mov->userNuevo->name }}
                            @elseif($mov->bodegaNueva)
                                Bodega: {{ $mov->bodegaNueva->nombre }}
                            @elseif($mov->clienteNuevo)
                                Cliente: {{ $mov->clienteNuevo->nombre }}
                            @elseif($mov->nodoNuevo)
                                Nodo: {{ $mov->nodoNuevo->nombre }}
                            @elseif($mov->visitaNueva)
                                Visita #{{ $mov->visitaNueva->id }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $mov->usuarioAccion->name ?? 'N/A' }}</td>
                        <td>{{ $mov->descripcion }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No se encontraron movimientos de equipos
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- Paginación Consumibles --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $movimientosInventario->onEachSide(1)->links('vendor.livewire.simple') }}
        </div>


    </div>

    {{-- =================== TABLA CONSUMIBLES =================== --}}
    <h5 class="mt-4"><i class="fas fa-box me-2"></i>Consumibles</h5>

    {{-- Buscador Consumibles --}}
    <div class="mb-3">
        <input type="text" wire:model.live="buscarConsumibles" class="form-control"
            placeholder="Buscar por nombre, descripción o unidad...">
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Consumible</th>
                    <th>Cantidad</th>
                    <th>Tipo</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Usuario</th>
                    <th>Descripción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientosConsumibles as $mov)
                    <tr>
                        <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $mov->consumible->nombre ?? 'N/A' }}</td>
                        <td>{{ $mov->cantidad }}</td>
                        <td>
                            <span
                                class="badge 
                                @if ($mov->tipo_movimiento == 'entrada') bg-success
                                @elseif($mov->tipo_movimiento == 'salida') bg-danger
                                @else bg-warning text-dark @endif">
                                {{ ucfirst($mov->tipo_movimiento) }}
                            </span>
                        </td>
                        <td>
                            @php $origen = $mov->origen(); @endphp
                            @if ($origen)
                                {{ ucfirst($mov->origen_tipo) }}:
                                {{ $origen->nombre ?? ($origen->name ?? 'N/A') }}
                            @else
                                Registro bodega
                            @endif
                        </td>
                        <td>
                            @php $destino = $mov->destino(); @endphp
                            @if ($destino)
                                {{ ucfirst($mov->destino_tipo) }}:
                                {{ $destino->nombre ?? ($destino->name ?? 'N/A') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $mov->usuario->name ?? 'N/A' }}</td>
                        <td>{{ $mov->descripcion ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">
                            No se encontraron movimientos de consumibles
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación Consumibles --}}
        <div class="mt-3 d-flex justify-content-center">
            {{ $movimientosConsumibles->onEachSide(1)->links('vendor.livewire.simple') }}
        </div>


    </div>
</div>
