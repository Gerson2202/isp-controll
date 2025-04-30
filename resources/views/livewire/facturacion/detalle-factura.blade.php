<div class="container-fluid">
    @if($factura)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0">Factura del Mes Actual #{{ $factura->numero_factura }}</h3>
                <span class="badge bg-light text-dark">
                    {{ $factura->fecha_emision->translatedFormat('F Y') }}
                </span>
            </div>
            
            <div class="card-body">
                <!-- Estado de la factura -->
                <div class="mb-3">
                    <span class="badge {{ $factura->estado == 'pendiente' ? 'bg-warning' : 'bg-success' }} p-2">
                        {{ $factura->estado == 'pendiente' ? 'PENDIENTE DE PAGO' : 'PAGADO (SIN DEUDA)' }}
                    </span>
                </div>
                
                <!-- Información general -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-2">
                        <p class="text-muted mb-1">Fecha Emisión:</p>
                        <p class="fw-bold">{{ $factura->fecha_emision->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-3 mb-2">
                        <p class="text-muted mb-1">Fecha Vencimiento:</p>
                        <p class="fw-bold">{{ $factura->fecha_vencimiento->format('d/m/Y') }}</p>
                    </div>
                    <div class="col-md-3 mb-2">
                        <p class="text-muted mb-1">Monto Total:</p>
                        <p class="fw-bold">${{ number_format($factura->monto_total, 2) }}</p>
                    </div>
                    <div class="col-md-3 mb-2">
                        <p class="text-muted mb-1">Saldo Pendiente:</p>
                        <p class="fw-bold">${{ number_format($factura->saldo_pendiente, 2) }}</p>
                    </div>
                </div>
                
                <!-- Items de la factura -->
                <h5 class="fw-bold mb-3">Items Facturados</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-start">Descripción</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->descripcion }}</td>
                                    <td class="text-end">${{ number_format($item->monto, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Historial de pagos -->
                <h5 class="fw-bold mb-3">Historial de Pagos</h5>
                @if($pagos->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start">Fecha</th>
                                    <th class="text-start">Método</th>
                                    <th class="text-start">Referencia</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagos as $pago)
                                    <tr>
                                        <td>{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                                        <td>{{ $pago->metodo_pago }}</td>
                                        <td>{{ $pago->referencia }}</td>
                                        <td class="text-end">${{ number_format($pago->monto, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-4">
                        <a 
                            href="{{ route('clientes.historial-facturas', $cliente->id) }}" 
                            class="btn btn-primary"
                        >
                            <i class="fas fa-history me-2"></i> Ver Histórico Completo
                        </a>
                    </div>
                @else
                
                    <div class="alert alert-info mb-0">
                        No se han registrado pagos.
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted mb-0">El cliente no tiene facturas asociadas.</p>
            </div>
        </div>
    @endif
</div>