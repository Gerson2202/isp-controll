<div>
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bi bi-calendar2-check me-2"></i> Toda La Programación
            </h5>
        </div>

        <div class="card-body">
            {{-- Buscador --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" class="form-control"
                        placeholder="Buscar por cliente, número de ticket o descripción..." wire:model.live="search">
                </div>
                <div class="col-md-3">
                    <select wire:model.live="perPage" class="form-select">
                        <option value="5">5 por página</option>
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                    </select>
                </div>
            </div>

            {{-- Tabla --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('id')" style="cursor:pointer;">
                                ID
                                @if ($sortField === 'id')
                                    <i
                                        class="bi bi-caret-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-fill text-secondary"></i>
                                @endif
                            </th>
                            <th>Cliente</th>
                            <th>N° Ticket</th>
                            <th>Descripción</th>
                            <th>Usuario asignado</th>
                            <th>Fecha inicio</th>
                            <th>Fecha cierre</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visitas as $visita)
                            @php
                                $usuario = $visita->usuarios->first();
                            @endphp
                            <tr>
                                <td>{{ $visita->id }}</td>
                                <td>{{ $visita->ticket->cliente->nombre ?? '—' }}</td>
                                <td>{{ $visita->ticket->id ?? '—' }}</td>
                                <td>{{ Str::limit($visita->descripcion ?? 'Sin descripción', 50) }}</td>
                                <td>
                                    @if ($usuario)
                                        <span class="badge bg-success">{{ $usuario->name }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Sin asignar</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($usuario && $usuario->pivot->fecha_inicio)
                                        {{ \Carbon\Carbon::parse($usuario->pivot->fecha_inicio)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Sin agendar</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($usuario && $usuario->pivot->fecha_cierre)
                                        {{ \Carbon\Carbon::parse($usuario->pivot->fecha_cierre)->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Sin agendar</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($visita->estado === 'Pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif ($visita->estado === 'En progreso')
                                        <span class="badge bg-primary">En progreso</span>
                                    @elseif ($visita->estado === 'Completada')
                                        <span class="badge bg-success">Completada</span>
                                    @else
                                        <span class="badge bg-secondary">{{ $visita->estado }}</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    <a href="{{ route('visitas.show', $visita->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> Ver Detalle
                                    </a>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    No se encontraron resultados...
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $visitas->onEachSide(1)->links('vendor.livewire.simple-pagination') }}
            </div>

        </div>
    </div>
</div>
