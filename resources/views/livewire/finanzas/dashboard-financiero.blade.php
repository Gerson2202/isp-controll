<div>
    <!-- HEADER -->
  <div class="row mb-4">
    <div class="col-md-6">
        <div class="d-flex align-items-center">
            <span class="badge badge-primary mr-2" style="font-size: 14px; padding: 8px 12px;">
                <i class="fas fa-calendar-alt"></i> {{ $nombreMes }}
            </span>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> Resumen mensual
            </small>
        </div>
    </div>
    <div class="col-md-6 text-right">
        <div class="btn-group" role="group">
            <button wire:click="cambiarMes('anterior')" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm" disabled style="min-width: 120px;">
                <strong>{{ $nombreMes }}</strong>
            </button>
            <button wire:click="cambiarMes('siguiente')" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <button wire:click="generarReporte" class="btn btn-danger btn-sm ml-2">
            <i class="fas fa-file-pdf"></i> PDF
        </button>
    </div>
</div>

    <!-- MODAL PDF -->
    @if ($mostrarReportePDF)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); display: block;" tabindex="-1">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-pdf text-danger"></i> Reporte Financiero - {{ $nombreMes }}
                        </h5>
                        <div>
                            <button onclick="window.print()" class="btn btn-success mr-2">
                                <i class="fas fa-print"></i> Imprimir / Guardar PDF
                            </button>
                            <button wire:click="cerrarReporte" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cerrar
                            </button>
                        </div>
                    </div>
                    <div class="modal-body">
                        @include('pdf.reporte-financiero', [
                            'nombreMes' => $nombreMes,
                            'ingresos' => $ingresos,
                            'gastos' => $gastos,
                            'saldoNeto' => $saldoNeto,
                            'tasaRetencion' => $tasaRetencion,
                            'gastosPorCategoria' => $gastosPorCategoria,
                            'topGastos' => $topGastos,
                            'gastosRecurrentes' => $gastosRecurrentes,
                            'gastosRecurrentesDetalle' => $gastosRecurrentesDetalle,
                            'gastosMovimientos' => $gastosMovimientos, // Cambiado de ultimosMovimientos a gastosMovimientos
                            'fechaGeneracion' => now()->format('d/m/Y H:i:s'),
                            'mesSeleccionado' => $mesSeleccionado,
                            'anoSeleccionado' => $anoSeleccionado,
                        ])
                    </div>
                </div>
            </div>
        </div>
        
    @endif

    <!-- TARJETAS DE RESUMEN -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>$ {{ number_format($ingresos, 2) }}</h3>
                    <p>Total Ingresos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>$ {{ number_format($gastos, 2) }}</h3>
                    <p>Total Gastos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-down"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box {{ $saldoNeto >= 0 ? 'bg-info' : 'bg-warning' }}">
                <div class="inner">
                    <h3>$ {{ number_format($saldoNeto, 2) }}</h3>
                    <p>Saldo Neto</p>
                </div>
                <div class="icon">
                    <i class="fas fa-balance-scale"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $tasaRetencion }}%</h3>
                    <p>Tasa de Retención</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICOS DE EVOLUCIÓN -->
