<div class="container-fluid mt-1">
    <!-- Card Principal -->
    <div class="card border">
        <!-- Header simple -->
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0">Clientes Morosos (Mas de 3 Meses En Mora)</h5>
                </div>
                <div>
                    @php
                        $totalDeuda = 0;
                        $totalFacturas = 0;
                        foreach ($clientes as $cliente) {
                            foreach ($cliente->contratos as $contrato) {
                                foreach ($contrato->facturas as $factura) {
                                    if (
                                        $factura->estado == 'pendiente' &&
                                        Carbon\Carbon::parse($factura->fecha_emision)->lt(now()->subMonths(3))
                                    ) {
                                        $totalDeuda += $factura->saldo_pendiente;
                                        $totalFacturas++;
                                    }
                                }
                            }
                        }
                    @endphp
                    <span class="badge bg-secondary text-white">
                        {{ $clientes->count() }} Clientes
                    </span>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="card-body p-0">
            <!-- Barra de Búsqueda -->
            <div class="p-3 border-bottom">
                <div class="row">
                    <div class="col-md-12">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar por nombre del cliente..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            @if (session()->has('message'))
                <div class="alert alert-success rounded-0 mb-0 border-bottom" role="alert">
                    <div class="d-flex align-items-center">
                        <div>{{ session('message') }}</div>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            @endif

            <!-- Tabla de Clientes -->
            @if ($clientes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Cliente</th>
                                <th class="text-center">Contrato</th>
                                <th class="text-center">Nodo</th>
                                <th class="text-center">Ip</th>
                                <th class="text-center">Estado Contrato</th>
                                <th class="text-center">Estado Mikrotik</th>
                                <th class="text-center">Factura</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clientes as $cliente)
                                @php
                                    $facturasMorosas = [];
                                    $totalMoraCliente = 0;
                                    $mesesMaxMora = 0;

                                    foreach ($cliente->contratos as $contrato) {
                                        foreach ($contrato->facturas as $factura) {
                                            if (
                                                $factura->estado == 'pendiente' &&
                                                Carbon\Carbon::parse($factura->fecha_emision)->lt(now()->subMonths(3))
                                            ) {
                                                $mesesMora = Carbon\Carbon::parse(
                                                    $factura->fecha_emision,
                                                )->diffInMonths(now());
                                                $mesesMaxMora = max($mesesMaxMora, $mesesMora);
                                                $totalMoraCliente += $factura->saldo_pendiente;

                                                $facturasMorosas[] = [
                                                    'contrato_id' => $contrato->id,
                                                    'numero_factura' => $factura->numero_factura,
                                                    'fecha_emision' => $factura->fecha_emision,
                                                    'mes_factura' => Carbon\Carbon::parse(
                                                        $factura->fecha_emision,
                                                    )->format('M Y'),
                                                    'saldo_pendiente' => $factura->saldo_pendiente,
                                                    'ip' => $cliente->ip ?? 'Sin ip',
                                                    'estado_contrato' => $contrato->estado ?? 'N/A',
                                                    'estado_mikrotik' => $cliente->estado ?? 'N/A',
                                                    'meses_mora' => $mesesMora,
                                                    'plan_nombre' => $contrato->plan->nombre ?? 'N/A',
                                                    'nodo_nombre' => $contrato->plan->nodo->nombre ?? 'Sin nodo',
                                                ];
                                            }
                                        }
                                    }

                                    usort($facturasMorosas, function ($a, $b) {
                                        return strtotime($a['fecha_emision']) - strtotime($b['fecha_emision']);
                                    });
                                @endphp

                                @if (count($facturasMorosas) > 0)
                                    <tr>
                                        <td class="ps-4">
                                            <div>
                                                <div class="fw-semibold">
                                                    <a href="{{ route('clientes.show', $cliente->id) }}" target="_blank"
                                                        rel="noopener" class="text-decoration-none link-primary">
                                                        {{ $cliente->nombre }}
                                                    </a>
                                                </div>
                                                <div class="small text-muted">
                                                    @if ($cliente->cedula)
                                                        {{ $cliente->cedula }}
                                                    @endif
                                                    @if ($cliente->telefono)
                                                        <br>{{ $cliente->telefono }}
                                                    @endif
                                                    {{-- </div>
                                        <div class="small mt-1">
                                            <span class="text-dark">
                                                {{ $mesesMaxMora }} meses
                                            </span>
                                            <span class="ms-2">
                                                ${{ number_format($totalMoraCliente, 2) }}
                                            </span>
                                        </div> --}}
                                                </div>
                                        </td>

                                        <td class="text-center">
                                            @if (count($facturasMorosas) > 0)
                                                <div class="small">
                                                    <div>#{{ $facturasMorosas[0]['contrato_id'] }}</div>
                                                    <div class="text-muted">{{ $facturasMorosas[0]['plan_nombre'] }}
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if (count($facturasMorosas) > 0)
                                                <div class="small">
                                                    {{ $facturasMorosas[0]['nodo_nombre'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (count($facturasMorosas) > 0)
                                                <div class="small">
                                                    {{ $facturasMorosas[0]['ip'] }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if (count($facturasMorosas) > 0)
                                                @php
                                                    $estadoContrato = strtolower(
                                                        $facturasMorosas[0]['estado_contrato'],
                                                    );
                                                    $colorContrato =
                                                        $estadoContrato === 'activo' ? 'success' : 'danger';
                                                @endphp

                                                <span class="badge bg-{{ $colorContrato }} rounded-pill px-3 py-2">
                                                    <span class="me-1">●</span>
                                                    {{ ucfirst($estadoContrato) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            @if (count($facturasMorosas) > 0)
                                                @php
                                                    $estadoMikrotik = strtolower(
                                                        $facturasMorosas[0]['estado_mikrotik'],
                                                    );
                                                    $colorMikrotik =
                                                        $estadoMikrotik === 'activo' ? 'success' : 'danger';
                                                @endphp

                                                <span class="badge bg-{{ $colorMikrotik }} rounded-pill px-3 py-2">
                                                    <span class="me-1">●</span>
                                                    {{ ucfirst($estadoMikrotik) }}
                                                </span>
                                            @endif
                                        </td>


                                        <td class="text-center">
                                            @if (count($facturasMorosas) > 0)
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ $facturasMorosas[0]['mes_factura'] }}
                                                    </div>
                                                    <div class="small text-muted">
                                                        #{{ $facturasMorosas[0]['numero_factura'] }}
                                                    </div>
                                                    <div>
                                                        ${{ number_format($facturasMorosas[0]['saldo_pendiente'], 2) }}
                                                    </div>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="text-center pe-4">
                                            <button class="btn btn-sm btn-danger"
                                                onclick="confirmarBaja({{ $cliente->id }}, '{{ $cliente->nombre }}')">
                                                Dar de Baja
                                            </button>

                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Resumen -->
                <div class="p-3 border-top">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="small">
                                    {{ $clientes->count() }} clientes con mora > 3 meses
                                </div>
                                <div>
                                    <span class="fw-semibold">
                                        Total: ${{ number_format($totalDeuda, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado vacío -->
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-check-circle fa-lg"></i>
                    </div>
                    <h6 class="mb-2">Sin clientes morosos</h6>
                    <p class="text-muted mb-0">No hay clientes con facturas pendientes mayores a 3 meses.</p>
                </div>
            @endif
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmarBaja(clienteId, nombreCliente) {

        Swal.fire({
            title: '¿Estás seguro?',
            html: `
                <strong>Se dará de baja al cliente:</strong><br><br>
                ${nombreCliente}<br><br>
                <span style="color:red;">
                ⚠ Esto realizará lo siguiente:
                </span>
                <ul style="text-align:left;">
                    <li>Eliminar colas en MikroTik</li>
                    <li>Cancelar contratos activos</li>
                    <li>Liberar IP asignada</li>
                    <li>Marcar cliente como CORTADO</li>
                    <li>Crear ticket automático</li>
                </ul>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, dar de baja',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {

            if (result.isConfirmed) {
                @this.call('darDeBaja', clienteId);
            }

        });
    }
</script>
