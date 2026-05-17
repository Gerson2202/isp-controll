@php
    $esParaImagen = isset($paraImagen) && $paraImagen === true;

    if ($esParaImagen) {
        $clienteNombre = $facturaSeleccionada->contrato->cliente->nombre ?? '';
        $clienteIdentificacion = $facturaSeleccionada->contrato->cliente->identificacion ?? '';
        $clienteContacto = $facturaSeleccionada->contrato->cliente->telefono ?? '';
        $tipoServicio = $facturaSeleccionada->contrato->plan->nombre ?? 'Internet';
        $periodoServicio = $facturaSeleccionada->periodo ?? 'junio 2026';
        $numeroFactura = $facturaSeleccionada->numero_factura ?? '';
        $montoPagado = $pagoRegistrado->monto ?? 0;
        $metodoPago = $pagoRegistrado->metodo_pago ?? '';
        $fechaPago = $pagoRegistrado->fecha_pago ?? now();
        $saldoPendiente = 0;

        // Datos de la empresa (desde la variable $empresa que viene del componente)
        $empresaNombre = $empresa->nombre ?? 'FerNet';
        $empresaLogo = $empresa->logo ?? null;
        $empresaTelefono = $empresa->telefono ?? '####';
        $empresaEmail = $empresa->email ?? 'contacto@fernet.com';
        $empresaDireccion = $empresa->direccion ?? '';
        $empresaCiudad = $empresa->ciudad ?? '';
        $empresaNit = $empresa->nit ?? '';
        $empresaSlogan = $empresa->slogan ?? '';
    }
@endphp

@if (!$esParaImagen)
    <!-- Tu código original del modal completo -->
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <!-- todo tu modal actual -->
    </div>
