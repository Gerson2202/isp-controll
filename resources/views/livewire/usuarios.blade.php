<div class="container mt-2">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Gestión de Usuarios</h5>
            <button class="btn btn-light btn-sm" wire:click="create">
                <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
            </button>
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <input type="text" wire:model.live="search" class="form-control w-25" placeholder="Buscar usuario...">
            </div>

            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Roles</th>
                            <th>Permisos</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>{{ $u->roles->pluck('name')->join(', ') }}</td>
                                <td>{{ $u->permissions->pluck('name')->join(', ') }}</td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm" wire:click="edit({{ $u->id }})" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    {{-- <button class="btn btn-danger btn-sm" wire:click="delete({{ $u->id }})"
                                        onclick="confirm('¿Eliminar usuario?') || event.stopImmediatePropagation()" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button> --}}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No hay usuarios registrados</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <!-- MODAL CREAR/EDITAR -->
    <div wire:ignore.self class="modal fade" id="modalUsuario" tabindex="-1" aria-labelledby="modalUsuarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalUsuarioLabel">
                        {{ $user_id ? 'Editar Usuario' : 'Nuevo Usuario' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" wire:model="name" class="form-control">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                    <label class="form-label fw-bold">Email</label>
                    <input type="email"
                        wire:model.defer="email"
                        class="form-control @error('email') is-invalid @enderror"
                        placeholder="Ingrese un correo válido">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                    <div class="mb-3" x-data="{ show: false }">
                    <label class="form-label fw-bold">Contraseña</label>

                    <div class="input-group">
                        <input :type="show ? 'text' : 'password'" 
                            wire:model="password"
                            class="form-control"
                            placeholder="{{ $user_id ? 'Dejar en blanco para mantener la actual' : 'Escribe una contraseña' }}">
                        <button type="button" class="btn btn-outline-secondary" @click="show = !show">
                            <i :class="show ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>

                        @if($user_id)
                            <small class="text-muted">Dejar en blanco para mantener la contraseña actual.</small>
                        @endif

                        @error('password') 
                            <small class="text-danger">{{ $message }}</small> 
                        @enderror
                    </div>


                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Roles</label>
                            <select multiple wire:model="selectedRoles" class="form-select">
                                @foreach($roles as $id => $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Permisos</label>
                            <select multiple wire:model="selectedPermissions" class="form-select">
                                @foreach($permissions as $id => $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" wire:click="save">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('show-create-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
            modal.show();
        });

        window.addEventListener('show-edit-modal', () => {
            const modal = new bootstrap.Modal(document.getElementById('modalUsuario'));
            modal.show();
        });

        window.addEventListener('hide-modals', () => {
            const modalEl = document.getElementById('modalUsuario');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        });
    </script>
    <!-- Script Para manejo de Notificaciones Tosatar -->
      @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

         <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });
            });
        </script>      
      @endpush
</div>
