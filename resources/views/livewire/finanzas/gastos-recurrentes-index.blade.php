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
                                    <option value="mensual">📅 Mensual</option>
                                    <option value="quincenal">📆 Quincenal</option>
                                    <option value="anual">📊 Anual</option>
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
                                        <i class="fas fa-circle {{ $activo ? 'text-success' : 'text-danger' }} me-1"></i>
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
                                <input type="text" class="form-control border-start-0"
                                    placeholder="Buscar..." wire:model.live.debounce.300ms="buscar">
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
                                <strong class="text-primary">$ {{ number_format($totalGastosBase, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-white rounded shadow-sm text-center">
                                <small class="text-muted d-block">Pagados en {{ $nombreMes }}</small>
                                <strong class="text-success">$ {{ number_format($totalPagadosMes ?? 0, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 bg-white rounded shadow-sm text-center">
                                <small class="text-muted d-block">Pendientes en {{ $nombreMes }}</small>
                                <strong class="text-danger">$ {{ number_format(($totalGastosBase - ($totalPagadosMes ?? 0)), 0, ',', '.') }}</strong>
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
                                    <i class="fas fa-check-circle me-1"></i> Pagados
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
                                    <th>Día</th>
                                    <th>Estado</th>
                                    <th class="text-center">Pago {{ $nombreMes }}</th>
                                    <th width="100" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastosPaginated as $item)
                                    <tr>
                                        <td>
                                            <span class="badge px-3 py-2"
                                                style="background: {{ $item->categoria?->color ?? '#6c757d' }}; color: #fff;">
                                                {{ $item->categoria?->nombre ?? 'Sin Categoría' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $item->concepto }}</span>
                                            @if ($item->descripcion)
                                                <small class="d-block text-muted">{{ $item->descripcion }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>$ {{ number_format($item->valor, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-calendar-day me-1"></i>
                                                {{ $item->dia_ejecucion }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm {{ $item->activo ? 'btn-success' : 'btn-danger' }} w-100"
                                                wire:click="cambiarEstado({{ $item->id }})">
                                                <i class="fas {{ $item->activo ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                {{ $item->activo ? 'Activo' : 'Inactivo' }}
                                            </button>
                                        </td>
                                        <td>
                                            @if($item->activo)
                                                @if($item->pagado_este_mes)
                                                    <div class="text-center">
                                                        <span class="badge bg-success py-2 px-3">
                                                            <i class="fas fa-check-circle me-1"></i> Pagado
                                                        </span>
                                                        <small class="d-block text-muted">
                                                            {{ $item->fecha_pago_mes ? $item->fecha_pago_mes->format('d/m/Y') : '' }}
                                                        </small>
                                                        <button class="btn btn-sm btn-secondary mt-1" 
                                                            wire:click="anularPago({{ $item->registro_pago?->id ?? 0 }})"
                                                            wire:confirm="¿Anular el pago de '{{ $item->concepto }}'?"
                                                            title="Anular pago">
                                                            <i class="fas fa-undo"></i> Anular
                                                        </button>
                                                    </div>
                                                @else
                                                    <button class="btn btn-sm btn-warning w-100"
                                                        wire:click="marcarComoPagado({{ $item->id }})"
                                                        wire:confirm="¿Marcar '{{ $item->concepto }}' como pagado en {{ $nombreMes }}?">
                                                        <i class="fas fa-hand-holding-usd me-1"></i> Marcar Pagado
                                                    </button>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary py-2 px-3">
                                                    <i class="fas fa-pause-circle me-1"></i> Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <button class="btn btn-sm btn-warning"
                                                    wire:click="editar({{ $item->id }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    wire:click="eliminar({{ $item->id }})"
                                                    wire:confirm="¿Estás seguro de eliminar este gasto recurrente?"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <i class="fas fa-sync-alt fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No hay gastos recurrentes registrados</p>
                                            <small class="text-muted">Comienza creando un nuevo gasto recurrente</small>
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
                            Mostrando {{ $gastosPaginated->firstItem() ?? 0 }} - {{ $gastosPaginated->lastItem() ?? 0 }} de
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
</div>