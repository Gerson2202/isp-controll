<div>
    <div class="container-fluid py-1">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Nodo: {{ $nodo->nombre }}</h5>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $nodo->latitud }},{{ $nodo->longitud }}" 
                target="_blank" 
                class="btn btn-info btn-sm">
                    Ver en Google Maps
                </a>
            </div>

            <div class="card-body">
                {{-- Información del nodo --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p><strong>IP:</strong> {{ $nodo->ip }}</p>
                    </div>
                </div>

                {{-- Mapa embed de Google Maps --}}
                <div style="width: 100%; height: 200px;">
                    <iframe 
                        src="https://maps.google.com/maps?q={{ $nodo->latitud }},{{ $nodo->longitud }}&z=15&output=embed" 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        style="border:0;" 
                        allowfullscreen>
                    </iframe>
                </div>


                <hr>
                <h5 class="mb-3"><strong>Equipos asignados</strong></h5>

                @if ($nodo->inventarios->count() > 0)
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>ID</th>
                                    <th>Modelo</th>
                                    <th>Foto</th>
                                    <th>MAC</th>
                                    <th>Fecha</th>
                                    <th>Descripción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($nodo->inventarios as $inventario)
                                    <tr>
                                        <td>{{ $inventario->id }}</td>
                                        <td class="align-middle">
                                            @if ($inventario->modelo)
                                                <a href="{{ route('equipos.show', $inventario->id) }}" 
                                                target="_blank" 
                                                class="text-primary text-decoration-none fw-semibold hover-text">
                                                    {{ $inventario->modelo->nombre }}
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic">Sin modelo asignado</span>
                                            @endif
                                        </td>


                                        <td>
                                            @if ($inventario->modelo && $inventario->modelo->foto)
                                                <img src="{{ asset('storage/' . $inventario->modelo->foto) }}" 
                                                    alt="Foto del modelo" 
                                                    class="img-thumbnail mx-auto d-block" 
                                                    style="max-width: 80px;">
                                            @else
                                                <span class="text-muted">Sin foto</span>
                                            @endif
                                        </td>
                                        <td>{{ $inventario->mac }}</td>
                                        <td>{{ \Carbon\Carbon::parse($inventario->fecha)->format('d/m/Y') }}</td>
                                        <td>{{ $inventario->descripcion }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">No hay equipos asignados a este nodo.</div>
                @endif

            </div>
        </div>
    </div>
    
</div>
