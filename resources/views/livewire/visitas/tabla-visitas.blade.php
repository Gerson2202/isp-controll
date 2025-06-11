<div class="container-fluid">
    <div class="card">
        <div class="card-body">
    <!-- Barra de búsqueda y paginación -->
    <div class="row mb-3">
        <div class="col-md-6">
            <div class="input-group">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Buscar por cliente, estado o número de ticket..." 
                    class="form-control"
                >
                <div class="input-group-append">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de visitas -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover ">
            <thead class="thead-dark">
                <tr>
                    <th wire:click="sortBy('fecha_inicio')" style="cursor: pointer;">
                        Fecha Inicio
                        @if($sortField === 'fecha_inicio')
                            @if($sortDirection === 'asc')
                                <i class="fas fa-sort-up ml-1"></i>
                            @else
                                <i class="fas fa-sort-down ml-1"></i>
                            @endif
                        @endif
                    </th>
                    <th wire:click="sortBy('fecha_cierre')" style="cursor: pointer;">
                        Fecha Cierre
                        @if($sortField === 'fecha_cierre')
                            @if($sortDirection === 'asc')
                                <i class="fas fa-sort-up ml-1"></i>
                            @else
                                <i class="fas fa-sort-down ml-1"></i>
                            @endif
                        @endif
                    </th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th>Ticket</th>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($visitas as $visita)
                    <tr>
                        <td>
                            @if($visita->fecha_inicio)
                                {{ \Carbon\Carbon::parse($visita->fecha_inicio )->format('d/m/Y H:i') }}
                            @else
                                Sin agendar
                            @endif
                        </td>
                        <td>
                            @if($visita->fecha_cierre)
                                {{ \Carbon\Carbon::parse($visita->fecha_cierre)->format('d/m/Y H:i') }}
                            @else
                                Sin agendar
                            @endif
                        </td>
                        <td data-toggle="tooltip" title="{{ $visita->descripcion }}">
                            {{ Str::limit($visita->descripcion ?? 'sin descripcion', 50) }}
                        </td>
                        <td>
                            <span class="badge 
                                @if($visita->estado == 'Completada') badge-success
                                @elseif($visita->estado == 'En progreso') badge-warning
                                @elseif($visita->estado == 'Pendiente') badge-danger
                                @else badge-secondary @endif">
                                {{ ucfirst($visita->estado) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-primary">
                                #{{ $visita->ticket->id }}
                            </span>
                        </td>
                        <td>{{ $visita->ticket->cliente->nombre }}</td>
                        <td>
                            <a href="{{ route('visitas.show', $visita->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                            <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            No se encontraron visitas registradas
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="row">
        <div class="mt-3">
            {{ $visitas->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
        </div>
    </div>
        </div>
    </div>
    
</div>

@push('scripts')
<script>
$(function () {
    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Livewire hook para reiniciar tooltips después de actualización
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', () => {
            $('[data-toggle="tooltip"]').tooltip('dispose').tooltip();
        });
    });
});
</script>
@endpush