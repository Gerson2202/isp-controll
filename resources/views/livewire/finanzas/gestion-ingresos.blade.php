<div>
    <div class="card shadow-sm border-0">
        {{-- HEADER CON BOTÓN NUEVO INGRESO --}}
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-plus-circle me-2"></i> Gestión de Ingresos Adicionales
            </h5>
            <button class="btn btn-primary" wire:click="$toggle('mostrarFormulario')">
                <i class="fas {{ $mostrarFormulario ? 'fa-times' : 'fa-plus' }} me-2"></i>
                {{ $mostrarFormulario ? 'Cancelar' : 'Nuevo Ingreso' }}
            </button>
        </div>

        {{-- FORMULARIO --}}
        @if ($mostrarFormulario)
            <div class="card-body bg-light">
                <form wire:submit.prevent="{{ $ingresoEditando ? 'actualizarIngreso' : 'guardarIngreso' }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-text-width me-1 text-primary"></i> Concepto *
                            </label>
                            <input type="text" class="form-control @error('concepto') is-invalid @enderror"
                                wire:model="concepto" placeholder="Ej: Instalación de cámaras...">
                            @error('concepto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-1 text-primary"></i> Monto *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control @error('monto') is-invalid @enderror"
                                    wire:model="monto" wire:input="monto = limpiarMonto(monto)">
                            </div>
                            @error('monto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar me-1 text-primary"></i> Fecha *
                            </label>
                            <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror"
                                wire:model="fecha_ingreso">
                            @error('fecha_ingreso')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i> Tipo
                            </label>
                            <select class="form-select @error('tipo') is-invalid @enderror" wire:model="tipo">
                                <option value="instalacion">🔧 Instalación</option>
                                <option value="servicio_extra">🛠️ Servicio Extra</option>
                                <option value="venta_producto">📦 Venta de Producto</option>
                                <option value="consultoria">📋 Consultoría</option>
                                <option value="otro">📌 Otro</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-user me-1 text-primary"></i> Cliente (opcional)
                            </label>
                            <div class="position-relative">
                                <div class="input-group">
                                    <input type="text" class="form-control @error('cliente_id') is-invalid @enderror"
                                        wire:model.live="searchCliente" wire:focus="$set('showClienteList', true)"
                                        placeholder="Buscar cliente por nombre...">
                                    @if ($cliente_id)
                                        <button class="btn btn-outline-danger" type="button" wire:click="clearCliente">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>

                                @if ($showClienteList && $searchCliente && $clientes->isNotEmpty())
                                    <div class="dropdown-menu show w-100" style="max-height: 200px; overflow-y: auto;">
                                        @foreach ($clientes as $cliente)
                                            <a class="dropdown-item" href="#"
                                                wire:click.prevent="selectCliente({{ $cliente->id }}, '{{ $cliente->nombre }}')">
                                                <i class="fas fa-user me-2"></i> {{ $cliente->nombre }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                @error('cliente_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @if ($cliente_id)
                                <small class="text-success">
                                    <i class="fas fa-check-circle"></i> Cliente seleccionado: {{ $cliente_nombre }}
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-credit-card me-1 text-primary"></i> Método de Pago
                            </label>

                            <select class="form-select @error('metodo_pago') is-invalid @enderror"
                                wire:model="metodo_pago">
                                <option value="">Seleccione un método de pago</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Datáfono">Datáfono</option>
                            </select>

                            @error('metodo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-file-invoice me-1 text-primary"></i> N° Documento
                            </label>
                            <input type="text" class="form-control @error('numero_documento') is-invalid @enderror"
                                wire:model="numero_documento" placeholder="Factura, recibo...">
                            @error('numero_documento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i> Descripción adicional
                            </label>
                            <input type="text" class="form-control" wire:model="descripcion"
                                placeholder="Detalles adicionales...">
                        </div>

                        <div class="col-12">
                            <hr>
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i> {{ $ingresoEditando ? 'Actualizar' : 'Guardar' }}
                                </button>
                                <button type="button" wire:click="resetearFormulario"
                                    class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-2"></i> Limpiar
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        {{-- FILTROS Y TABLA --}}
        <div class="card-body">
            {{-- FILTROS --}}
            <div class="row g-2 mb-3">
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Buscar ingreso..."
                            wire:model.live.debounce.300ms="search">
                        @if ($search)
                            <button class="btn btn-outline-secondary" wire:click="$set('search', '')">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="confirmado">✅ Confirmado</option>
                        <option value="anulado">❌ Anulado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select class="form-select form-select-sm" wire:model.live="filtroTipo">
                        <option value="">Todos los tipos</option>
                        <option value="instalacion">🔧 Instalación</option>
                        <option value="servicio_extra">🛠️ Servicio Extra</option>
                        <option value="venta_producto">📦 Venta Producto</option>
                        <option value="consultoria">📋 Consultoría</option>
                        <option value="otro">📌 Otro</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select class="form-select form-select-sm" wire:model.live="filtroMes">
                        @foreach ($meses as $key => $mes)
                            <option value="{{ $key }}">{{ $mes }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" wire:click="limpiarFiltros">
                        <i class="fas fa-undo me-1"></i> Limpiar
                    </button>
                </div>
            </div>

            {{-- RESUMEN DE TOTALES --}}
            {{-- RESUMEN DE TOTALES --}}
            <div class="row g-2 mb-3">
                <div class="col-4">
                    <div class="p-2 bg-light rounded">
                        <small class="text-muted d-block">Total Ingresos</small>
                        <strong class="text-primary">$
                            {{ number_format($totalIngresos, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 bg-light rounded">
                        <small class="text-muted d-block">Confirmados</small>
                        <strong class="text-success">$
                            {{ number_format($totalConfirmados, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2 bg-light rounded">
                        <small class="text-muted d-block">Anulados</small>
                        <strong class="text-danger">$
                            {{ number_format($totalAnulados, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>

            {{-- TABLA --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Concepto</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th width="140" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ingresos as $ingreso)
                            <tr>
                                <td>
                                    <span class="fw-semibold">{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $ingreso->concepto }}</span>
                                    @if ($ingreso->descripcion)
                                        <small class="d-block text-muted">{{ $ingreso->descripcion }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge px-3 py-2" style="background: #6c757d; color: #fff;">
                                        @if ($ingreso->tipo == 'instalacion')
                                            <i class="fas fa-tools me-1"></i> Instalación
                                        @elseif($ingreso->tipo == 'servicio_extra')
                                            <i class="fas fa-wrench me-1"></i> Servicio Extra
                                        @elseif($ingreso->tipo == 'venta_producto')
                                            <i class="fas fa-box me-1"></i> Venta Producto
                                        @elseif($ingreso->tipo == 'consultoria')
                                            <i class="fas fa-clipboard-list me-1"></i> Consultoría
                                        @else
                                            <i class="fas fa-ellipsis-h me-1"></i> Otro
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-success">$
                                        {{ number_format($ingreso->monto, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if ($ingreso->cliente)
                                        <span class="badge bg-info px-3 py-2">
                                            <i class="fas fa-user me-1"></i>
                                            {{ $ingreso->cliente->nombre }}
                                        </span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($ingreso->estado == 'confirmado')
                                        <span class="badge bg-success px-3 py-2">
                                            <i class="fas fa-check-circle me-1"></i> Confirmado
                                        </span>
                                    @else
                                        <span class="badge bg-danger px-3 py-2">
                                            <i class="fas fa-times-circle me-1"></i> Anulado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group w-100" role="group">
                                        <button class="btn btn-sm btn-warning"
                                            wire:click="editarIngreso({{ $ingreso->id }})" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if ($ingreso->estado == 'confirmado')
                                            <button class="btn btn-sm btn-danger"
                                                wire:click="cambiarEstado({{ $ingreso->id }}, 'anulado')"
                                                wire:confirm="¿Estás seguro de anular este ingreso?" title="Anular">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <button class="btn btn-sm btn-success"
                                                wire:click="cambiarEstado({{ $ingreso->id }}, 'confirmado')"
                                                title="Confirmar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                    <p class="text-muted mb-0">No hay ingresos registrados</p>
                                    <small class="text-muted">Comienza registrando un nuevo ingreso</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINACIÓN --}}

            <div class="mt-4 d-flex justify-content-center">
                {{ $ingresos->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
            </div>

        </div>
    </div>
</div>
