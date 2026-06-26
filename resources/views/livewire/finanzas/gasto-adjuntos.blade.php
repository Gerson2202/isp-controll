<div>
    {{-- resources/views/livewire/finanzas/gasto-adjuntos.blade.php --}}
    <div>
        {{-- Botón para volver --}}
        <div class="mb-3">
            <a href="{{ route('finanzas.gastos.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Volver a Gastos
            </a>
            <span class="ms-3 text-muted">
                <i class="fas fa-tag me-1"></i> Gasto: <strong>{{ $gasto->concepto ?? 'Sin concepto' }}</strong>
            </span>
            <span class="ms-2 text-muted">
                <i class="fas fa-dollar-sign me-1"></i> $ {{ number_format($gasto->valor ?? 0, 0, ',', '.') }}
            </span>
        </div>

        <div class="row g-4">

            {{-- FORMULARIO DE SUBIDA --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white py-2">
                        <h6 class="mb-0 fw-bold">
                            <i class="fas fa-upload me-2"></i> Subir Archivo
                        </h6>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="subirArchivo">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-file me-1 text-primary"></i> Seleccionar archivo
                                </label>
                                <input type="file" class="form-control @error('archivo') is-invalid @enderror"
                                    wire:model="archivo" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                @error('archivo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror

                                @if ($archivo)
                                    <div class="mt-2 text-muted small">
                                        <i class="fas fa-file me-1"></i>
                                        {{ $archivo->getClientOriginalName() }}
                                        ({{ number_format($archivo->getSize() / 1024, 1) }} KB)
                                    </div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-align-left me-1 text-primary"></i> Descripción (opcional)
                                </label>
                                <textarea class="form-control" rows="2" wire:model="descripcion" placeholder="Breve descripción del archivo..."></textarea>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                    <span wire:loading.remove>
                                        <i class="fas fa-cloud-upload-alt me-2"></i> Subir
                                    </span>
                                    <span wire:loading>
                                        <i class="fas fa-spinner fa-spin me-2"></i> Subiendo...
                                    </span>
                                </button>
                            </div>
                        </form>

                        <div class="mt-3">
                            <div class="alert alert-info small">
                                <i class="fas fa-info-circle me-1"></i>
                                Formatos permitidos: PDF, JPG, PNG, DOC, DOCX, XLS, XLSX
                                <br>Máximo: 10MB
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LISTADO DE ARCHIVOS --}}
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-paperclip me-2"></i> Archivos Adjuntos
                            <span class="badge bg-secondary ms-2">{{ $adjuntos->count() }}</span>
                        </h6>
                        <button class="btn btn-sm btn-outline-secondary" wire:click="cargarAdjuntos">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @if ($adjuntos->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($adjuntos as $adjunto)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-center">
                                                @php
                                                    $icono = 'fa-file';
                                                    $color = 'text-secondary';
                                                    $mime = $adjunto->mime_type;

                                                    if (str_contains($mime, 'pdf')) {
                                                        $icono = 'fa-file-pdf';
                                                        $color = 'text-danger';
                                                    } elseif (str_contains($mime, 'image')) {
                                                        $icono = 'fa-file-image';
                                                        $color = 'text-success';
                                                    } elseif (
                                                        str_contains($mime, 'word') ||
                                                        str_contains($mime, 'document')
                                                    ) {
                                                        $icono = 'fa-file-word';
                                                        $color = 'text-primary';
                                                    } elseif (
                                                        str_contains($mime, 'excel') ||
                                                        str_contains($mime, 'spreadsheet')
                                                    ) {
                                                        $icono = 'fa-file-excel';
                                                        $color = 'text-success';
                                                    }
                                                @endphp
                                                <i class="fas {{ $icono }} {{ $color }} fa-2x"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $adjunto->nombre_original }}</div>
                                                <div class="text-muted small">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $adjunto->created_at->format('d/m/Y H:i') }}
                                                    <span class="mx-1">•</span>
                                                    <i class="fas fa-weight me-1"></i>
                                                    {{ number_format($adjunto->size / 1024, 1) }} KB
                                                    @if ($adjunto->descripcion)
                                                        <span class="mx-1">•</span>
                                                        <i class="fas fa-align-left me-1"></i>
                                                        {{ $adjunto->descripcion }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary"
                                                wire:click="descargarArchivo({{ $adjunto->id }})" title="Descargar">
                                                <i class="fas fa-download"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger"
                                                wire:click="eliminarAdjunto({{ $adjunto->id }})"
                                                wire:confirm="¿Estás seguro de eliminar este archivo?" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-paperclip fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted mb-0">No hay archivos adjuntos</p>
                                <small class="text-muted">Sube archivos relacionados con este gasto</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
