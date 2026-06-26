<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Financiero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
            font-size: 12px;
        }
        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }
        .card {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 5px;
            text-align: center;
            border-left: 4px solid #007bff;
        }
        .card .label {
            font-size: 11px;
            color: #7f8c8d;
            margin: 0;
        }
        .card .value {
            font-size: 18px;
            font-weight: bold;
            margin-top: 3px;
        }
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-info { color: #17a2b8; }
        .section-title {
            background: #e9ecef;
            padding: 8px 12px;
            margin: 15px 0 10px 0;
            border-radius: 3px;
            font-size: 14px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        th {
            background: #f8f9fa;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }
        td {
            padding: 6px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
            color: #7f8c8d;
            font-size: 10px;
        }
        .badge {
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            background: #ffc107;
            color: #333;
        }
        .total-row {
            background: #f8f9fa;
            font-weight: bold;
        }
        .total-row td {
            border-top: 2px solid #dee2e6;
        }
        .border-left-green { border-left-color: #28a745; }
        .border-left-red { border-left-color: #dc3545; }
        .border-left-blue { border-left-color: #17a2b8; }
        .border-left-purple { border-left-color: #6f42c1; }
    </style>
</head>
<body>
    <!-- HEADER -->
    <div class="header">
        <h1>Reporte Financiero</h1>
        <p>Período: {{ $nombreMes }}</p>
        <p>Fecha de generación: {{ $fechaGeneracion }}</p>
    </div>

    <!-- CARDS DE RESUMEN -->
    <div class="cards">
        <div class="card border-left-green">
            <p class="label">Total Ingresos</p>
            <p class="value text-success">$ {{ number_format($ingresos, 2) }}</p>
        </div>
        <div class="card border-left-red">
            <p class="label">Total Gastos</p>
            <p class="value text-danger">$ {{ number_format($gastos, 2) }}</p>
        </div>
        <div class="card border-left-blue">
            <p class="label">Saldo Neto</p>
            <p class="value {{ $saldoNeto >= 0 ? 'text-success' : 'text-danger' }}">
                $ {{ number_format($saldoNeto, 2) }}
            </p>
        </div>
        <div class="card border-left-purple">
            <p class="label">Tasa de Retención</p>
            <p class="value text-info">{{ $tasaRetencion }}%</p>
        </div>
    </div>

    <!-- RESUMEN FACTURAS -->
    <div class="section-title">📄 Resumen Facturas</div>
    <table>
        <tbody>
            <tr>
                <td style="width: 70%;">Total Facturas</td>
                <td class="text-right">$ {{ number_format($totalFacturas, 2) }}</td>
            </tr>
            <tr>
                <td>Facturas Pagadas</td>
                <td class="text-right text-success">$ {{ number_format($facturasPagadas, 2) }}</td>
            </tr>
            <tr>
                <td>Pendientes</td>
                <td class="text-right text-danger">$ {{ number_format($facturasPendientes, 2) }}</td>
            </tr>
            <tr>
                <td>Gastos Recurrentes</td>
                <td class="text-right text-warning">$ {{ number_format($gastosRecurrentes, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- GASTOS RECURRENTES -->
    @if(count($gastosRecurrentesDetalle) > 0)
        <div class="section-title">🔄 Gastos Recurrentes</div>
        <table>
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
                @foreach($gastosRecurrentesDetalle as $gasto)
                    <tr>
                        <td>{{ $gasto->concepto }}</td>
                        <td>{{ $gasto->categoria }}</td>
                        <td><span class="badge">{{ ucfirst($gasto->frecuencia) }}</span></td>
                        <td>Día {{ $gasto->dia_ejecucion }}</td>
                        <td class="text-right">$ {{ number_format($gasto->valor, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right">Total:</td>
                    <td class="text-right">$ {{ number_format($gastosRecurrentes, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    <!-- TODOS LOS GASTOS (SIN RECURRENTES) -->
    <div class="section-title">💰 Todos los Gastos (Sin Recurrentes)</div>
    <table>
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
                @if($gasto->tipo_gasto != 'Recurrente')
                    @php
                        $totalGastosNormales += $gasto->valor;
                    @endphp
                    <tr>
                        <td>{{ $gasto->concepto }}</td>
                        <td>{{ $gasto->categoria }}</td>
                        <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                        <td class="text-right text-danger">$ {{ number_format($gasto->valor, 2) }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="4" class="text-center">No hay gastos normales registrados</td>
                </tr>
            @endforelse
        </tbody>
        @if($totalGastosNormales > 0)
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total Gastos Normales:</td>
                    <td class="text-right text-danger">$ {{ number_format($totalGastosNormales, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <!-- FOOTER -->
    <div class="footer">
        <p>Reporte generado automáticamente - Sistema Financiero</p>
    </div>
</body>
</html>