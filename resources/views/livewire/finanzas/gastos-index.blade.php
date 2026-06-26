{{-- resources/views/livewire/finanzas/gastos-index.blade.php --}}
<div>
    <div class="row g-4">

        {{-- FORMULARIO --}}
        <div class="col-lg-4 col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas {{ $gasto_id ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                        {{ $gasto_id ? 'Editar Gasto' : 'Nuevo Gasto' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="guardar">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i> Categoría *
                            </label>
                            <select class="form-select @error('categorias_gasto_id') is-invalid @enderror"
                                wire:model="categorias_gasto_id">
                                <option value="">Seleccione...</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}">
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorias_gasto_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-text-width me-1 text-primary"></i> Concepto *
                            </label>
                            <input type="text" class="form-control @error('concepto') is-invalid @enderror"
                                wire:model="concepto" placeholder="Ej: Compra de insumos...">
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
                                    wire:model="valor" placeholder="0" x-data
                                    x-on:input="$event.target.value = $event.target.value.replace(/[^0-9]/g, '')">
                                @error('valor')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar me-1 text-primary"></i> Fecha *
                            </label>
                            <input type="date" class="form-control @error('fecha_gasto') is-invalid @enderror"
                                wire:model="fecha_gasto">
                            @error('fecha_gasto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tipo</label>
                                <select class="form-select" wire:model="tipo">
                                    <option value="fijo">📌 Fijo</option>
                                    <option value="variable">📊 Variable</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Estado</label>
                                <select class="form-select" wire:model="estado">
                                    <option value="pagado">✅ Pagado</option>
                                    <option value="pendiente">⏳ Pendiente</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i> Descripción
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" rows="2" wire:model="descripcion"
                                placeholder="Detalles adicionales..."></textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Guardar
                            </button>
                            <button type="button" wire:click="limpiar" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i> Limpiar
                            </button>
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
                                <i class="fas fa-list me-2"></i> Gastos
                                <span class="badge bg-secondary ms-2">{{ $gastos->total() }}</span>
                            </h5>
                        </div>
                        <div class="col-sm-8">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <select class="form-select form-select-sm" wire:model.live="mes">
                                        <option value="">Todos los meses</option>
                                        @foreach ($mesesDisponibles as $mesItem)
                                            <option value="{{ str_pad($mesItem->mes, 2, '0', STR_PAD_LEFT) }}">
                                                {{ DateTime::createFromFormat('!m', $mesItem->mes)->format('F') }}
                                                {{ $mesItem->anio }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0"
                                            placeholder="Buscar gasto..." wire:model.live.debounce.300ms="buscar">
                                        @if ($buscar)
                                            <button class="btn btn-outline-secondary" wire:click="$set('buscar', '')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-outline-primary w-100"
                                        wire:click="$set('mes', ''); $set('buscar', '')">
                                        <i class="fas fa-undo me-1"></i> Limpiar filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RESUMEN DE TOTALES --}}
                <div class="card-body bg-light border-bottom">
                    <div class="row g-2 text-center">
                        <div class="col-4">
                            <div class="p-2 bg-white rounded shadow-sm">
                                <small class="text-muted d-block">Total Gastos</small>
                                <strong class="text-primary">$ {{ number_format($totalGastos, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-white rounded shadow-sm">
                                <small class="text-muted d-block">Pagado</small>
                                <strong class="text-success">$ {{ number_format($totalPagado, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-white rounded shadow-sm">
                                <small class="text-muted d-block">Pendiente</small>
                                <strong class="text-danger">$
                                    {{ number_format($totalPendiente, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Categoría</th>
                                    <th>Concepto</th>
                                    <th>Valor</th>
                                    <th>Estado</th>
                                    <th width="140" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gastos as $gasto)
                                    <tr>
                                        <td>
                                            <span
                                                class="fw-semibold">{{ $gasto->fecha_gasto->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge px-3 py-2"
                                                style="background: {{ $gasto->categoria?->color ?? '#6c757d' }}; color: #fff;">
                                                {{ $gasto->categoria?->nombre ?? 'Sin Categoría' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-semibold">{{ $gasto->concepto }}</span>
                                            @if ($gasto->descripcion)
                                                <small class="d-block text-muted">{{ $gasto->descripcion }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>$ {{ number_format($gasto->valor, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $gasto->estado == 'pagado' ? 'bg-success' : 'bg-warning' }} px-3 py-2">
                                                <i
                                                    class="fas {{ $gasto->estado == 'pagado' ? 'fa-check-circle' : 'fa-clock' }} me-1"></i>
                                                {{ ucfirst($gasto->estado) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <button class="btn btn-sm btn-warning"
                                                    wire:click="editar({{ $gasto->id }})" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ route('gastos.adjuntos', $gasto->id) }}"
                                                    class="btn btn-sm btn-info" title="Ver adjuntos">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger"
                                                    wire:click="eliminar({{ $gasto->id }})"
                                                    wire:confirm="¿Estás seguro de eliminar este gasto?"
                                                    title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No hay gastos registrados</p>
                                            <small class="text-muted">Comienza registrando un nuevo gasto</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            Mostrando {{ $gastos->firstItem() ?? 0 }} - {{ $gastos->lastItem() ?? 0 }} de
                            {{ $gastos->total() }}
                        </div>
                        <div>
                            {{ $gastos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
