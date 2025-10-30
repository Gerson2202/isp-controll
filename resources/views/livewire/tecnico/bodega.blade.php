<div class="card shadow-sm rounded-0">
    <div class="card-header bg-primary text-white rounded-0">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0"><i class="fas fa-warehouse me-2"></i> Mi Bodega</h3>
            <a href="{{ route('tecnico.index') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="card-body p-4">
        <!-- Pestañas -->
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link @if ($activeTab === 'equipos') active @endif"
                    wire:click="setActiveTab('equipos')" type="button" role="tab">
                    <i class="fas fa-network-wired me-1"></i> Equipos
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link @if ($activeTab === 'consumibles') active @endif"
                    wire:click="setActiveTab('consumibles')" type="button" role="tab">
                    <i class="fas fa-boxes me-1"></i> Consumibles
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link @if ($activeTab === 'movimientos') active @endif"
                    wire:click="setActiveTab('movimientos')" type="button" role="tab">
                    <i class="fas fa-history me-1"></i> Historial de Movimientos
                </button>
            </li>
        </ul>


        <div class="tab-content" id="myTabContent">
            <!-- Pestaña de Equipos -->
            <div class="tab-pane fade @if ($activeTab === 'equipos') show active @endif" id="equipos"
                role="tabpanel">
                <div class="mt-3">
                    {{-- =================== EQUIPOS =================== --}}
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-network-wired me-2 text-primary"></i> Equipos Asignados
                    </h5>

                    {{-- Buscador de equipos --}}
                    <div class="mb-3">
                        <input type="text" wire:model.live.debounce.500ms="buscarEquipo" class="form-control"
                            placeholder="Buscar por modelo, MAC o serial...">
                    </div>

                    @if ($equipos->count())
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Imagen</th>
                                        <th>Modelo</th>
                                        <th>MAC</th>
                                        <th>Serial</th>
                                        <th>Fecha</th>
                                        <th>Descripción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($equipos as $equipo)
                                        <tr>
                                            <td>
                                                @if (!empty($equipo->modelo->foto))
                                                    <img src="{{ asset('storage/' . $equipo->modelo->foto) }}"
                                                        alt="foto {{ $equipo->modelo->nombre }}" class="rounded"
                                                        width="50" height="50" style="object-fit: cover;">
                                                @else
                                                    <img src="{{ asset('images/no-image.png') }}" alt="sin imagen"
                                                        width="50" height="50" class="rounded border">
                                                @endif
                                            </td>
                                            <td>{{ $equipo->modelo->nombre ?? 'Sin modelo' }}</td>
                                            <td>{{ $equipo->mac ?? 'N/A' }}</td>
                                            <td>{{ $equipo->serial ?? 'N/A' }}</td>
                                            <td>{{ $equipo->fecha ? \Carbon\Carbon::parse($equipo->fecha)->format('d/m/Y') : 'N/A' }}
                                            </td>
                                            <td>{{ $equipo->descripcion }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No se encontraron equipos asignados o que coincidan con la
                            búsqueda.</div>
                    @endif
                </div>
            </div>

            <!-- Pestaña de Consumibles -->
            <div class="tab-pane fade @if ($activeTab === 'consumibles') show active @endif" id="consumibles"
                role="tabpanel">
                <div class="mt-3">
                    {{-- =================== CONSUMIBLES =================== --}}
                    <h5 class="border-bottom pb-2 mb-3">
                        <i class="fas fa-boxes me-2 text-success"></i> Consumibles Asignados
                    </h5>

                    {{-- Buscador de consumibles --}}
                    <div class="mb-3">
                        <input type="text" wire:model.live.debounce.500ms="buscarConsumible" class="form-control"
                            placeholder="Buscar por nombre de consumible...">
                    </div>

                    @if ($consumibles->count())
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Cantidad</th>
                                        <th>Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($consumibles as $item)
                                        <tr>
                                            <td>{{ $item->consumible->nombre ?? 'Sin nombre' }}</td>
                                            <td>{{ $item->consumible->descripcion ?? 'Sin descripción' }}</td>
                                            <td>{{ $item->cantidad }}</td>
                                            <td>{{ $item->consumible->unidad ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No se encontraron consumibles que coincidan con la búsqueda.</div>
                    @endif
                </div>

            </div>

            <!-- Pestaña de Historial de Movimientos -->
            <div class="tab-pane fade @if ($activeTab === 'movimientos') show active @endif" id="movimientos"
                role="tabpanel">
                <div class="mt-3">
                    <h4 class="mt-4 mb-3"><i class="fas fa-history me-2"></i>Historia de movimientos</h4>

                    <div class="mb-3">
                        <input type="text" wire:model.live="searchMovimientos" class="form-control"
                            placeholder="Buscar movimiento por consumible o modelo...">

                    </div>

                    {{-- TABLA MOVIMIENTOS DE INVENTARIO --}}
                    <h5 class="mt-3"><i class="fas fa-server me-2"></i>Equipos</h5>
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
                                                Usuario: {{ $mov->userAnterior->name }},
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
                                        <td colspan="7" class="text-center text-muted">No se encontraron
                                            movimientos de equipos
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $movimientosInventario->links() }}
                        </div>
                    </div>

                    {{-- TABLA MOVIMIENTOS DE CONSUMIBLES --}}
                    <h5><i class="fas fa-box me-2"></i>Consumibles</h5>
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
                                            @php
                                                $origen = $mov->origen();
                                            @endphp
                                            @if ($origen)
                                                {{ ucfirst($mov->origen_tipo) }}:
                                                {{ $origen->nombre ?? ($origen->name ?? 'N/A') }}
                                            @else
                                                Registro bodega
                                            @endif
                                        </td>

                                        <td>
                                            @php
                                                $destino = $mov->destino();
                                            @endphp
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
                                        <td colspan="8" class="text-center text-muted">No se encontraron
                                            movimientos de
                                            consumibles</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="mt-2">
                            {{ $movimientosConsumibles->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
