<div>
    <div class="container-fuid">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-money-bill-wave me-2"></i>Registrar Pago</h5>
            </div>
            <div class="card-body">
                <!-- Buscador de facturas -->
                <div class="mb-4">
                    <label class="form-label">Buscar Factura</label>
                    <input type="text" wire:model.live="search" class="form-control" placeholder="N° Factura o Cliente">
                </div>
    
                <!-- Facturas pendientes -->
                @if($facturas->count())
                   <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>N° Factura</th>
                                    <th>Cliente</th>
                                    <th>Mes</th>
                                    <th>Total</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($facturas as $factura)
                                <tr>
                                    <td data-label="N° Factura">{{ $factura->numero_factura }}</td>
                                    <td data-label="Cliente">
                                        <a href="{{ route('clientes.show', $factura->contrato->cliente->id) }}" 
                                        class="badge bg-info text-decoration-none text-truncate" 
                                        style="max-width: 150px; display: inline-block;" 
                                        target="_blank"
                                        title="{{ $factura->contrato->cliente->nombre }}">
                                            {{ $factura->contrato->cliente->nombre }}
                                        </a>
                                    </td>
                                    <td data-label="Mes">
                                        @php
                                            $meses = [
                                                1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                                5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                                9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                            ];
                                            $mesNumero = $factura->fecha_emision ? date('n', strtotime($factura->fecha_emision)) : null;
                                            $mesNombre = $mesNumero ? $meses[$mesNumero] : 'Sin fecha';
                                        @endphp
                                        <span class="badge bg-primary">{{ $mesNombre }}</span>
                                    </td>
                                    <td data-label="Total">${{ number_format($factura->monto_total, 2) }}</td>
                                    <td data-label="Acciones">
                                        <button wire:click="seleccionarFactura({{ $factura->id }})" 
                                                class="btn btn-sm btn-success">
                                            Registrar Pago
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $facturas->links() }}
                    </div>
                @else
                    <div class="alert alert-warning">
                        No se encontraron facturas pendientes
                    </div>
                @endif
    
                <!-- Modal para registrar pago -->
                @if($facturaSeleccionada && !$mostrarComprobante)
                    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Registrar Pago</h5>
                                    <button type="button" class="btn-close" aria-label="Close" wire:click="cerrarModal"></button>
                                </div>
                                <div class="modal-body">
                                    <form wire:submit.prevent="registrarPago">
                                        <div class="mb-3">
                                            <label>Monto</label>
                                            <input type="number" wire:model="monto" 
                                                class="form-control" 
                                                step="0.01"
                                                min="0.01"
                                                max="{{ $facturaSeleccionada->saldo_pendiente }}">
                                        </div>
                                        <div class="mb-3">
                                            <label>Método de Pago</label>
                                            <select wire:model="metodo_pago" class="form-select">
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="tarjeta">Tarjeta</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label>Fecha de Pago</label>
                                            <input type="date" wire:model="fecha_pago" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            Confirmar Pago
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Modal de Comprobante -->
                @if($mostrarComprobante && $pagoRegistrado)
                    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-gradient-success text-white">
                                    <h5 class="modal-title">COMPROBANTE DE PAGO</h5>
                                    <button type="button" class="btn-close btn-close-white" aria-label="Close" wire:click="cerrarComprobante"></button>
                                </div>
                                
                                <!-- Cuerpo con logo profesional -->
                                <div class="modal-body p-4" id="comprobantePago">
                                    <!-- Sección superior con logo y marca -->
                                    {{-- <div class="text-center mb-4 border-bottom pb-3">
                                        <img src="{{ asset('img/logo-empresa.png') }}" 
                                             alt="Suministro e Instalaciones en Redes" 
                                             class="img-fluid mb-3" 
                                             style="max-height: 80px;">
                                        
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div class="text-start">
                                                <h2 class="fw-bold mb-0 text-success">RECIBO DE PAGO</h2>
                                                <small class="text-muted">Servicios Profesionales de Internet</small>
                                            </div>
                                        </div>
                                    </div> --}}
                    
                                    <!-- Icono de éxito -->
                                    <div class="text-center">  <!-- Eliminé mb-4 del contenedor principal -->
                                        <!-- Logo con margen inferior reducido -->
                                        <img src="{{ asset('img/logo-empresa.png') }}" 
                                             alt="Suministro e Instalaciones en Redes" 
                                             class="img-fluid" 
                                             style="height: 120px; width: auto; max-width: 120%; margin-bottom: 0.5rem !important;">  <!-- mb-2 -->
                                        
                                        <!-- Texto con margen superior reducido -->
                                        <div style="margin-top: 0.5rem">  <!-- mt-2 -->
                                            <h3 class="text-success fw-bold d-flex align-items-center justify-content-center" 
                                                style="font-size: 1.6rem; gap: 0.5rem;">
                                                <i class="fas fa-check-circle"></i>
                                                <span>PAGO REGISTRADO</span>
                                            </h3>
                                            <p class="text-muted mb-0 small">
                                                #{{ substr($facturaSeleccionada->numero_factura, -8) }}
                                                <span class="mx-1">•</span>
                                                {{ now()->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                    
                                    <!-- Grid de información -->
                                    <div class="row g-3">
                                        <!-- Columna Cliente -->
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 fw-bold">
                                                        <i class="fas fa-user-tie me-2 text-primary"></i> INFORMACIÓN DEL CLIENTE
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="mb-2"><strong>Nombre:</strong> {{ $facturaSeleccionada->contrato->cliente->nombre }}</li>
                                                        <li class="mb-2"><strong>Identificación:</strong> {{ $facturaSeleccionada->contrato->cliente->cedula }}</li>
                                                        <li class="mb-2"><strong>Contacto:</strong> {{ $facturaSeleccionada->contrato->cliente->telefono }}</li>
                                                        <li><strong>Tipo de servicio:</strong> Internet</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                    
                                        <!-- Columna Pago -->
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-header bg-light">
                                                    <h6 class="mb-0 fw-bold">
                                                        <i class="fas fa-receipt me-2 text-primary"></i> DETALLES DE PAGO
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-unstyled mb-0">
                                                        <li class="mb-2"><strong>Fecha:</strong> {{ date('d/m/Y', strtotime($pagoRegistrado->fecha_pago)) }}</li>
                                                        <li class="mb-2"><strong>Método:</strong> <span class="badge bg-success">{{ ucfirst($pagoRegistrado->metodo_pago) }}</span></li>
                                                        <li class="mb-2"><strong>Monto:</strong> <span class="fw-bold">${{ number_format($pagoRegistrado->monto, 2) }}</span></li>
                                                        <li class="mb-2"><strong>Saldo pendiente :</strong> <span class="fw-bold">${{ number_format($facturaSeleccionada->saldo_pendiente, 2) }}</span></li>
                                                        <li class="mb-2"><strong>Factura:</strong> {{ $facturaSeleccionada->numero_factura }}</li>
                                                        <li><strong>Estado:</strong> <span class="badge bg-success">{{ $facturaSeleccionada->estado }}</span></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    
                                    <!-- Nota legal -->
                                    <div class="mt-4 p-3 bg-light rounded">
                                        <div class="d-flex">
                                            <i class="fas fa-info-circle text-primary mt-1 me-2"></i>
                                            <div>
                                                <p class="small mb-0 text-muted">
                                                    <strong>Suministro e Instalaciones en Redes</strong><br>
                                                    Este documento es un comprobante de pago válido. Para consultas contactar al: +58 412-5676329
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" wire:click="cerrarComprobante">
                                        <i class="fas fa-times me-2"></i>Cerrar
                                    </button>
                                    @if($facturaSeleccionada->contrato->cliente->telefono)
                                        @php
                                            $mensaje = "¡Hola! 👋 *{$facturaSeleccionada->contrato->cliente->nombre}*\n\n";
                                            $mensaje .= "Adjunto encontrarás el *comprobante de pago* de tu servicio de internet.\n\n";
                                            $mensaje .= "¿Necesitas ayuda con tu conexión o tienes alguna duda? Estamos para servirte.\n\n";
                                            $mensaje .= "¡Gracias por preferirnos! ⚡💻\n\n";
                                            $mensaje .= "*Equipo Suministro E Instalaciones En redes*";
                                        @endphp

                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $facturaSeleccionada->contrato->cliente->telefono) }}?text={{ rawurlencode($mensaje) }}" 
                                        class="btn btn-success" target="_blank">
                                        <i class="fab fa-whatsapp me-2"></i>Enviar comprobante
                                        </a>
                                        <button class="btn btn-primary" wire:ignore onclick="generarImagenComprobante()">
                                            <i class="fas fa-image me-2"></i>Ver imagen
                                        </button>                      
                                    @endif                                 
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('notify', (data) => {
                    toastr[data.type](data.message);
                });
            });
        </script>
    @endpush
</div>