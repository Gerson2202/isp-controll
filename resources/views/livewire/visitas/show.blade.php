<div class="card shadow h-100 d-flex flex-column rounded-0">
    <!-- Encabezado -->
    <div class="card-header bg-primary text-white rounded-0">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Detalles de la Visita</h3>
            <a href="{{ route('visitas.tabla') }}" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Cuerpo -->
    <div class="card-body flex-grow-1 overflow-auto p-4">
        <!-- Información básica -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2">Información de la Visita</h5>
                <dl class="row">
                    <dt class="col-sm-4">ID:</dt>
                    <dd class="col-sm-8">{{ $visita->id }}</dd>

                    <dt class="col-sm-4">Estado:</dt>
                    <dd class="col-sm-8">
                        <span
                            class="badge bg-{{ $visita->estado == 'Completada' ? 'success' : ($visita->estado == 'En progreso' ? 'warning' : 'danger') }}">
                            {{ $visita->estado }}
                        </span>
                    </dd>

                    <dt class="col-sm-4">Fecha Creación:</dt>
                    <dd class="col-sm-8">{{ $visita->created_at->format('d/m/Y H:i') }}</dd>
                </dl>
            </div>

            <div class="col-md-6">
                <h5 class="border-bottom pb-2">Descripción y Solución</h5>
                <p><strong>Descripción:</strong> {{ $visita->descripcion ?? 'No registrada' }}</p>
                <p><strong>Solución:</strong> {{ $visita->solucion ?? 'No registrada' }}</p>
                <p><strong>Observación:</strong>
                    @if ($visita->observacion)
                        {!! preg_replace(
                            ['/Equipos instalados:/', '/Equipos retirados:/'],
                            [
                                '<span class="badge bg-success">Equipos instalados:</span>',
                                '<span class="badge bg-danger">Equipos retirados:</span>',
                            ],
                            $visita->observacion,
                        ) !!}
                    @else
                        No registrada
                    @endif
                </p>
            </div>
        </div>

        <!-- Usuarios asignados -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="border-bottom pb-2">Técnicos Asignados</h5>
                @if ($visita->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Cierre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visita->users as $usuario)
                                    <tr>
                                        <td>
                                            <i class="fas fa-user text-primary me-2"></i>
                                            {{ $usuario->name }}
                                        </td>
                                        <td>
                                            {{ $usuario->pivot->fecha_inicio
                                                ? \Carbon\Carbon::parse($usuario->pivot->fecha_inicio)->format('d/m/Y H:i')
                                                : 'Sin iniciar' }}
                                        </td>
                                        <td>
                                            {{ $usuario->pivot->fecha_cierre
                                                ? \Carbon\Carbon::parse($usuario->pivot->fecha_cierre)->format('d/m/Y H:i')
                                                : 'Sin cerrar' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        No hay técnicos asignados a esta visita.
                    </div>
                @endif
            </div>
        </div>

        <!-- Información del ticket -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="border-bottom pb-2">Información del Ticket Relacionado</h5>
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tipo</th>
                            <th>Situación</th>
                            <th>Estado</th>
                            <th>Solución</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $visita->ticket->id ?? 'S/N' }}</td>
                            <td>{{ $visita->ticket?->tipo_reporte ?? 'S/N' }}</td>
                            <td>{{ $visita->ticket?->situacion ?? 'S/N' }}</td>
                            <td>
                                @if ($visita->ticket)
                                    <span
                                        class="badge bg-{{ $visita->ticket->estado == 'cerrado' ? 'success' : 'warning' }}">
                                        {{ ucfirst($visita->ticket->estado) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">S/N</span>
                                @endif
                            </td>
                            <td>{{ $visita->ticket?->solucion ?? 'No registrada' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Información del cliente -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="border-bottom pb-2">Información del Cliente</h5>
                <div class="d-flex align-items-center">
                    <div class="me-3 bg-light rounded-circle p-3">
                        <i class="fas fa-user fa-2x text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">
                            <a href="{{ route('clientes.show', $visita->ticket->cliente->id ?? '') }}"
                                class="text-decoration-none">
                                {{ $visita->ticket->cliente->nombre ?? 'Cliente no especificado' }}
                            </a>
                        </h5>
                        <p class="mb-1 text-muted">
                            <i class="fas fa-id-card"></i> ID: {{ $visita->ticket->cliente->id ?? 'N/A' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <!-- Equipos asignados -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="border-bottom pb-2">Equipos Asignados</h5>
                @if ($visita->inventarios->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Modelo</th>
                                    <th>MAC</th>
                                    <th>Serial</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visita->inventarios as $equipo)
                                    <tr>
                                        <td>{{ $equipo->modelo->nombre ?? 'Sin modelo' }}</td>
                                        <td>{{ $equipo->mac ?? '-' }}</td>
                                        <td>{{ $equipo->serial ?? '-' }}</td>
                                        <td>{{ $equipo->descripcion }}</td>
                                        <td>{{ $equipo->fecha ? \Carbon\Carbon::parse($equipo->fecha)->format('d/m/Y') : '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        No hay equipos asignados a esta visita.
                    </div>
                @endif
            </div>
        </div>
        <!-- Consumibles asignados -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="border-bottom pb-2">Consumibles Usados</h5>
                @if ($visita->consumibleStock->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Unidad</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($visita->consumibleStock as $item)
                                    <tr>
                                        <td>{{ $item->consumible->nombre }}</td>
                                        <td>{{ $item->consumible->unidad ?? '-' }}</td>
                                        <td>{{ $item->cantidad }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        No hay consumibles asociados a esta visita.
                    </div>
                @endif
            </div>
        </div>
        <hr>
        <!-- Galería -->
        <div class="row">
            <div class="col-12">
                <h5 class="border-bottom pb-2">Galeria de fotos</h5>
                @if ($visita->fotos->count() > 0)
                    <div class="row">
                        @foreach ($visita->fotos as $foto)
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <a href="{{ asset('storage/' . $foto->ruta) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $foto->ruta) }}" class="card-img-top"
                                            style="height:200px;object-fit:cover;">
                                    </a>
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $foto->nombre_original }}</h6>
                                        <p class="card-text text-muted small">
                                            {{ $foto->descripcion ?? 'Sin descripción' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">No hay fotos registradas para esta visita.</div>
                @endif
            </div>
        </div>

    </div>

    <!-- Pie -->
    {{-- <div class="card-footer bg-light rounded-0">
            <div class="d-flex justify-content-between">
                <a href="{{ route('visitas.edit', $visita->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
        </div> --}}
</div>