@else
    <!-- Versión para la IMAGEN - Con logo de empresa dinámico y más grande -->
    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
                background: #f0f2f5;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }

            .comprobante {
                max-width: 500px;
                width: 100%;
                background: white;
                border-radius: 28px;
                overflow: hidden;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            }

            /* Logo más grande */
            .logo-container {
                text-align: center;
                padding: 30px 20px 20px 20px;
                background: white;
                border-bottom: 1px solid #e9ecef;
            }

            .logo-container img {
                max-height: 90px;
                width: auto;
                object-fit: contain;
            }

            .logo-text {
                font-size: 32px;
                font-weight: bold;
                color: #2e7d32;
                letter-spacing: 3px;
            }

            .slogan {
                font-size: 12px;
                color: #6c757d;
                margin-top: 5px;
            }

            /* Header */
            .header-status {
                background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
                padding: 25px 20px;
                text-align: center;
                color: white;
            }

            .header-status .check-icon {
                font-size: 56px;
                margin-bottom: 10px;
            }

            .header-status h1 {
                font-size: 24px;
                font-weight: 700;
                letter-spacing: 2px;
                margin-bottom: 5px;
            }

            .header-status .ref-number {
                font-size: 12px;
                opacity: 0.9;
                font-family: monospace;
            }

            /* Info cliente */
            .info-cliente {
                padding: 25px 25px 20px 25px;
                background: white;
                border-bottom: 1px solid #e9ecef;
            }

            .info-cliente h3 {
                font-size: 14px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                margin-bottom: 18px;
            }

            .cliente-row {
                display: flex;
                margin-bottom: 14px;
                font-size: 14px;
            }

            .cliente-label {
                width: 110px;
                color: #6c757d;
                font-weight: normal;
            }

            .cliente-value {
                flex: 1;
                color: #212529;
                font-weight: 500;
            }

            /* Detalles de pago */
            .detalles-pago {
                padding: 20px 25px;
                background: white;
            }

            .detalles-pago h3 {
                font-size: 14px;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                margin-bottom: 18px;
            }

            .pago-row {
                display: flex;
                justify-content: space-between;
                padding: 12px 0;
                border-bottom: 1px solid #f1f3f5;
            }

            .pago-row:last-child {
                border-bottom: none;
            }

            .pago-label {
                color: #6c757d;
                font-size: 14px;
            }

            .pago-value {
                font-weight: 600;
                color: #212529;
                font-size: 14px;
            }

            .pago-value.pagado {
                color: #2e7d32;
            }

            .monto-destacado {
                font-size: 22px;
                font-weight: 800;
                color: #1b5e20;
            }

            /* Línea decorativa */
            .decorative-line {
                margin: 20px 25px 10px 25px;
                height: 3px;
                background: linear-gradient(90deg, transparent, #2e7d32, #1a237e, #2e7d32, transparent);
                border-radius: 3px;
            }

            /* Footer mejorado */
            .footer {
                background: linear-gradient(135deg, #1a237e 0%, #0d174a 100%);
                padding: 25px 25px 30px 25px;
                text-align: center;
                margin-top: 5px;
            }

            .footer .empresa-nombre {
                font-size: 18px;
                font-weight: 700;
                color: white;
                margin-bottom: 8px;
                letter-spacing: 1px;
            }

            .footer .empresa-info {
                font-size: 11px;
                color: rgba(255, 255, 255, 0.85);
                line-height: 1.6;
                margin-bottom: 12px;
            }

            .footer .empresa-contacto {
                font-size: 10px;
                color: rgba(255, 255, 255, 0.7);
                line-height: 1.5;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
                padding-top: 12px;
                margin-top: 8px;
            }

            .footer .mensaje-legal {
                font-size: 9px;
                color: rgba(255, 255, 255, 0.5);
                margin-top: 12px;
            }

            /* Reducir paddings para ahorrar espacio */
            .logo-container {
                padding: 20px 20px 15px 20px;
                /* Reducido */
            }

            .logo-container img {
                max-height: 60px;
                /* Logo un poco más pequeño */
            }

            .header-status {
                padding: 18px 20px;
                /* Reducido */
            }

            .header-status .check-icon {
                font-size: 40px;
                /* Ícono más pequeño */
            }

            .header-status h1 {
                font-size: 20px;
            }

            .info-cliente {
                padding: 15px 25px 12px 25px;
                /* Reducido */
            }

            .detalles-pago {
                padding: 12px 25px;
                /* Reducido */
            }

            .pago-row {
                padding: 8px 0;
                /* Reducido */
            }

            .footer {
                padding: 15px 25px 20px 25px;
                /* Reducido */
            }

            .decorative-line {
                margin: 10px 25px 5px 25px;
                /* Reducido */
            }
        </style>
    </head>

    <body>
        <div class="comprobante">
            <!-- LOGO GRANDE (desde empresa) -->
            <div class="logo-container">
                @php
                    // Usar logo de la empresa desde base de datos
                    $logoBase64 = '';
                    if (!empty($empresaLogo)) {
                        $logoPath = public_path('storage/' . $empresaLogo);
                        if (file_exists($logoPath)) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                        }
                    }

                    // Fallback a logo por defecto
                    if (empty($logoBase64)) {
                        $defaultLogoPath = public_path('img/logo-fernet.png');
                        if (file_exists($defaultLogoPath)) {
                            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($defaultLogoPath));
                        }
                    }
                @endphp
                @if ($logoBase64)
                    <img src="{{ $logoBase64 }}" alt="{{ $empresaNombre }}">
                @else
                    <div class="logo-text">{{ $empresaNombre }}</div>
                @endif
                {{-- @if ($empresaSlogan)
                    <div class="slogan">{{ $empresaSlogan }}</div>
                @endif --}}
            </div>

            <!-- Header con estado -->
            <div class="header-status">
                <div class="check-icon">✓</div>
                <h1>COMPROBANTE DE PAGO</h1>
                <div class="ref-number">#{{ substr($numeroFactura, -8) ?? '13221056' }} •
                    {{ \Carbon\Carbon::parse($fechaPago)->format('d/m/Y H:i') }}</div>
            </div>

            <!-- Información del cliente -->
            <div class="info-cliente">
                <h3>INFORMACIÓN DEL CLIENTE</h3>
                <div class="cliente-row">
                    <span class="cliente-label">Nombre:</span>
                    <span class="cliente-value">{{ $clienteNombre }}</span>
                </div>
                <div class="cliente-row">
                    <span class="cliente-label">Identificación:</span>
                    <span class="cliente-value">{{ $clienteIdentificacion }}</span>
                </div>
                <div class="cliente-row">
                    <span class="cliente-label">Contacto:</span>
                    <span class="cliente-value">{{ $clienteContacto }}</span>
                </div>
                <div class="cliente-row">
                    <span class="cliente-label">Tipo de servicio:</span>
                    <span class="cliente-value">{{ $tipoServicio }}</span>
                </div>
            </div>

            <!-- Detalles de pago -->
            <div class="detalles-pago">
                <h3>DETALLES DE PAGO</h3>
                <div class="pago-row">
                    <span class="pago-label">Fecha de pago:</span>
                    <span class="pago-value">{{ \Carbon\Carbon::parse($fechaPago)->format('d/m/Y') }}</span>
                </div>
                <div class="pago-row">
                    <span class="pago-label">Periodo del servicio:</span>
                    <span class="pago-value">{{ $periodoServicio }}</span>
                </div>
                <div class="pago-row">
                    <span class="pago-label">Método:</span>
                    <span class="pago-value">{{ ucfirst($metodoPago) }}</span>
                </div>
                <div class="pago-row">
                    <span class="pago-label">Monto:</span>
                    <span class="pago-value monto-destacado">${{ number_format($montoPagado, 0, ',', '.') }}</span>
                </div>
                <div class="pago-row">
                    <span class="pago-label">Saldo pendiente:</span>
                    <span class="pago-value pagado">${{ number_format($saldoPendiente, 0, ',', '.') }}</span>
                </div>
                <div class="pago-row">
                    <span class="pago-label">Comprobante:</span>
                    <span class="pago-value">{{ $numeroFactura }}</span>
                </div>
                <div class="pago-row">
                    <span class="pago-label">Estado:</span>
                    <span class="pago-value pagado">PAGADA</span>
                </div>
            </div>

            <!-- Línea decorativa -->
            <div class="decorative-line"></div>

            <!-- Footer con información de la empresa -->
            <div class="footer">
                <div class="empresa-nombre">{{ $empresaNombre }}</div>
                @if ($empresaNit)
                    <div class="empresa-info">NIT: {{ $empresaNit }}</div>
                @endif
                {{-- @if ($empresaDireccion || $empresaCiudad)
                    <div class="empresa-info">
                        {{ $empresaDireccion }}{{ $empresaDireccion && $empresaCiudad ? ', ' : '' }}{{ $empresaCiudad }}
                    </div>
                @endif --}}
                <div class="empresa-contacto">
                    @if ($empresaTelefono)
                        📞 {{ $empresaTelefono }}
                    @endif
                    @if ($empresaTelefono && $empresaEmail)
                        |
                    @endif
                    @if ($empresaEmail)
                        ✉ {{ $empresaEmail }}
                    @endif
                </div>
                <div class="mensaje-legal">
                    Este documento es un comprobante de pago válido
                </div>
            </div>
        </div>
    </body>

    </html>
@endif
