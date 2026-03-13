<div class="card">

    <div class="card-header bg-white py-3">
        <div class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-secondary"></i>
                    </span>
                    <input type="text" class="form-control border-start-0 ps-0 bg-light" placeholder="Nombre o MAC..."
                        wire:model.live="searchAp">
                </div>
            </div>

            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light border-end-0 px-3">
                        <i class="fas fa-broadcast-tower" style="color: #20207d;"></i>
                    </span>
                    <select class="form-select border-start-0 ps-3 bg-light" wire:model.live="filtroNodo"
                        style="cursor: pointer; font-size: 0.85rem;">

                        <option value="" class="fw-bold">Todos los nodos</option>

                        @foreach ($nodos as $nodo)
                            <option value="{{ $nodo->id }}">
                                {{ $nodo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-3 text-end">
                <button class="btn btn-sm btn-primary shadow-sm w-100" data-bs-toggle="modal"
                    data-bs-target="#modalCrear" wire:click="limpiar"
                    style="background-color: #20207d; border-color: #20207d;">
                    <i class="fas fa-plus-circle me-1"></i> Nuevo AP
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{-- TABLA PRINCIPAL --}}
        <div class="table-responsive shadow-sm rounded border">
            <table class="table table-hover table-striped align-middle mb-0">

                <thead class="table-dark">

                    <tr>
                        <th>Nombre</th>
                        <th>Nodo</th>
                        <th>IP LAN</th>
                        <th>IP WAN</th>
                        <th>SSID</th>
                        <th>Mac</th>
                        <th>Clientes</th>
                        {{-- <th>Estado</th> --}}
                        <th>Acciones</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach ($aps as $ap)
                        <tr>

                            <td>{{ $ap->nombre }}</td>
                            <td>{{ $ap->inventario->nodo->nombre ?? '' }}</td>
                            <td>
                                @php
                                    $protocoloLan = $ap->puerto_lan == 443 ? 'https' : 'http';
                                @endphp
                                <a href="{{ $protocoloLan }}://{{ $ap->ip_lan }}" target="_blank"
                                    class="text-decoration-none">
                                    <i class="fas fa-network-wired small"></i> {{ $ap->ip_lan }}
                                </a>
                            </td>
                            <td>
                                @if ($ap->ip_wan && $ap->puerto_wan)
                                    @php
                                        $protocolo = $ap->puerto_lan == 443 ? 'https' : 'http';
                                        $urlWan = "{$protocolo}://" . trim($ap->ip_wan) . ':' . trim($ap->puerto_wan);
                                    @endphp

                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control form-control-sm bg-white"
                                            value="{{ $ap->ip_wan }}:{{ $ap->puerto_wan }}" readonly>
                                        <button class="btn btn-danger" type="button"
                                            onclick="copyToClipboard('{{ $urlWan }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                @else
                                    <span class="text-muted small">N/A</span>
                                @endif
                            </td>
                            <td>{{ $ap->ssid }}</td>
                            <td>{{ $ap->inventario->mac }}</td>

                            <td>

                                <span class="badge bg-info">
                                    {{ $ap->clientes_count }}
                                    @if ($ap->clientes_max)
                                        / {{ $ap->clientes_max }}
                                    @endif
                                </span>

                            </td>

                            {{-- <td>

                            @if ($ap->estado == 'activo')
                                <span class="badge bg-success">Activo</span>
                            @endif

                            @if ($ap->estado == 'mantenimiento')
                                <span class="badge bg-warning">Mantenimiento</span>
                            @endif

                            @if ($ap->estado == 'caido')
                                <span class="badge bg-danger">Caido</span>
                            @endif

                        </td> --}}

                            <td>

                                <button class="btn btn-info btn-sm" wire:click="verClientes({{ $ap->id }})">
                                    <i class="fas fa-users"></i>
                                </button>

                                <button class="btn btn-warning btn-sm" wire:click="editarAp({{ $ap->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button class="btn btn-secondary btn-sm" wire:click="verDetalle({{ $ap->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>

                            </td>

                        </tr>
                    @endforeach

                </tbody>

            </table>
        </div>
        <!-- MODAL CREAR AP -->
        <div wire:ignore.self class="modal fade" id="modalCrear">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Crear Access Point</h5>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" class="form-control" wire:model="nombre"
                                    placeholder="Ej. AP-Norte-01">
                                @error('nombre')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Inventario (Buscador)</label>

                                <div class="position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                        <input type="text"
                                            class="form-control @error('inventario_id') is-invalid @enderror"
                                            placeholder="Buscar por Modelo o MAC..." wire:model.live="searchInventario">
                                    </div>

                                    @if ($inventario_id)
                                        <div class="mt-2">
                                            <span
                                                class="badge bg-primary p-2 w-100 d-flex justify-content-between align-items-center">
                                                <span><i class="fas fa-microchip me-2"></i>
                                                    {{ $inventario_nombre }}</span>
                                                <button type="button" class="btn-close btn-close-white"
                                                    style="font-size: 0.5rem;"
                                                    wire:click="$set('inventario_id', null)"></button>
                                            </span>
                                        </div>
                                    @endif

                                    @if (count($this->equiposInventario) > 0)
                                        <ul class="list-group position-absolute w-100 shadow-lg"
                                            style="z-index: 1050; top: 40px;">
                                            @foreach ($this->equiposInventario as $inv)
                                                <li class="list-group-item list-group-item-action cursor-pointer"
                                                    style="cursor: pointer;"
                                                    wire:click="seleccionarEquipo({{ $inv->id }}, '{{ $inv->modelo->nombre }}', '{{ $inv->mac }}')">
                                                    <div class="d-flex justify-content-between">
                                                        <strong>{{ $inv->modelo->nombre }}</strong>
                                                        <small class="text-muted">{{ $inv->mac }}</small>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                @error('inventario_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Configuración LAN</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="IP LAN"
                                        wire:model="ip_lan">
                                    <span class="input-group-text">:</span>
                                    <input type="number" class="form-control" style="max-width: 85px;"
                                        placeholder="443" wire:model="puerto_lan">
                                </div>
                                @error('ip_lan')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                                @error('puerto_lan')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Configuración WAN</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="IP WAN"
                                        wire:model="ip_wan">
                                    <span class="input-group-text">:</span>
                                    <input type="number" class="form-control" style="max-width: 85px;"
                                        placeholder="80" wire:model="puerto_wan">
                                </div>
                                @error('ip_wan')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                                @error('puerto_wan')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">SSID (Nombre WiFi)</label>
                                <input type="text" class="form-control" wire:model="ssid">
                                @error('ssid')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Clave WiFi</label>
                                <input type="text" class="form-control" wire:model="clave">
                                @error('clave')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usuario Login</label>
                                <input type="text" class="form-control" wire:model="user_login">
                                @error('user_login')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Password Login</label>
                                <input type="text" class="form-control" wire:model="clave_login">
                                @error('clave_login')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Clientes Max</label>
                                <input type="number" class="form-control" wire:model="clientes_max">
                                @error('clientes_max')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Frecuencia (GHz)</label>
                                <input type="text" class="form-control" wire:model="frecuencia"
                                    placeholder="Ej: 5.8">
                                @error('frecuencia')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ancho de Canal</label>
                                <select class="form-select" wire:model="ancho_canal">
                                    <option value="">Seleccione...</option>
                                    <option value="20">20 MHz</option>
                                    <option value="40">40 MHz</option>
                                    <option value="80">80 MHz</option>
                                    <option value="160">160 MHz</option>
                                </select>
                                @error('ancho_canal')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>

                        <button class="btn btn-primary" wire:click="guardar">
                            Guardar
                        </button>

                    </div>


                </div>
            </div>
        </div>
        {{-- MODAL VER CLIENTES --}}
        <div wire:ignore.self class="modal fade" id="modalClientes">

            <div class="modal-dialog modal-lg">

                <div class="modal-content">

                    <div class="modal-header bg-light border-0">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-broadcast-tower text-primary me-2"></i>

                            <span class="fw-bold">
                                Clientes conectados a
                            </span>

                            <span class="badge bg-primary ms-2 px-3 py-2">
                                {{ $apSeleccionado }}
                            </span>
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" placeholder="Buscar cliente por nombre..."
                                wire:model.live="searchCliente">
                        </div>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-bordered">
                                <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Contrato</th>
                                        <th>IP</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($this->clientesFiltrados as $cliente)
                                        <tr>
                                            <td>
                                                <a href="{{ route('clientes.show', $cliente->id) }}" target="_blank"
                                                    class="text-decoration-none fw-bold" style="color: #54b48f;">
                                                    {{ $cliente->nombre }}
                                                </a>
                                            </td>
                                            <td>{{ $cliente->contrato->id ?? '' }}</td>
                                            <td>{{ $cliente->ip ?? '' }}</td>
                                            <td>
                                                @if ($cliente->estado == 'activo')
                                                    <span class="badge bg-success">Activo</span>
                                                @else
                                                    <span class="badge bg-danger">Cortado</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">No hay clientes en este AP</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>

                </div>
            </div>
        </div>
        {{-- MODAL EDITAR --}}
        <div wire:ignore.self class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Access Point</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nombre</label>
                                <input type="text" class="form-control" wire:model="nombre">
                                @error('nombre')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Equipo de Inventario</label>

                                <div class="position-relative">
                                    @if (!$inventario_id)
                                        <div class="input-group shadow-sm">
                                            <span class="input-group-text bg-white border-end-0"><i
                                                    class="fas fa-search text-muted"></i></span>
                                            <input type="text" class="form-control border-start-0 ps-0"
                                                placeholder="Buscar Modelo o MAC..."
                                                wire:model.live="searchInventario">
                                        </div>
                                    @else
                                        <div class="card border-primary bg-primary-subtle shadow-sm">
                                            <div
                                                class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-primary d-block fw-bold"
                                                        style="font-size: 0.7rem;">EQUIPO ASOCIADO</small>
                                                    <span class="text-dark fw-bold">{{ $inventario_nombre }}</span>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-danger rounded-circle"
                                                    wire:click="quitarEquipo" title="Liberar equipo">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                    @if (count($this->equiposInventario) > 0)
                                        <ul class="list-group position-absolute w-100 shadow-lg"
                                            style="z-index: 1050; top: 45px;">
                                            @foreach ($this->equiposInventario as $inv)
                                                <li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center cursor-pointer"
                                                    style="cursor: pointer;"
                                                    wire:click="seleccionarEquipo({{ $inv->id }}, '{{ $inv->modelo->nombre }}', '{{ $inv->mac }}')">
                                                    <div>
                                                        <i class="fas fa-microchip me-2 text-muted"></i>
                                                        <strong>{{ $inv->modelo->nombre }}</strong>
                                                        <small
                                                            class="text-muted d-block ms-4">{{ $inv->mac }}</small>
                                                    </div>
                                                    @if ($inv->id == $this->inventario_id)
                                                        <span class="badge bg-primary">Actual</span>
                                                    @else
                                                        <span class="badge bg-success">Disponible</span>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>

                                @error('inventario_id')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Configuración LAN</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="IP LAN"
                                        wire:model="ip_lan">
                                    <span class="input-group-text">:</span>
                                    <input type="number" class="form-control" style="max-width: 85px;"
                                        wire:model="puerto_lan">
                                </div>
                                @error('ip_lan')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Configuración WAN</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="IP WAN"
                                        wire:model="ip_wan">
                                    <span class="input-group-text">:</span>
                                    <input type="number" class="form-control" style="max-width: 85px;"
                                        wire:model="puerto_wan">
                                </div>
                                @error('ip_wan')
                                    <span class="text-danger small d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">SSID</label>
                                <input type="text" class="form-control" wire:model="ssid">
                                @error('ssid')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Clave WiFi</label>
                                <input type="text" class="form-control" wire:model="clave">
                                @error('clave')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Usuario Login</label>
                                <input type="text" class="form-control" wire:model="user_login">
                                @error('user_login')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Password Login</label>
                                <input type="text" class="form-control" wire:model="clave_login">
                                @error('clave_login')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Clientes Max</label>
                                <input type="number" class="form-control" wire:model="clientes_max">
                                @error('clientes_max')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Frecuencia</label>
                                <input type="number" step="0.1" class="form-control" wire:model="frecuencia">
                                @error('frecuencia')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ancho de Canal</label>
                                <select class="form-select" wire:model="ancho_canal">
                                    <option value="">Seleccione...</option>
                                    <option value="20">20 MHz</option>
                                    <option value="40">40 MHz</option>
                                    <option value="80">80 MHz</option>
                                    <option value="160">160 MHz</option>
                                </select>
                                @error('ancho_canal')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                        </div>
                        <hr>
                        <h6 class="fw-bold" style="color: #20207d;"><i class="fas fa-users"></i> Vincular
                            Clientes a
                            este
                            AP</h6>

                        <div class="row">
                            <div class="col-12">
                                <div class="input-group mb-2">
                                    <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control"
                                        placeholder="Buscar cliente por nombre..." wire:model.live="searchCliente">
                                </div>

                                <div class="list-group shadow-sm"
                                    style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6;">
                                    @forelse($this->clientes as $cliente)
                                        <label class="list-group-item list-group-item-action p-3">
                                            <div class="d-flex align-items-center">

                                                <div class="d-flex justify-content-center align-items-center"
                                                    style="width: 50px; flex-shrink: 0;">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $cliente->id }}"
                                                        wire:model.live="selectedClientes"
                                                        id="cliente_{{ $cliente->id }}"
                                                        style="cursor: pointer; width: 1.4em; height: 1.4em; border: 1.5px solid #3333cc; margin: 0;">
                                                </div>

                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex align-items-center">
                                                        <span
                                                            class="fw-bold {{ $cliente->ap_id == $this->ap_id ? 'text-primary' : '' }}"
                                                            style="font-size: 1rem;">
                                                            {{ $cliente->nombre }}
                                                        </span>

                                                        @if ($cliente->ap_id == $this->ap_id)
                                                            <small class="badge bg-primary-subtle text-primary ms-2"
                                                                style="font-size: 0.65rem;">Actual</small>
                                                        @endif
                                                    </div>

                                                    <small class="text-muted d-block mt-1">
                                                        <i class="fas fa-network-wired me-1"></i> IP:
                                                        {{ $cliente->ip ?? 'Sin IP' }}
                                                    </small>
                                                </div>

                                                <div class="ms-auto">
                                                    <span class="badge bg-light text-dark border px-3 py-2">
                                                        #{{ $cliente->contrato_id ?? 'N/A' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    @empty
                                        <div class="p-3 text-center text-muted small">No hay clientes vinculados ni
                                            coincidencias.</div>
                                    @endforelse
                                </div>

                                @if (count($selectedClientes) > 0)
                                    <div class="mt-2">
                                        <small class="text-primary fw-bold">
                                            Has seleccionado {{ count($selectedClientes) }} cliente(s) para añadir.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" wire:click="actualizarAp">
                            <i class="fas fa-save"></i> Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        {{-- MODAL VER DETALLE --}}
        <div wire:ignore.self class="modal fade" id="modalDetalle">

            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header text-white" style="background-color: #20207d;">
                        <h5 class="modal-title"><i class="fas fa-broadcast-tower"></i> Detalle Técnico del Access
                            Point
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        @if ($detalleAp && $detalleInventario)
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3"
                                        style="color: #3333cc; border-bottom: 2px solid #3333cc;">
                                        <i class="fas fa-cog"></i> Configuración de Red
                                    </h6>
                                    <ul class="list-group list-group-flush mb-4">
                                        <li class="list-group-item"><strong>Nombre:</strong>
                                            {{ $detalleAp->nombre }}
                                        </li>
                                        <li class="list-group-item"><strong>IP LAN:</strong>
                                            {{ $detalleAp->ip_lan }}
                                        </li>
                                        <li class="list-group-item"><strong>IP WAN:</strong>
                                            {{ $detalleAp->ip_wan }}
                                        </li>
                                        <li class="list-group-item"><strong>Puerto LAN/WAN:</strong>
                                            {{ $detalleAp->puerto_lan }} / {{ $detalleAp->puerto_wan }}</li>
                                        <li class="list-group-item"><strong>SSID:</strong> {{ $detalleAp->ssid }}
                                        </li>
                                        <li class="list-group-item"><strong>Clave Wi-Fi:</strong> <code
                                                class="text-dark">{{ $detalleAp->clave }}</code></li>
                                        <li class="list-group-item"><strong>Login User:</strong>
                                            {{ $detalleAp->user_login }}</li>
                                        <li class="list-group-item"><strong>Login Pass:</strong>
                                            <code>{{ $detalleAp->clave_login }}</code>
                                        </li>
                                        <li class="list-group-item"><strong>Máx. Clientes:</strong>
                                            {{ $detalleAp->clientes_max }}</li>
                                        <li class="list-group-item"><strong>Frecuencia:</strong>
                                            {{ $detalleAp->frecuencia }}</li>
                                        <li class="list-group-item"><strong>Ancho Canal:</strong>
                                            {{ $detalleAp->ancho_canal }}</li>
                                        <li class="list-group-item">
                                            <strong>Estado:</strong>
                                            <span
                                                class="badge {{ $detalleAp->estado == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                                {{ ucfirst($detalleAp->estado) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3"
                                        style="color: #20207d; border-bottom: 2px solid #20207d;">
                                        <i class="fas fa-box"></i> Datos de Inventario
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item"><strong>MAC Address:</strong> <span
                                                class="text-uppercase">{{ $detalleInventario->mac }}</span></li>
                                        <li class="list-group-item"><strong>Serial:</strong>
                                            {{ $detalleInventario->serial }}</li>
                                        <li class="list-group-item"><strong>Modelo:</strong>
                                            {{ $detalleInventario->modelo->nombre }}</li>
                                        <li class="list-group-item"><strong>Nodo Instalado:</strong>
                                            {{ $detalleInventario->nodo->nombre ?? 'N/A' }}</li>
                                        <li class="list-group-item"><strong>Fecha Ingreso:</strong>
                                            {{ \Carbon\Carbon::parse($detalleInventario->fecha)->format('d/m/Y') }}
                                        </li>
                                        <li class="list-group-item"><strong>Descripción:</strong> <small
                                                class="text-muted">{{ $detalleInventario->descripcion ?? 'Sin descripción' }}</small>
                                        </li>
                                    </ul>

                                    @if ($detalleInventario->modelo && $detalleInventario->modelo->foto)
                                        <div class="mt-3 text-center">
                                            <img src="{{ asset('storage/' . $detalleInventario->modelo->foto) }}"
                                                alt="Foto del modelo" class="img-fluid rounded border shadow-sm"
                                                style="max-height: 150px; object-fit: contain; width: 100%; background-color: #fff;">
                                        </div>
                                    @endif

                                    <div class="mt-4 p-2 rounded"
                                        style="background-color: #f8f9fa; border-left: 4px solid #3333cc;">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i> Este equipo está vinculado al ID de
                                            Inventario #{{ $detalleAp->inventario_id }}.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status"></div>
                                <p class="mt-2">Cargando información técnica...</p>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('abrirModalClientes', event => {

        let modal = new bootstrap.Modal(document.getElementById('modalClientes'));
        modal.show();

    });
    window.addEventListener('abrirModalEditar', () => {
        let modal = new bootstrap.Modal(document.getElementById('modalEditar'));
        modal.show();
    });

    window.addEventListener('cerrarModalEditar', () => {
        let modal = bootstrap.Modal.getInstance(document.getElementById('modalEditar'));
        modal.hide();
    });

    window.addEventListener('abrirModalDetalle', () => {
        let modal = new bootstrap.Modal(document.getElementById('modalDetalle'));
        modal.show();
    });
</script>

<script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('Enlace copiado: ' + text + '\nPégalo en una nueva pestaña.');
        });
    }
</script>
