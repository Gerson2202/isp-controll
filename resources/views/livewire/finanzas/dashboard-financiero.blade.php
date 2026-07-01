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
                            'saldoAcumulado' => $saldoAcumulado, // 🔥 AGREGAR
                            'saldoAnterior' => $saldoAnterior, // 🔥 AGREGAR
                            'tasaRetencion' => $tasaRetencion,
                            'gastosPorCategoria' => $gastosPorCategoria,
                            'topGastos' => $topGastos,
                            'gastosRecurrentes' => $gastosRecurrentes,
                            'gastosRecurrentesDetalle' => $gastosRecurrentesDetalle,
                            'gastosMovimientos' => $gastosMovimientos,
                            'ingresosList' => $ingresosList, // 🔥 AGREGAR
                            'totalFacturas' => $totalFacturas, // 🔥 AGREGAR
                            'facturasPagadas' => $facturasPagadas, // 🔥 AGREGAR
                            'facturasPendientes' => $facturasPendientes, // 🔥 AGREGAR
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
                    <h3>$ {{ number_format($ingresos, 0) }}</h3>
                    <p>Total Ingresos + facturación</p>
                </div>
                <div class="icon">
                    <i class="fas fa-arrow-up"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>$ {{ number_format($gastos, 0) }}</h3>
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
                    <h3>$ {{ number_format($saldoNeto, 0) }}</h3>
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
                    <h3>$ {{ number_format($ingresosRegistrados, 0) }}</h3>
                    <p>Total Ingresos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Saldo acomulado -->
    <div class="row">
        <div class="col-lg-12">
            <div class="small-box bg-gradient-purple">
                <div class="inner">
                    <h3>$ {{ number_format($saldoAcumulado, 2) }}</h3>
                    <p>
                        💰 Saldo Acumulado (Bolsillo Fijo)
                        <small class="text-light">
                            <i class="fas fa-info-circle"
                                title="Este valor es fijo y se mantiene igual para todos los meses"></i>
                        </small>
                    </p>
                    <small class="text-light">
                        <i class="fas fa-check-circle text-success"></i>
                        Valor total acumulado de todos los meses
                    </small>
                </div>
                <div class="icon">
                    <i class="fas fa-piggy-bank"></i>
                </div>
                {{-- <!-- Botón para recalcular manualmente -->
                <button wire:click="calcularYGuardarSaldoAcumulado" class="btn btn-sm btn-light"
                    style="position: absolute; bottom: 10px; right: 10px;">
                    <i class="fas fa-sync-alt"></i> Recalcular
                </button> --}}
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
                        <div class="d-flex align-items-end justify-content-between"
                            style="height: 220px; gap: 4px; padding-top: 20px;">
                            @php
                                $maxIngresos = max(array_column($ingresosPorDia->toArray(), 'total'));
                            @endphp
                            @foreach ($ingresosPorDia as $dia)
                                @php
                                    $altura = ($dia['total'] / $maxIngresos) * 180;
                                    $altura = max($altura, 5);
                                @endphp
                                <div class="text-center"
                                    style="flex: 1; min-width: 20px; height: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
                                    @if ($dia['total'] > 0)
                                        <small class="text-success font-weight-bold"
                                            style="font-size: 9px; margin-bottom: 2px;">
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
                        <div class="d-flex align-items-end justify-content-between"
                            style="height: 220px; gap: 4px; padding-top: 20px;">
                            @php
                                $maxGastos = max(array_column($gastosPorDia->toArray(), 'total'));
                            @endphp
                            @foreach ($gastosPorDia as $dia)
                                @php
                                    $altura = ($dia['total'] / $maxGastos) * 180;
                                    $altura = max($altura, 5);
                                @endphp
                                <div class="text-center"
                                    style="flex: 1; min-width: 20px; height: 100%; display: flex; flex-direction: column; justify-content: flex-end;">
                                    @if ($dia['total'] > 0)
                                        <small class="text-danger font-weight-bold"
                                            style="font-size: 9px; margin-bottom: 2px;">
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
                                    <small class="text-muted mt-1"
                                        style="font-size: 10px;">{{ $dia['fecha'] }}</small>
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
                        <!-- 🔥 NUEVO TAB DE INGRESOS -->
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-four-ingresos-tab" data-toggle="pill"
                                href="#custom-tabs-four-ingresos" role="tab"
                                aria-controls="custom-tabs-four-ingresos" aria-selected="false">
                                <i class="fas fa-coins text-success"></i> Ingresos
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
                                                <td class="text-center">
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i> Pagado
                                                    </span>
                                                    <small class="d-block text-muted">
                                                        {{ $gasto->fecha_pago_mes ? $gasto->fecha_pago_mes->format('d/m/Y') : '' }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle"></i> No hay gastos recurrentes
                                                    pagados en {{ $nombreMes }}
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
                        <!-- TAB 5: INGRESOS -->
                        <div class="tab-pane fade" id="custom-tabs-four-ingresos" role="tabpanel"
                            aria-labelledby="custom-tabs-four-ingresos-tab">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th>Tipo</th>
                                            <th>Fecha</th>
                                            <th>Cliente</th>
                                            <th>Método Pago</th>
                                            <th>Estado</th>
                                            <th class="text-right">Monto</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $ingresosList = App\Models\Ingreso::whereBetween('fecha_ingreso', [
                                                $rangoFechas['inicio'],
                                                $rangoFechas['fin'],
                                            ])
                                                ->where('estado', '!=', 'anulado')
                                                ->orderBy('fecha_ingreso', 'desc')
                                                ->limit(15)
                                                ->get();
                                        @endphp
                                        @forelse($ingresosList as $ingreso)
                                            <tr>
                                                <td>
                                                    <i class="fas fa-arrow-up text-success"></i>
                                                    {{ $ingreso->concepto }}
                                                    @if ($ingreso->descripcion)
                                                        <small
                                                            class="d-block text-muted">{{ $ingreso->descripcion }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge badge-success px-3 py-2">
                                                        @if ($ingreso->tipo == 'instalacion')
                                                            <i class="fas fa-tools me-1"></i> Instalación
                                                        @elseif($ingreso->tipo == 'servicio_extra')
                                                            <i class="fas fa-wrench me-1"></i> Servicio Extra
                                                        @elseif($ingreso->tipo == 'venta_producto')
                                                            <i class="fas fa-box me-1"></i> Venta Producto
                                                        @elseif($ingreso->tipo == 'consultoria')
                                                            <i class="fas fa-clipboard-list me-1"></i> Consultoría
                                                        @else
                                                            <i class="fas fa-ellipsis-h me-1"></i> Otro
                                                        @endif
                                                    </span>
                                                </td>
                                                <td>{{ $ingreso->fecha_ingreso->format('d/m/Y') }}</td>
                                                <td>
                                                    @if ($ingreso->cliente)
                                                        <span class="badge bg-info px-3 py-2">
                                                            <i class="fas fa-user me-1"></i>
                                                            {{ $ingreso->cliente->nombre }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($ingreso->metodo_pago)
                                                        <span
                                                            class="badge bg-secondary">{{ $ingreso->metodo_pago }}</span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($ingreso->estado == 'confirmado')
                                                        <span class="badge bg-success px-3 py-2">
                                                            <i class="fas fa-check-circle me-1"></i> Confirmado
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger px-3 py-2">
                                                            <i class="fas fa-times-circle me-1"></i> Anulado
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-right font-weight-bold text-success">
                                                    + $ {{ number_format($ingreso->monto, 2) }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    <i class="fas fa-info-circle"></i> No hay ingresos registrados en
                                                    este mes
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($ingresosList->count() > 0)
                                        <tfoot>
                                            <tr class="bg-light">
                                                <th colspan="6" class="text-right">Total Ingresos:</th>
                                                <th class="text-right text-success">
                                                    $ {{ number_format($ingresosList->sum('monto'), 2) }}
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
