<div>
    <h5><i class="fas fa-network-wired me-2 text-primary"></i> Equipos Asignados</h5>

    <input type="text" wire:model.live.debounce.500ms="buscar" class="form-control mb-3"
           placeholder="Buscar por modelo, MAC o serial...">

    @if ($equipos->count())
        <div class="table-responsive">
            <table class="table table-sm table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Imagen</th>
                        <th>Modelo</th>
                        <th>MAC</th>
                        <th>Serial</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($equipos as $equipo)
                        <tr>
                            <td>
                                <img src="{{ $equipo->modelo->foto 
                                    ? asset('storage/'.$equipo->modelo->foto)
                                    : asset('images/no-image.png') }}"
                                    width="50" height="50" class="rounded border" style="object-fit: cover;">
                            </td>
                            <td>{{ $equipo->modelo->nombre ?? 'Sin modelo' }}</td>
                            <td>{{ $equipo->mac ?? 'N/A' }}</td>
                            <td>{{ $equipo->serial ?? 'N/A' }}</td>
                            <td>{{ optional($equipo->fecha)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ $equipo->descripcion }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $equipos->links() }}
        </div>
    @else
        <div class="alert alert-info">No se encontraron equipos.</div>
    @endif
</div>
