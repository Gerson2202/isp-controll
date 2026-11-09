<div>
    <div class="row g-4">

        {{-- FORMULARIO --}}
        <div class="col-lg-4 col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas {{ $registro_id ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                        {{ $registro_id ? 'Editar Gasto Recurrente' : 'Nuevo Gasto Recurrente' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="guardar">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i> Categoría *
                            </label>
                            <select class="form-select @error('categoria_gasto_id') is-invalid @enderror"
                                wire:model="categoria_gasto_id">
                                <option value="">Seleccione...</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_gasto_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-text-width me-1 text-primary"></i> Concepto *
                            </label>
                            <input type="text" class="form-control @error('concepto') is-invalid @enderror"
                                wire:model="concepto" placeholder="Ej: Arriendo, Servicios...">
                            @error('concepto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-dollar-sign me-1 text-primary"></i> Valor *
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="text" class="form-control @error('valor') is-invalid @enderror"
                                    wire:model="valor_formateado" wire:keyup="updatedValor(valor_formateado)"
                                    placeholder="0">
                                @error('valor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-clock me-1 text-primary"></i> Frecuencia
                                </label>
                                <select class="form-select @error('frecuencia') is-invalid @enderror"
                                    wire:model="frecuencia">
                                    <option value="mensual">📅 Mensual (1 pago/mes)</option>
                                    <option value="quincenal">📆 Quincenal (2 pagos/mes)</option>
                                    <option value="semanal">📊 Semanal (4 pagos/mes)</option>
                                </select>
                                @error('frecuencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar-day me-1 text-primary"></i> Día
                                </label>
                                <input type="number" min="1" max="31"
                                    class="form-control @error('dia_ejecucion') is-invalid @enderror"
                                    wire:model="dia_ejecucion" placeholder="1-31">
                                @error('dia_ejecucion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipo</label>
                                <select class="form-select @error('tipo') is-invalid @enderror" wire:model="tipo">
                                    <option value="fijo">📌 Fijo</option>
                                    <option value="variable">📊 Variable</option>
                                </select>
                                @error('tipo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estado</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" role="switch" wire:model="activo"
                                        id="flexSwitchCheckDefault">
                                    <label class="form-check-label fw-semibold" for="flexSwitchCheckDefault">
                                        <i
                                            class="fas fa-circle {{ $activo ? 'text-success' : 'text-danger' }} me-1"></i>
                                        {{ $activo ? 'Activo' : 'Inactivo' }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i> Descripción
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" rows="2" wire:model="descripcion"
                                placeholder="Descripción adicional..."></textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>
                                {{ $registro_id ? 'Actualizar' : 'Guardar' }}
                            </button>
                            @if ($registro_id)
                                <button type="button" wire:click="limpiar" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i> Cancelar
                                </button>
                            @else
                                <button type="button" wire:click="limpiar" class="btn btn-outline-secondary">
                                    <i class="fas fa-undo me-2"></i> Limpiar
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- LISTADO --}}
        <div class="col-lg-8 col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="row g-2 align-items-center">
                        <div class="col-sm-4">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-sync-alt me-2"></i> Gastos Recurrentes
                            </h5>
                        </div>
                        <div class="col-sm-4">
                            <div class="d-flex justify-content-center">
                                <button class="btn btn-sm btn-outline-secondary" wire:click="cambiarMes('anterior')">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <span class="mx-2 fw-bold">{{ $nombreMes }}</span>
                                <button class="btn btn-sm btn-outline-secondary" wire:click="cambiarMes('siguiente')">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" placeholder="Buscar..."
                                    wire:model.live.debounce.300ms="buscar">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FILTROS --}}
                <div class="card-body bg-light border-bottom">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="p-2 bg-white rounded shadow-sm text-center">
                                <small class="text-muted d-block">Total Gastos Base</small>
                                <strong class="text-primary">$
                                    {{ number_format($totalGastosBase, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-white rounded shadow-sm text-center">
                                <small class="text-muted d-block">Pagados en {{ $nombreMes }}</small>
                                <strong class="text-success">$
                                    {{ number_format($totalPagadosMes ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-white rounded shadow-sm text-center">
                                <small class="text-muted d-block">Por pagar</small>
                                <strong class="text-danger">$
                                    {{ number_format($totalPendiente, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="btn-group w-100" role="group">
                                <button wire:click="$set('filtroEstado', 'todos')"
                                    class="btn btn-sm {{ $filtroEstado == 'todos' ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    Todos
                                </button>
                                <button wire:click="$set('filtroEstado', 'pagados')"
                                    class="btn btn-sm {{ $filtroEstado == 'pagados' ? 'btn-success' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-check-circle me-1"></i> Completados
                                </button>
                                <button wire:click="$set('filtroEstado', 'pendientes')"
                                    class="btn btn-sm {{ $filtroEstado == 'pendientes' ? 'btn-warning' : 'btn-outline-secondary' }}">
                                    <i class="fas fa-clock me-1"></i> Pendientes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLA DE GASTOS BASE --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Categoría</th>
                                    <th>Concepto</th>
                                    <th>Valor</th>
                                    <th>Frecuencia</th>
                                    <th>Estado en {{ $nombreMes }}</th>
                                    <th width="140" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastosPaginated as $item)
                                    <tr>

                                        {{-- Categoría --}}
                                        <td>
                                            <span class="badge px-3 py-2"
                                                style="background: {{ $item->categoria?->color ?? '#6c757d' }}; color: #fff;">
                                                {{ $item->categoria?->nombre ?? 'Sin Categoría' }}
                                            </span>
                                        </td>

                                        {{-- Concepto --}}
                                        <td>
                                            <span class="fw-semibold {{ !$item->activo ? 'text-muted' : '' }}">
                                                {{ $item->concepto }}
                                            </span>

                                            @if ($item->descripcion)
                                                <small class="d-block text-muted">
                                                    {{ $item->descripcion }}
                                                </small>
                                            @endif
                                        </td>

                                        {{-- Valor --}}
                                        <td>
                                            <strong class="{{ !$item->activo ? 'text-muted' : '' }}">
                                                $ {{ number_format($item->valor, 0, ',', '.') }}
                                            </strong>
                                        </td>

                                        {{-- Frecuencia --}}
                                        <td>
                                            @if ($item->frecuencia === 'mensual')
                                                <span class="badge {{ !$item->activo ? 'bg-secondary' : 'bg-info' }}">
                                                    📅 Mensual
                                                </span>
                                            @elseif($item->frecuencia === 'quincenal')
                                                <span class="badge {{ !$item->activo ? 'bg-secondary' : 'bg-info' }}">
                                                    📆 Quincenal
                                                </span>
                                            @elseif($item->frecuencia === 'semanal')
                                                <span class="badge {{ !$item->activo ? 'bg-secondary' : 'bg-info' }}">
                                                    📊 Semanal
                                                </span>
                                            @endif
                                        </td>

                                        {{-- Estado de pagos --}}
                                        <td>
                                            <div class="d-flex gap-2 align-items-center flex-wrap">

                                                <small class="{{ !$item->activo ? 'text-muted' : 'text-muted' }}">
                                                    <i class="fas fa-check-circle text-success"></i>
                                                    {{ $item->pagos_realizados }}/{{ $item->limite_pagos }}
                                                </small>

                                                @if ($item->activo)
                                                    @if ($item->pagos_pendientes > 0)
                                                        <span class="badge bg-warning text-dark">
                                                            {{ $item->pagos_pendientes }}
                                                            pendiente{{ $item->pagos_pendientes > 1 ? 's' : '' }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-success">
                                                            ✓ Completado
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">
                                                        Inactivo
                                                    </span>
                                                @endif

                                            </div>
                                        </td>

                                        {{-- Acciones --}}
                                        <td>
                                            <div class="d-flex gap-1 justify-content-center flex-wrap">

                                                {{-- Solo mostrar Pagar si está activo --}}
                                                @if ($item->activo && $item->puede_pagar)
                                                    <button class="btn btn-sm btn-success"
                                                        wire:click="abrirModalPago({{ $item->id }})"
                                                        title="Registrar pago">
                                                        <i class="fas fa-plus"></i> Pagar
                                                    </button>
                                                @endif

                                                {{-- Editar siempre visible --}}
                                                <button class="btn btn-sm btn-warning"
                                                    wire:click="editar({{ $item->id }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>

                                            </div>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-sync-alt fa-3x text-muted mb-3 d-block"></i>

                                            <p class="text-muted mb-0">
                                                No hay gastos recurrentes registrados
                                            </p>

                                            <small class="text-muted">
                                                Comienza creando un nuevo gasto recurrente
                                            </small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- PAGINACIÓN --}}
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $gastosPaginated->firstItem() ?? 0 }} -
                            {{ $gastosPaginated->lastItem() ?? 0 }} de
                            {{ $gastosPaginated->total() }}
                        </div>
                        <div>
                            {{ $gastosPaginated->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DE PAGO --}}
    @if ($mostrarModalPago)
        <div class="modal d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-credit-card me-2"></i> Registrar Pago
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="cerrarModalPago()"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar me-1"></i> Fecha del Pago
                                </label>
                                <input type="date" class="form-control" wire:model="fecha_pago_modal">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-credit-card me-1"></i> Método de Pago
                                </label>
                                <select class="form-select" wire:model="metodo_pago_seleccionado">
                                    <option value="efectivo">💵 Efectivo</option>
                                    <option value="transferencia">🏦 Transferencia</option>
                                    {{-- <option value="datafono">🔢 Datáfono</option> --}}
                                </select>
                            </div>

                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Nota:</strong> Este pago será registrado en la tabla de gastos con la fecha
                                especificada.
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModalPago()">
                            <i class="fas fa-times me-2"></i> Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="guardarPago()">
                            <i class="fas fa-save me-2"></i> Guardar Pago
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
