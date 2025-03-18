<div>
    <!-- Botón para abrir el modal -->
    <button wire:click="show" class="btn btn-primary">Abrir Modal</button>

    <!-- Modal -->
    <div class="modal fade @if($showModal) show @endif" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: @if($showModal) block @else none @endif;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Título del Modal</h5>
                    <button wire:click="hide" type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Aquí va el contenido de tu modal.
                </div>
                <div class="modal-footer">
                    <button wire:click="hide" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Script de Bootstrap para manejar el modal -->
    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function () {
            // Escuchar la actualización del modal
            @this.on('showModal', () => {
                $('#exampleModal').modal('show');
            });

            @this.on('hideModal', () => {
                $('#exampleModal').modal('hide');
            });
        });
    </script>
    @endpush
</div>

