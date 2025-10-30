<div>
    <h5><i class="fas fa-boxes me-2 text-success"></i> Consumibles Asignados</h5>

    <input type="text" wire:model.live.debounce.500ms="buscar" class="form-control mb-3"
           placeholder="Buscar por nombre de consumible...">

    @if ($consumibles->count())
        <table class="table table-sm table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($consumibles as $item)
                    <tr>
                        <td>{{ $item->consumible->nombre ?? 'Sin nombre' }}</td>
                        <td>{{ $item->consumible->descripcion ?? '-' }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>{{ $item->consumible->unidad ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $consumibles->links() }}
    @else
        <div class="alert alert-info">No se encontraron consumibles.</div>
    @endif
</div>
