<div class="container-fluid py-3">
    <div class="row g-4">
        
        {{-- FORMULARIO --}}
        <div class="col-lg-4 col-md-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas {{ $categoria_id ? 'fa-edit' : 'fa-plus-circle' }} me-2"></i>
                        {{ $categoria_id ? 'Editar Categoría' : 'Nueva Categoría' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="guardar">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-tag me-1 text-primary"></i> Nombre *
                            </label>
                            <input type="text" 
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   wire:model="nombre"
                                   placeholder="Ej: Alimentación, Transporte...">
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-palette me-1 text-primary"></i> Color
                            </label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" 
                                       class="form-control form-control-color w-25"
                                       wire:model="color">
                                <span class="badge px-3 py-2" style="background: {{ $color }}; color: #fff;">
                                    {{ $color }}
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-align-left me-1 text-primary"></i> Descripción
                            </label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror"
                                      rows="3"
                                      wire:model="descripcion"
                                      placeholder="Breve descripción de la categoría..."></textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       role="switch"
                                       wire:model="activo"
                                       id="flexSwitchCheckDefault">
                                <label class="form-check-label fw-semibold" for="flexSwitchCheckDefault">
                                    <i class="fas fa-circle text-success me-1"></i> Activa
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i> Guardar
                            </button>
                            <button type="button" 
                                    wire:click="resetFormulario" 
                                    class="btn btn-outline-secondary">
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
                        <div class="col-sm-6">
                            <h5 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-list me-2"></i> Categorías de Gastos
                                <span class="badge bg-secondary ms-2">{{ $categorias->total() }}</span>
                            </h5>
                        </div>
                        <div class="col-sm-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text"
                                       class="form-control border-start-0"
                                       placeholder="Buscar categoría..."
                                       wire:model.live.debounce.300ms="buscar">
                                @if($buscar)
                                    <button class="btn btn-outline-secondary" 
                                            wire:click="$set('buscar', '')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="60">#</th>
                                    <th>Nombre</th>
                                    <th width="120">Estado</th>
                                    <th width="160" class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categorias as $categoria)
                                    <tr>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $categoria->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="badge rounded-circle p-2" 
                                                      style="background: {{ $categoria->color }}; width: 20px; height: 20px;">
                                                </span>
                                                <span class="fw-semibold">{{ $categoria->nombre }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $categoria->activo ? 'bg-success' : 'bg-danger' }} px-3 py-2">
                                                <i class="fas {{ $categoria->activo ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                {{ $categoria->activo ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group w-100" role="group">
                                                <button class="btn btn-sm btn-warning"
                                                        wire:click="editar({{ $categoria->id }})"
                                                        title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm {{ $categoria->activo ? 'btn-secondary' : 'btn-success' }}"
                                                        wire:click="cambiarEstado({{ $categoria->id }})"
                                                        title="{{ $categoria->activo ? 'Desactivar' : 'Activar' }}">
                                                    <i class="fas {{ $categoria->activo ? 'fa-pause' : 'fa-play' }}"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No existen categorías registradas</p>
                                            <small class="text-muted">Comienza creando una nueva categoría</small>
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
                            Mostrando {{ $categorias->firstItem() ?? 0 }} - {{ $categorias->lastItem() ?? 0 }} de {{ $categorias->total() }}
                        </div>
                        <div>
                            {{ $categorias->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>