<div class="row">
    <div class="col-md-6">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> Ingresos Diarios
                </h3>
            </div>
            <div class="card-body">
                @if (count($ingresosPorDia) > 0 && max(array_column($ingresosPorDia->toArray(), 'total')) > 0)
                    <div class="d-flex align-items-end justify-content-between" style="height: 220px; gap: 4px; padding-top: 20px;">
                        @php
                            $maxIngresos = max(array_column($ingresosPorDia->toArray(), 'total'));
                        @endphp
                        @foreach ($ingresosPorDia as $dia)
                            @php
                                $altura = ($dia['total'] / $maxIngresos) * 180;
                                $altura = max($altura, 5);
                            @endphp
                            <div class="text-center" style="flex: 1; min-width: 20px; height: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
                                @if($dia['total'] > 0)
                                    <small class="text-success font-weight-bold" style="font-size: 9px; margin-bottom: 2px;">
                                        ${{ number_format($dia['total'], 0) }}
                                    </small>
                                @endif
                                <div class="bg-success rounded-top" 
                                     style="height: {{ $altura }}px; 
                                            width: 80%; 
                                            margin: 0 auto;
                                            min-height: 5px;
                                            transition: height 0.5s ease;">
                                </div>
                                <small class="text-muted mt-1" style="font-size: 10px;">{{ $dia['fecha'] }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-4">No hay datos</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-danger card-outline">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i> Gastos Diarios
                </h3>
            </div>
            <div class="card-body">
                @if (count($gastosPorDia) > 0 && max(array_column($gastosPorDia->toArray(), 'total')) > 0)
                    <div class="d-flex align-items-end justify-content-between" style="height: 220px; gap: 4px; padding-top: 20px;">
                        @php
                            $maxGastos = max(array_column($gastosPorDia->toArray(), 'total'));
                        @endphp
                        @foreach ($gastosPorDia as $dia)
                            @php
                                $altura = ($dia['total'] / $maxGastos) * 180;
                                $altura = max($altura, 5);
                            @endphp
                            <div class="text-center" style="flex: 1; min-width: 20px; height: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
                                @if($dia['total'] > 0)
                                    <small class="text-danger font-weight-bold" style="font-size: 9px; margin-bottom: 2px;">
                                        ${{ number_format($dia['total'], 0) }}
                                    </small>
                                @endif
                                <div class="bg-danger rounded-top" 
                                     style="height: {{ $altura }}px; 
                                            width: 80%; 
                                            margin: 0 auto;
                                            min-height: 5px;
                                            transition: height 0.5s ease;">
                                </div>
                                <small class="text-muted mt-1" style="font-size: 10px;">{{ $dia['fecha'] }}</small>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-4">No hay datos</p>
                @endif
            </div>
        </div>
    </div>
</div>

    <!-- GASTOS POR CATEGORÍA Y RESUMEN FACTURAS -->
    <div class="row">
        <div class="col-md-8">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tags"></i> Gastos por Categoría
                    </h3>
                </div>
                <div class="card-body">
                    @forelse($gastosPorCategoria as $categoria)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>{{ $categoria->nombre }}</span>
                                <span class="font-weight-bold">$ {{ number_format($categoria->total, 2) }}</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar"
                                    style="width: {{ ($categoria->total / ($gastosPorCategoria->sum('total') ?: 1)) * 100 }}%;
                                            background-color: {{ $categoria->color ?? '#007bff' }};">
                                    {{ round(($categoria->total / ($gastosPorCategoria->sum('total') ?: 1)) * 100) }}%
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No hay gastos registrados</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-invoice"></i> Resumen Facturas
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Total Facturas</span>
                        <span class="font-weight-bold">$ {{ number_format($totalFacturas, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Facturas Pagadas</span>
                        <span class="font-weight-bold text-success">$ {{ number_format($facturasPagadas, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>Pendientes</span>
                        <span class="font-weight-bold text-danger">$
                            {{ number_format($facturasPendientes, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span>Gastos Recurrentes</span>
                        <span class="font-weight-bold text-warning">$
                            {{ number_format($gastosRecurrentes, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABLAS ORGANIZADAS CON PESTAÑAS -->
    <div class="row">
        <div class="col-md-12">
            <div class="card card-outline card-primary">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill"
                                href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home"
                                aria-selected="true">
                                <i class="fas fa-fire text-danger"></i> Top 5 Gastos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill"
                                href="#custom-tabs-four-profile" role="tab"
                                aria-controls="custom-tabs-four-profile" aria-selected="false">
                                <i class="fas fa-sync-alt text-warning"></i> Gastos Recurrentes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-messages-tab" data-toggle="pill"
                                href="#custom-tabs-four-messages" role="tab"
                                aria-controls="custom-tabs-four-messages" aria-selected="false">
                                <i class="fas fa-file-invoice text-success"></i> Pagos de Facturas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-settings-tab" data-toggle="pill"
                                href="#custom-tabs-four-settings" role="tab"
                                aria-controls="custom-tabs-four-settings" aria-selected="false">
                                <i class="fas fa-money-bill-wave text-danger"></i> Todos los Gastos
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-four-tabContent">
                        <!-- TAB 1: TOP 5 GASTOS -->
                        <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel"
                            aria-labelledby="custom-tabs-four-home-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Categoría</th>
                                            <th>Tipo</th>
                                            <th class="text-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($topGastos as $gasto)
                                            <tr>
                                                <td>
                                                    <span class="badge"
                                                        style="background-color: {{ $gasto->color ?? '#007bff' }}; 
                                                             width: 10px; height: 10px; display: inline-block; 
                                                             border-radius: 50%;"></span>
                                                    {{ $gasto->concepto }}
                                                </td>
                                                <td>{{ $gasto->categoria }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $gasto->tipo_gasto == 'Recurrente' ? 'badge-warning' : 'badge-secondary' }}">
                                                        {{ $gasto->tipo_gasto }}
                                                    </span>
                                                </td>
                                                <td class="text-right font-weight-bold text-danger">
                                                    $ {{ number_format($gasto->valor, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle"></i> No hay gastos registrados
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: GASTOS RECURRENTES -->
                        <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-four-profile-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Categoría</th>
                                            <th>Frecuencia</th>
                                            <th>Día</th>
                                            <th class="text-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($gastosRecurrentesDetalle as $gasto)
                                            <tr>
                                                <td>
                                                    <span class="badge"
                                                        style="background-color: {{ $gasto->color ?? '#ffc107' }}; 
                                                             width: 10px; height: 10px; display: inline-block; 
                                                             border-radius: 50%;"></span>
                                                    {{ $gasto->concepto }}
                                                </td>
                                                <td>{{ $gasto->categoria }}</td>
                                                <td>
                                                    <span class="badge badge-warning">
                                                        {{ ucfirst($gasto->frecuencia) }}
                                                    </span>
                                                </td>
                                                <td>Día {{ $gasto->dia_ejecucion }}</td>
                                                <td class="text-right font-weight-bold text-warning">
                                                    $ {{ number_format($gasto->valor, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle"></i> No hay gastos recurrentes
                                                    activos
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if (count($gastosRecurrentesDetalle) > 0)
                                        <tfoot>
                                            <tr class="bg-light">
                                                <th colspan="4" class="text-right">Total:</th>
                                                <th class="text-right text-warning">
                                                    $ {{ number_format($gastosRecurrentes, 2) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- TAB 3: PAGOS DE FACTURAS -->
                        <div class="tab-pane fade" id="custom-tabs-four-messages" role="tabpanel"
                            aria-labelledby="custom-tabs-four-messages-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Fecha</th>
                                            <th class="text-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pagosFacturas as $pago)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-file-invoice text-success"></i>
                                                    {{ $pago->concepto }}
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($pago->fecha)->format('d/m/Y') }}</td>
                                                <td class="text-right font-weight-bold text-success">
                                                    + $ {{ number_format($pago->valor, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle"></i> No hay pagos de facturas en este
                                                    mes
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 4: TODOS LOS GASTOS (SIN RECURRENTES) -->
                        <div class="tab-pane fade" id="custom-tabs-four-settings" role="tabpanel"
                            aria-labelledby="custom-tabs-four-settings-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Categoría</th>
                                            <th>Fecha</th>
                                            <th class="text-right">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalGastosNormales = 0;
                                        @endphp
                                        @forelse($gastosMovimientos as $gasto)
                                            @if ($gasto->tipo_gasto != 'Recurrente')
                                                @php
                                                    $totalGastosNormales += $gasto->valor;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-money-bill-wave text-danger"></i>
                                                        {{ $gasto->concepto }}
                                                    </td>
                                                    <td>{{ $gasto->categoria }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}
                                                    </td>
                                                    <td class="text-right font-weight-bold text-danger">
                                                        - $ {{ number_format($gasto->valor, 2) }}
                                                    </td>
                                                </tr>
                                            @endif
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle"></i> No hay gastos normales
                                                    registrados en este mes
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($totalGastosNormales > 0)
                                        <tfoot>
                                            <tr class="bg-light">
                                                <th colspan="3" class="text-right">Total Gastos Normales:</th>
                                                <th class="text-right text-danger">
                                                    $ {{ number_format($totalGastosNormales, 2) }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- FOOTER -->
    <div class="row mt-3">
        <div class="col-12 text-center text-muted">
            <small>
                Dashboard actualizado al mes de {{ $nombreMes }} |
                Días: {{ Carbon\Carbon::create($anoSeleccionado, $mesSeleccionado, 1)->daysInMonth }}
            </small>
        </div>
    </div>
</div>
