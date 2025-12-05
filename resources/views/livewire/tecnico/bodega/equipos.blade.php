<div>
    <h5 class="mb-3">
        <i class="fas fa-network-wired me-2 text-primary"></i>
        Equipos Asignados
    </h5>

    {{-- 🔍 Buscador --}}
    <input type="text" wire:model.live.debounce.500ms="buscar" class="form-control mb-4"
        placeholder="Buscar por modelo, MAC o serial...">

    {{-- 🔸 Equipos del técnico --}}
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-primary text-white fw-bold py-2">Mis equipos</div>
        <div class="card-body p-0">
            @if ($equiposPropios->count())
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-sm table-hover align-middle mb-0">
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
                            @foreach ($equiposPropios as $equipo)
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
                </div>

                {{-- Paginación --}}
                <div class="mt-3 d-flex justify-content-center">
                    {{ $equiposPropios->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
                </div>
            @else
                <div class="p-3 text-muted">No tienes equipos asignados directamente.</div>
            @endif
        </div>
    </div>

    {{-- 🔹 Equipos por bodega --}}
    @foreach ($bodegas as $bodega)
        @php
            $equipos = $bodega->inventarios
                ->filter(fn($item) =>
                    str_contains(strtolower($item->mac ?? ''), strtolower($buscar)) ||
                    str_contains(strtolower($item->serial ?? ''), strtolower($buscar)) ||
                    str_contains(strtolower($item->modelo->nombre ?? ''), strtolower($buscar))
                );
        @endphp

        <div class="card shadow-sm mb-4 border-0">
            <div class="card-header bg-secondary text-white fw-bold py-2">
                Bodega: {{ $bodega->nombre }}
            </div>
            <div class="card-body p-0">
                @if ($equipos->count())
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover align-middle mb-0">
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
                    </div>
                @else
                    <div class="p-3 text-muted">No hay equipos en esta bodega.</div>
                @endif
            </div>
        </div>
    @endforeach
</div>
