<div>
    <div class="container-fluid px-0">
        <!-- Debug info (remover después) -->
        <div class="alert alert-info d-none">
            <small>
                <strong>Debug:</strong>
                Hoy: {{ count($actividadesHoy) }} |
                Mañana: {{ count($actividadesManana) }} |
                Usuario: {{ Auth::id() }}
            </small>
        </div>

        <!-- Header optimizado para móvil -->
        <div class="bg-primary text-white py-3 px-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-calendar-day fs-4 me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="mb-0 fw-bold">Cronograma de Actividades</h5>
                    <small class="opacity-75">Técnico: {{ Auth::user()->name ?? 'Usuario' }}</small>
                </div>
                <button class="btn btn-sm btn-light" wire:click="cargarActividades" title="Actualizar">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="px-3 py-3">
            <!-- Hoy -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-primary mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-check me-2"></i>
                        <span>Hoy - {{ $fechaHoyFormateada }}</span>
                    </h6>
                    <span class="badge bg-primary">{{ count($actividadesHoy) }}</span>
                </div>

                @if (count($actividadesHoy) > 0)
                    <div class="row g-2">
                        @foreach ($actividadesHoy as $actividad)
                            <div class="col-12">
                                <div class="card border-0 shadow-sm mb-2"
                                    style="border-left: 4px solid var(--bs-{{ $actividad['estado_color'] }})">
                                    <div class="card-body p-3">
                                        <!-- Header de la actividad -->
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <div class="fw-bold text-dark mb-1">{{ $actividad['titulo'] }}</div>
                                                <div class="badge bg-{{ $actividad['estado_color'] }} mb-2">
                                                    {{ $actividad['estado'] }}
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="text-primary fw-bold">{{ $actividad['hora_inicio'] }}</div>
                                                <small class="text-muted">Hora inicio</small>
                                            </div>
                                        </div>

                                        <!-- Información básica -->
                                        <div class="mb-2">
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $actividad['hora_inicio'] }} - {{ $actividad['hora_fin'] }}
                                            </small>

                                            @if ($actividad['cliente'])
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-person text-primary me-2"></i>
                                                    <div>
                                                        <small
                                                            class="fw-semibold d-block">{{ $actividad['cliente']['nombre'] }}</small>
                                                        <small class="text-muted">
                                                            <i
                                                                class="bi bi-telephone me-1"></i>{{ $actividad['cliente']['telefono'] }}
                                                        </small>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Botones de acción -->
                                        <div class="d-flex justify-content-between mt-3">
                                            <div>
                                                @if ($actividad['ticket'])
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-ticket-detailed me-1"></i>Ticket
                                                        #{{ $actividad['ticket']['id'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <button class="btn btn-sm btn-outline-primary"
                                                wire:click="verDetalles({{ $actividad['id'] }})">
                                                <i class="bi bi-eye me-1"></i> Detalles
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 bg-light rounded">
                        <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mb-2">No hay actividades para hoy</h6>
                        <p class="text-muted small">No se encontraron visitas asignadas</p>
                    </div>
                @endif
            </div>

            <!-- Botón para ver mañana -->
            <div class="text-center mb-4">
                <button class="btn btn-info w-100 py-2" wire:click="toggleManana">
                    <i class="bi bi-calendar-week me-2"></i>
                    {{ $mostrarManana ? 'Ocultar' : 'Ver' }} Actividades de Mañana
                    @if (count($actividadesManana) > 0)
                        <span class="badge bg-light text-dark ms-2">{{ count($actividadesManana) }}</span>
                    @endif
                </button>
            </div>

            <!-- Mañana (Colapsable) -->
            @if ($mostrarManana)
                <div class="border-top pt-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-info mb-0 d-flex align-items-center">
                            <i class="bi bi-calendar-week me-2"></i>
                            <span>Mañana - {{ $fechaMananaFormateada }}</span>
                        </h6>
                        <span class="badge bg-info">{{ count($actividadesManana) }}</span>
                    </div>

                    @if (count($actividadesManana) > 0)
                        <div class="row g-2">
                            @foreach ($actividadesManana as $actividad)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm mb-2"
                                        style="border-left: 4px solid var(--bs-{{ $actividad['estado_color'] }})">
                                        <div class="card-body p-3">
                                            <!-- Header de la actividad -->
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <div class="fw-bold text-dark mb-1">{{ $actividad['titulo'] }}
                                                    </div>
                                                    <div class="badge bg-{{ $actividad['estado_color'] }} mb-2">
                                                        {{ $actividad['estado'] }}
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="text-primary fw-bold">{{ $actividad['hora_inicio'] }}
                                                    </div>
                                                    <small class="text-muted">Hora inicio</small>
                                                </div>
                                            </div>

                                            <!-- Información básica -->
                                            <div class="mb-2">
                                                <small class="text-warning mb-2 d-block">
                                                    <i class="bi bi-info-circle me-1"></i>Programada para mañana
                                                </small>

                                                @if ($actividad['cliente'])
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-person text-primary me-2"></i>
                                                        <div>
                                                            <small
                                                                class="fw-semibold d-block">{{ $actividad['cliente']['nombre'] }}</small>
                                                            <small class="text-muted">
                                                                <i
                                                                    class="bi bi-telephone me-1"></i>{{ $actividad['cliente']['telefono'] }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Botones de acción -->
                                            <div class="d-flex justify-content-between mt-3">
                                                <div>
                                                    @if ($actividad['ticket'])
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-ticket-detailed me-1"></i>Ticket
                                                            #{{ $actividad['ticket']['id'] }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <button class="btn btn-sm btn-outline-primary"
                                                    wire:click="verDetalles({{ $actividad['id'] }})">
                                                    <i class="bi bi-eye me-1"></i> Detalles
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 bg-light rounded">
                            <i class="bi bi-calendar-x text-muted mb-3" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mb-2">No hay actividades para mañana</h6>
                            <p class="text-muted small">No se encontraron visitas asignadas</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Footer con estados -->
        <div class="bg-white border-top p-3">
            <div class="d-flex justify-content-around">
                <div class="text-center">
                    <span class="badge bg-warning">Pendiente</span>
                    <small class="d-block text-muted">Por hacer</small>
                </div>
                <div class="text-center">
                    <span class="badge bg-info">En progreso</span>
                    <small class="d-block text-muted">En ejecución</small>
                </div>
                <div class="text-center">
                    <span class="badge bg-success">Completada</span>
                    <small class="d-block text-muted">Finalizada</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles (optimizado para móvil) -->
    @if ($mostrarModal && $actividadSeleccionada)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);"
            wire:click="cerrarModal">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" @click.stop>
                <div class="modal-content rounded-0 border-0" style="max-height: 90vh; overflow-y: auto;">
                    <!-- Header del modal -->
                    <div class="modal-header bg-primary text-white py-3">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-info-circle me-2"></i>
                            Detalles de la Visita
                        </h6>
                        <button type="button" class="btn-close btn-close-white" wire:click="cerrarModal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        <!-- Información de la visita -->
                        <div class="p-3 border-bottom">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-calendar-event me-2"></i>Información de la Visita
                            </h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <small class="text-muted d-block">Título</small>
                                    <strong>{{ $actividadSeleccionada['titulo'] }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Estado</small>
                                    <span class="badge bg-{{ $actividadSeleccionada['estado_color'] }}">
                                        {{ $actividadSeleccionada['estado'] }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Hora inicio</small>
                                    <strong>{{ $actividadSeleccionada['hora_inicio'] }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block">Hora fin</small>
                                    <strong>{{ $actividadSeleccionada['hora_fin'] }}</strong>
                                </div>
                                @if ($actividadSeleccionada['descripcion'])
                                    <div class="col-12 mt-2">
                                        <small class="text-muted d-block">Descripción</small>
                                        <p class="mb-0">{{ $actividadSeleccionada['descripcion'] }}</p>
                                    </div>
                                @endif
                                @if ($actividadSeleccionada['observacion'])
                                    <div class="col-12 mt-2">
                                        <small class="text-muted d-block">Observación</small>
                                        <p class="mb-0">{{ $actividadSeleccionada['observacion'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Información del cliente -->
                        @if ($actividadSeleccionada['cliente'])
                            <div class="p-3 border-bottom">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-person me-2"></i>Información del Cliente
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Nombre</small>
                                        <strong>{{ $actividadSeleccionada['cliente']['nombre'] }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Teléfono</small>
                                        <strong>{{ $actividadSeleccionada['cliente']['telefono'] }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Cédula</small>
                                        <strong>{{ $actividadSeleccionada['cliente']['cedula'] ?? 'N/A' }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Estado</small>
                                        <span
                                            class="badge {{ $actividadSeleccionada['cliente']['estado'] == 'activo' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $actividadSeleccionada['cliente']['estado'] }}
                                        </span>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <small class="text-muted d-block">Dirección</small>
                                        <strong>{{ $actividadSeleccionada['cliente']['direccion'] }}</strong>

                                        @if ($actividadSeleccionada['cliente']['latitud'] && $actividadSeleccionada['cliente']['longitud'])
                                            <div class="mt-2">
                                                <a href="https://www.google.com/maps?q={{ $actividadSeleccionada['cliente']['latitud'] }},{{ $actividadSeleccionada['cliente']['longitud'] }}"
                                                    target="_blank" class="btn btn-sm btn-info">
                                                   Ver <i class="bi bi-geo-alt me-1"></i> 
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    @if ($actividadSeleccionada['cliente']['punto_referencia'])
                                        <div class="col-12 mt-2">
                                            <small class="text-muted d-block">Punto de referencia</small>
                                            <strong>{{ $actividadSeleccionada['cliente']['punto_referencia'] }}</strong>
                                        </div>
                                    @endif
                                    @if ($actividadSeleccionada['cliente']['ip'])
                                        <div class="col-12 mt-2">
                                            <small class="text-muted d-block">IP asignada</small>
                                            <code
                                                class="bg-light p-1 rounded">{{ $actividadSeleccionada['cliente']['ip'] }}</code>
                                        </div>
                                    @endif
                           
                                </div>
                            </div>
                        @endif

                        <!-- Información del ticket -->
                        @if ($actividadSeleccionada['ticket'])
                            <div class="p-3">
                                <h6 class="fw-bold text-primary mb-3">
                                    <i class="bi bi-ticket-detailed me-2"></i>Información del Ticket
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">Ticket ID</small>
                                        <strong>#{{ $actividadSeleccionada['ticket']['id'] }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">Tipo de reporte</small>
                                        <strong>{{ $actividadSeleccionada['ticket']['tipo_reporte'] }}</strong>
                                    </div>
                                    <div class="col-12 mt-2">
                                        <small class="text-muted d-block">Situación</small>
                                        <p class="mb-0">{{ $actividadSeleccionada['ticket']['situacion'] }}</p>
                                    </div>
                                    @if ($actividadSeleccionada['ticket']['solucion'])
                                        <div class="col-12 mt-2">
                                            <small class="text-muted d-block">Solución</small>
                                            <p class="mb-0">{{ $actividadSeleccionada['ticket']['solucion'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" wire:click="cerrarModal">
                            <i class="bi bi-x-circle me-1"></i>Cerrar
                        </button>
                        @if (
                            $actividadSeleccionada['cliente'] &&
                                $actividadSeleccionada['cliente']['latitud'] &&
                                $actividadSeleccionada['cliente']['longitud']
                        )
                            <a href="https://www.google.com/maps?q={{ $actividadSeleccionada['cliente']['latitud'] }},{{ $actividadSeleccionada['cliente']['longitud'] }}"
                                target="_blank" class="btn btn-success">
                                <i class="bi bi-map me-1"></i>Abrir en Maps
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
