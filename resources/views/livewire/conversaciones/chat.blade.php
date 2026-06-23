<div class="chat-container">
    <div class="row g-0 h-100">

        <!-- SIDEBAR -->
        <div class="col-12 col-md-4 col-lg-3 border-end p-0 d-flex flex-column bg-white" style="height: 100%;">

            <!-- Cabecera - fija -->
            <div class="bg-success text-white p-3 d-flex align-items-center justify-content-between flex-shrink-0">
                <div class="d-flex align-items-center">
                    <i class="fab fa-whatsapp fa-2x me-2"></i>
                    <strong class="fs-5">Chats</strong>
                </div>
                <span class="badge bg-light text-success">{{ $conversaciones->count() }}</span>
            </div>

            <!-- Buscador - fija -->
            <div class="p-2 border-bottom bg-white flex-shrink-0">
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control bg-light border-0" placeholder="Buscar conversación..."
                        wire:model.live="search" wire:keyup="buscarConversaciones">
                </div>
            </div>

            <!-- Lista de conversaciones - con scroll -->
            <div class="flex-grow-1 overflow-auto" style="overflow-y: auto;">
                <div class="list-group list-group-flush">

                    @forelse($conversaciones as $conversacion)
                        @php
                            $nombre = $conversacion->nombre_contacto ?? ($conversacion->cliente->nombre ?? 'Usuario');
                            $iniciales = strtoupper(substr($nombre, 0, 2));
                            $colors = ['4CAF50', '9E9E9E', '2196F3', 'FF9800', '9C27B0', 'F44336'];
                            $color = $colors[$conversacion->id % count($colors)];
                            $ultimoMensaje = $conversacion->mensajes->first();
                        @endphp

                        <a href="#"
                            class="list-group-item list-group-item-action border-0 py-3 {{ $conversacionActiva && $conversacionActiva->id == $conversacion->id ? 'active' : '' }}"
                            wire:click.prevent="seleccionarConversacion({{ $conversacion->id }})"
                            wire:key="conv-{{ $conversacion->id }}">

                            <div class="d-flex align-items-center">
                                <div class="position-relative me-2 flex-shrink-0">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($nombre) }}&background={{ $color }}&color=fff&size=40"
                                        class="rounded-circle" width="45" height="45" alt="{{ $nombre }}">
                                    <span
                                        class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                                        style="width: 12px; height: 12px;"></span>
                                </div>
                                <div class="flex-grow-1 ms-2" style="min-width: 0;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-truncate">{{ $nombre }}</span>
                                        <small class="text-muted flex-shrink-0 ms-2">
                                            {{ $conversacion->ultima_actividad ? \Carbon\Carbon::parse($conversacion->ultima_actividad)->format('H:i') : '' }}
                                        </small>
                                    </div>
                                    <div>
                                        <small class="text-truncate text-muted d-block" style="max-width: 100%;">
                                            {{ $ultimoMensaje?->mensaje ?? 'Sin mensajes' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay conversaciones</p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>

        <!-- ÁREA DEL CHAT -->
        <div class="col-12 col-md-8 col-lg-9 d-flex flex-column p-0 bg-light" style="height: 100%;">

            @if ($conversacionActiva)
                @php
                    $nombreActivo = $conversacionActiva->nombre_contacto ?? ($clienteActivo->nombre ?? 'Usuario');
                    $colors = ['4CAF50', '9E9E9E', '2196F3', 'FF9800', '9C27B0'];
                    $colorActivo = $colors[$conversacionActiva->id % count($colors)];
                    $estadoTexto =
                        $conversacionActiva->estado == 'abierto'
                            ? 'En línea'
                            : ($conversacionActiva->estado == 'cerrado'
                                ? 'Desconectado'
                                : 'Conectado');
                @endphp

                <!-- Cabecera del chat - fija -->
                <div class="bg-white p-3 d-flex align-items-center border-bottom flex-shrink-0">
                    <div class="position-relative me-3 flex-shrink-0">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($nombreActivo) }}&background={{ $colorActivo }}&color=fff&size=40"
                            class="rounded-circle" width="45" height="45" alt="{{ $nombreActivo }}">
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle"
                            style="width: 12px; height: 12px;"></span>
                    </div>
                    <div style="min-width: 0;">
                        <div class="fw-bold text-truncate">
                            {{ $nombreActivo }}
                            @if ($conversacionActiva->telefono)
                                <span class="text-muted fw-normal">
                                    ({{ $conversacionActiva->telefono }})
                                </span>
                            @endif
                        </div> <small class="text-success">
                            <i class="fas fa-circle fa-xs me-1"></i>
                            {{ $estadoTexto }}
                        </small>
                    </div>
                </div>

                <!-- Mensajes - con scroll -->
                <div class="flex-grow-1 p-3 overflow-auto" id="messageArea"
                    style="background: #f8f9fa; overflow-y: auto;">

                    @php
                        $fechaActual = null;
                    @endphp

                    @forelse($mensajes as $mensaje)
                        @php
                            $fechaMensaje = \Carbon\Carbon::parse($mensaje->fecha_mensaje);
                            $fechaFormateada = $fechaMensaje->format('Y-m-d');

                            if ($fechaActual != $fechaFormateada) {
                                $fechaActual = $fechaFormateada;
                                if ($fechaMensaje->isToday()) {
                                    $fechaMostrar = 'Hoy';
                                } elseif ($fechaMensaje->isYesterday()) {
                                    $fechaMostrar = 'Ayer';
                                } else {
                                    $fechaMostrar = $fechaMensaje->locale('es')->isoFormat('dddd D [de] MMMM');
                                }
                            }
                        @endphp

                        @if (isset($fechaMostrar))
                            <div class="text-center mb-3">
                                <span
                                    class="bg-white px-3 py-1 rounded-pill small text-muted shadow-sm">{{ $fechaMostrar }}</span>
                            </div>
                            @php unset($fechaMostrar); @endphp
                        @endif

                        <!-- Mensaje -->
                        <div class="d-flex {{ $mensaje->tipo == 'cliente' ? '' : 'justify-content-end' }} mb-2">
                            <div class="p-2 rounded-3 shadow-sm {{ $mensaje->tipo == 'cliente' ? 'bg-white' : 'bg-success text-white' }}"
                                style="max-width: 70%; {{ $mensaje->tipo == 'cliente' ? 'border-top-left-radius: 4px;' : 'border-top-right-radius: 4px;' }}">
                                {{ $mensaje->mensaje }}
                                <div class="text-end mt-1">
                                    <small
                                        style="font-size: 0.65rem; {{ $mensaje->tipo == 'cliente' ? 'color: #6c757d;' : 'opacity: 0.8;' }}">
                                        {{ \Carbon\Carbon::parse($mensaje->fecha_mensaje)->format('H:i') }}
                                        @if ($mensaje->tipo != 'cliente')
                                            <i class="fas fa-check-double ms-1"></i>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="far fa-comment-dots fa-4x text-muted mb-3"></i>
                            <p class="text-muted">No hay mensajes en esta conversación</p>
                        </div>
                    @endforelse

                </div>
            @else
                <!-- Estado sin conversación seleccionada -->
                <div class="d-flex flex-column justify-content-center align-items-center h-100 bg-light">
                    <i class="far fa-comment-dots fa-5x text-muted mb-4"></i>
                    <h5 class="text-muted">Selecciona una conversación</h5>
                    <p class="text-muted small">Elige un chat para ver los mensajes</p>
                </div>
            @endif

        </div>

    </div>
</div>
