<div>
    <div class="container-fluid min-vh-100 d-flex flex-column">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tickets Abiertos</h4>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" placeholder="Buscar..."
                                wire:model.live.debounce.300ms="search">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <select class="form-select" style="width: auto;" wire:model.live="perPage">
                            <option value="10">10 por página</option>
                            <option value="25">25 por página</option>
                            <option value="50">50 por página</option>
                            <option value="100">100 por página</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="ticketsTable">
                        <thead class="table-light">
                            <tr>
                                <th wire:click="sortBy('tipo_reporte')">Tipo de Reporte</th>
                                <th wire:click="sortBy('situacion')">Situación</th>
                                <th>Cliente</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td>{{ $ticket->tipo_reporte }}</td>
                                    <td>{{ $ticket->situacion }}</td>
                                    <td>{{ $ticket->cliente->nombre }}</td>
                                    <td>
                                        <a href="{{ route('tickets.edit', $ticket->id) }}"
                                            class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No se encontraron tickets abiertos</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>


                {{-- Paginación --}}
                <div class="mt-4 d-flex justify-content-center">
                    {{ $tickets->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
                </div>
            </div>
        </div>
    </div>


    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
        <script>
            document.addEventListener('livewire:load', function() {
                $('#ticketsTable').DataTable({
                    responsive: true,
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
                    },
                    paging: false, // Desactivamos la paginación de DataTables porque usamos Livewire
                    searching: false, // Desactivamos la búsqueda de DataTables
                    info: false, // Desactivamos el info de DataTables
                });
            });

            Livewire.on('closeModal', () => {
                $('#editTicketModal').modal('hide');
            });
        </script>
    @endpush

    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
    @endpush
</div>
