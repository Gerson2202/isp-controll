<div>
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold text-primary">
                    📱 Envío Masivo de Recordatorios
                </h5>
                <span class="badge bg-info">
                    Límite diario: {{ $limiteDiario }}
                </span>
            </div>
        </div>

        <div class="card-body">
            <!-- Estadísticas -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="opacity-75">Total Pendientes</small>
                                    <h3 class="mb-0">{{ $total }}</h3>
                                </div>
                                <span class="fs-1 opacity-50">👥</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="opacity-75">Enviados Hoy</small>
                                    <h3 class="mb-0">{{ $enviadosHoy }}</h3>
                                    <small class="opacity-75">de {{ $limiteDiario }}</small>
                                </div>
                                <span class="fs-1 opacity-50">✅</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="opacity-75">Pendientes por Enviar</small>
                                    <h3 class="mb-0">{{ $pendientes }}</h3>
                                    <small class="opacity-75">clientes únicos</small>
                                </div>
                                <span class="fs-1 opacity-50">⏳</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="opacity-75">Último Envío</small>
                                    <h6 class="mb-0">
                                        {{ $fechaUltimoEnvio ? $fechaUltimoEnvio->diffForHumans() : 'Sin envíos' }}
                                    </h6>
                                    <small class="opacity-75">
                                        Este mes: {{ $mensajesEnviadosEsteMes }} mensajes
                                    </small>
                                </div>
                                <span class="fs-1 opacity-50">🕐</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <button wire:click="enviarMensajesMasivos" wire:loading.attr="disabled"
                    @if (!$puedeEnviar) disabled @endif
                    class="btn btn-success btn-lg flex-grow-1 
               @if (!$puedeEnviar) opacity-50 cursor-not-allowed @endif">
                    <span wire:loading.remove>
                        @if ($puedeEnviar)
                            🚀 Enviar recordatorio a todos
                        @else
                            📅 Disponible solo el día 4 de cada mes
                        @endif
                    </span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Procesando...
                    </span>
                </button>

                {{-- <button wire:click="verificarEnvioDiario" class="btn btn-outline-secondary">
                    🔄 Actualizar
                </button>

                @if (auth()->user() && auth()->user()->hasRole('admin'))
                    <button wire:click="resetearContadorMensual" 
                            class="btn btn-outline-danger"
                            onclick="return confirm('¿Estás seguro de resetear el contador mensual? Se eliminarán todos los registros del mes actual.')">
                        🔄 Resetear Mes
                    </button>
                @endif --}}
            </div>

            <!-- Barra de Progreso -->
            @if ($procesando)
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Progreso</span>
                        <span class="fw-bold">{{ $progreso }}%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                            style="width: {{ $progreso }}%">
                            {{ $progreso }}%
                        </div>
                    </div>
                </div>
            @endif

            <!-- Información del envío -->
            <div class="alert alert-info">
                <div class="d-flex">
                    <span class="me-3 fs-4">ℹ️</span>
                    <div>
                        <strong>Total Clientes pendientes:</strong> {{ $total }}
                        <span class="text-muted">| Límite diario: {{ $limiteDiario }}</span>
                        <br>
                        <small>
                            ⚡ Los datos se envían a n8n, donde se procesarán los mensajes con la plantilla de Meta
                        </small>
                        <br>
                        <small class="text-muted">
                            📝 Plantilla: <strong>recordatorio_pago</strong> (configurada en n8n)
                        </small>
                        <br>
                        <small class="text-success">
                            ✅ <strong>Control mensual:</strong> Cada cliente recibe SOLO 1 mensaje por mes
                        </small>
                    </div>
                </div>
            </div>

            <!-- Preview del mensaje -->
            <div class="card mb-4 border border-info">
                <div class="card-header bg-info ">
                    <h6 class="mb-0">👁️ Vista previa del mensaje</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="p-3 bg-white rounded border">
                        <p class="mb-0 text-muted" style="white-space: pre-line;">
                            👋 Hola <span class="text-primary fw-bold">{Nombre del Cliente}</span>.

                            📡 Te recordamos que tienes un saldo pendiente de <span
                                class="text-danger fw-bold">${Monto}</span> correspondiente a tu servicio de internet.

                            ⚠️ Te recomendamos realizar el pago oportunamente para mantener tu servicio activo.

                            ✅ Si ya realizaste el pago, por favor ignora este mensaje.

                            ¡Gracias! 😊
                        </p>
                    </div>
                    <div class="mt-2">
                        <span class="badge bg-primary me-1">{{ 1 }} = Nombre</span>
                        <span class="badge bg-success">{{ 2 }} = Monto</span>
                    </div>
                </div>
            </div>

            <!-- Lista de Clientes -->
            <div class="mt-4">
                <h6 class="fw-bold mb-3">
                    📋 Clientes Pendientes ({{ $total }})
                </h6>

                @if ($total > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Teléfono</th>
                                    <th>Monto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($clientes as $index => $cliente)
                                    @php
                                        $yaRecibio = false;
                                        if (isset($cliente['cliente_id'])) {
                                            $yaRecibio = \App\Models\EnvioMasivoLog::where(
                                                'cliente_id',
                                                $cliente['cliente_id'],
                                            )
                                                ->where('estado', 'enviado')
                                                ->where('fecha_envio', '>=', Carbon\Carbon::now()->startOfMonth())
                                                ->exists();
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $cliente['nombre'] }}</strong>
                                            <br>
                                            <small class="text-muted">ID: {{ $cliente['cliente_id'] ?? 'N/A' }}</small>
                                        </td>
                                        <td>{{ $cliente['telefono'] }}</td>
                                        <td class="fw-bold text-success">{{ $cliente['monto'] }}</td>
                                        <td>
                                            @if ($yaRecibio)
                                                <span class="badge bg-success">✅ Enviado este mes</span>
                                            @else
                                                <span class="badge bg-warning text-dark">⏳ Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="enviarPrueba({{ $index }})"
                                                wire:loading.attr="disabled" class="btn btn-outline-primary btn-sm"
                                                @if ($yaRecibio) disabled @endif>
                                                📨 Probar
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2 text-muted small">
                        Mostrando {{ count($clientes) }} de {{ $total }} clientes
                        <span class="ms-3 text-success">
                            ✅ Control mensual: 1 mensaje por cliente
                        </span>
                    </div>
                @else
                    <div class="text-center py-5">
                        <span class="display-1">✅</span>
                        <h5 class="mt-3 text-muted">No hay clientes pendientes</h5>
                        <p class="text-muted small">Todos los clientes ya recibieron su mensaje este mes 🎉</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
