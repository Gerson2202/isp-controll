<div class="container py-2">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestión de Roles y Permisos</h5>
            <button wire:click="create" class="btn btn-light btn-sm fw-bold">
                <i class="fas fa-plus"></i> Nuevo Rol
            </button>
        </div>

        <div class="card-body">
            <input type="text" wire:model.live="search" class="form-control mb-3" placeholder="Buscar rol...">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Permisos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                            <tr>
                                <td>{{ $role->id }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $role->permissions->count() }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button wire:click="edit({{ $role->id }})" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $role->id }})" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay roles registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $roles->links() }}
        </div>
    </div>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="modalRol" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="roleModalLabel">{{ $modalTitle }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Rol</label>
                        <input type="text" wire:model.defer="name"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Ingrese el nombre del rol">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Permisos</label>
                        <div class="row">
                            @foreach($permissions as $perm)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            wire:model="selectedPermissions"
                                            value="{{ $perm->name }}"
                                            id="perm_{{ $perm->id }}">
                                        <label class="form-check-label" for="perm_{{ $perm->id }}">
                                            {{ $perm->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button wire:click="save" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Mostrar modal para crear
    window.addEventListener('show-create-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalRol'));
        modal.show();
    });

    // Mostrar modal para editar
    window.addEventListener('show-edit-modal', () => {
        const modal = new bootstrap.Modal(document.getElementById('modalRol'));
        modal.show();
    });

    // Ocultar todos los modales
    window.addEventListener('hide-modals', () => {
        const modalEl = document.getElementById('modalRol');
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    });
</script>

<!-- Script para notificaciones Toastr -->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

   <script>
    document.addEventListener('livewire:load', () => {
        // Evita registrar el evento más de una vez
        if (!window.__toastrListenerAdded) {
            window.__toastrListenerAdded = true;

            Livewire.on('notify', (data) => {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: "toast-bottom-right",
                    timeOut: "4000",
                };
                toastr[data.type](data.message);
            });
        }
    });
</script>

@endpush

