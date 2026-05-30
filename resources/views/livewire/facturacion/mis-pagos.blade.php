<div>

    {{-- METRICAS --}}
    <div class="row mb-4">

        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Total Recaudado
                    </div>

                    <h3 class="fw-bold text-success mb-0">
                        $ {{ number_format($totalRecaudado, 0, ',', '.') }}
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Total Pagos
                    </div>

                    <h3 class="fw-bold mb-0">
                        {{ $totalPagos }}
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Promedio Pago
                    </div>

                    <h3 class="fw-bold text-primary mb-0">
                        $ {{ number_format($promedioPago, 0, ',', '.') }}
                    </h3>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body">

                    <div class="text-muted small mb-2">
                        Clientes Atendidos
                    </div>

                    <h3 class="fw-bold mb-0">
                        {{ $clientesUnicos }}
                    </h3>

                </div>

            </div>

        </div>

    </div>

    {{-- FILTROS --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">

                    <label class="small text-muted mb-1">
                        Buscar
                    </label>

                    <input type="text" class="form-control" placeholder="Cliente, contrato o factura"
                        wire:model.live="search">

                </div>

                <div class="col-md-2 mb-3">

                    <label class="small text-muted mb-1">
                        Mes
                    </label>

                    <select class="form-control" wire:model.live="mes">

                        <option value="">
                            Todos
                        </option>

                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">

                                {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}

                            </option>
                        @endfor

                    </select>

                </div>

                <div class="col-md-2 mb-3">

                    <label class="small text-muted mb-1">
                        Año
                    </label>

                    <select class="form-control" wire:model.live="anio">

                        <option value="">
                            Todos
                        </option>

                        @for ($y = now()->year; $y >= 2020; $y--)
                            <option value="{{ $y }}">
                                {{ $y }}
                            </option>
                        @endfor

                    </select>

                </div>

                <div class="col-md-2 mb-3">

                    <label class="small text-muted mb-1">
                        Método Pago
                    </label>

                    <select class="form-control" wire:model.live="metodoPago">

                        <option value="">
                            Todos
                        </option>

                        <option value="efectivo">
                            Efectivo
                        </option>

                        <option value="transferencia">
                            Transferencia
                        </option>

                        <option value="tarjeta">
                            Tarjeta
                        </option>

                        <option value="otro">
                            Otro
                        </option>

                    </select>

                </div>

                <div class="col-md-2 mb-3">

                    <label class="small text-muted mb-1">
                        Estado
                    </label>

                    <select class="form-control" wire:model.live="estadoFactura">

                        <option value="">
                            Todos
                        </option>

                        <option value="pendiente">
                            Pendiente
                        </option>

                        <option value="pagada">
                            Pagada
                        </option>

                        <option value="vencida">
                            Vencida
                        </option>

                        <option value="anulada">
                            Anulada
                        </option>

                    </select>

                </div>

                <div class="col-md-1 mb-3 d-flex align-items-end">

                    <button class="btn btn-outline-secondary w-100" wire:click="limpiarFiltros">
                        <i class="fas fa-sync-alt"></i>
                    </button>

                </div>

            </div>

        </div>

    </div>

    {{-- RECAUDO MENSUAL --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="mb-0 fw-bold">
                        Recaudo Mensual
                    </h5>

                    <small class="text-muted">
                        Resumen de recaudo agrupado por meses
                    </small>

                </div>

                <button class="btn btn-light border" type="button" data-toggle="collapse"
                    data-target="#recaudoMensualCollapse" aria-expanded="true">
                    <i class="fas fa-chevron-down"></i>
                </button>

            </div>

        </div>

        <div class="collapse show" id="recaudoMensualCollapse">

            <div class="card-body pt-0">

                <div class="table-responsive"
                    style="
                    max-height: 320px;
                    overflow-y: auto;
                    overflow-x: hidden;
                ">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="bg-light"
                            style="
                            position: sticky;
                            top: 0;
                            z-index: 1;
                        ">

                            <tr>

                                <th style="width: 20%;">
                                    Mes
                                </th>

                                <th style="width: 20%;">
                                    Año
                                </th>

                                <th style="width: 25%;">
                                    Total
                                </th>

                                <th style="width: 20%;">
                                    Pagos
                                </th>

                                <th style="width: 15%;">
                                    Estado
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($recaudoMensual as $item)
                                <tr>

                                    <td>

                                        <div class="fw-semibold">

                                            {{ \Carbon\Carbon::create()->month($item->mes)->translatedFormat('F') }}

                                        </div>

                                    </td>

                                    <td>

                                        {{ $item->anio }}

                                    </td>

                                    <td>

                                        <span class="fw-bold text-success">

                                            $ {{ number_format($item->total, 0, ',', '.') }}

                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge badge-light border px-3 py-2 text-dark">

                                            {{ $item->cantidad ?? 0 }} pagos

                                        </span>

                                    </td>

                                    <td>

                                        <span class="badge badge-success px-3 py-2">
                                            Cerrado
                                        </span>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center text-muted py-5">

                                        <i class="fas fa-chart-line fa-2x mb-3 d-block"></i>

                                        No hay registros mensuales

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

    {{-- TABLA --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h5 class="fw-bold mb-0">
                        Historial de Pagos
                    </h5>

                    <small class="text-muted">
                        Pagos registrados por el usuario
                    </small>

                </div>

                <div style="width: 90px;">

                    <select class="form-control" wire:model.live="perPage">

                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>

                    </select>

                </div>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                        <tr class="border-bottom">

                            <th class="text-muted small">CLIENTE</th>
                            <th class="text-muted small">FACTURA</th>
                            <th class="text-muted small">PLAN</th>
                            <th class="text-muted small">PAGO</th>
                            <th class="text-muted small">FECHA</th>
                            <th class="text-muted small">MÉTODO</th>
                            <th class="text-muted small">ESTADO</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($pagos as $pago)
                            <tr>

                                <td style="min-width: 240px;">

                                    <div class="fw-bold">

                                        {{ $pago->factura->contrato->cliente->nombre ?? 'N/A' }}

                                    </div>

                                    <small class="text-muted d-block">

                                        Contrato #{{ $pago->factura->contrato->id ?? 'N/A' }}

                                    </small>

                                    <small class="text-muted d-block">

                                        {{ $pago->factura->contrato->cliente->telefono ?? '' }}

                                    </small>

                                </td>

                                <td style="min-width: 180px;">

                                    <div class="fw-bold">

                                        {{ $pago->factura->numero_factura }}

                                    </div>

                                    <small class="text-muted">

                                        Factura:
                                        $ {{ number_format($pago->factura->monto_total, 0, ',', '.') }}

                                    </small>

                                </td>

                                <td style="min-width: 220px;">

                                    {{ $pago->factura->contrato->plan->nombre ?? 'Sin plan' }}

                                </td>

                                <td>

                                    <span class="fw-bold text-success">

                                        $ {{ number_format($pago->monto, 0, ',', '.') }}

                                    </span>

                                </td>

                                <td>

                                    {{ \Carbon\Carbon::parse($pago->fecha_pago)->format('d/m/Y') }}

                                </td>

                                <td>

                                    <span class="badge badge-light border text-dark text-uppercase px-3 py-2">

                                        {{ $pago->metodo_pago }}

                                    </span>

                                </td>

                                <td>

                                    @if ($pago->factura->estado == 'pagada')
                                        <span class="badge badge-success px-3 py-2">
                                            Pagada
                                        </span>
                                    @elseif($pago->factura->estado == 'pendiente')
                                        <span class="badge badge-warning px-3 py-2">
                                            Pendiente
                                        </span>
                                    @elseif($pago->factura->estado == 'vencida')
                                        <span class="badge badge-danger px-3 py-2">
                                            Vencida
                                        </span>
                                    @else
                                        <span class="badge badge-secondary px-3 py-2">
                                            Anulada
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center py-5">

                                    <i class="fas fa-money-check-alt fa-3x text-muted mb-3"></i>

                                    <div class="text-muted">
                                        No se encontraron pagos
                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $pagos->links() }}

            </div>

        </div>

    </div>

</div>
