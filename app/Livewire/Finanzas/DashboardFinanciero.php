<?php

namespace App\Livewire\Finanzas;

use Livewire\Component;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Gasto;
use App\Models\CategoriaGasto;
use App\Models\GastoRecurrente;
use App\Models\Ingreso;
use App\Models\SaldoAcumulado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PDF;

class DashboardFinanciero extends Component
{
    public $mesSeleccionado;
    public $anoSeleccionado;
    public $rangoFechas;
    public $mostrarReportePDF = false;

    protected $listeners = ['cambiarMes'];

    public function mount()
    {
        $this->mesSeleccionado = Carbon::now()->month;
        $this->anoSeleccionado = Carbon::now()->year;
        $this->rangoFechas = $this->obtenerRangoFechas();

        $this->calcularYGuardarSaldoAcumulado();
    }

    public function cambiarMes($direccion)
    {
        $fecha = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1);

        if ($direccion === 'anterior') {
            $fecha->subMonth();
        } elseif ($direccion === 'siguiente') {
            $fecha->addMonth();
        }

        $this->mesSeleccionado = $fecha->month;
        $this->anoSeleccionado = $fecha->year;
        $this->rangoFechas = $this->obtenerRangoFechas();
        $this->mostrarReportePDF = false;

        $this->calcularYGuardarSaldoAcumulado();
    }

    private function obtenerRangoFechas()
    {
        $inicio = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->startOfMonth();
        $fin = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->endOfMonth();

        return [
            'inicio' => $inicio,
            'fin' => $fin
        ];
    }

    private function calcularYGuardarSaldoAcumulado()
    {
        $mesAnterior = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->subMonth();
        $saldoAnterior = SaldoAcumulado::where('ano', $mesAnterior->year)
            ->where('mes', $mesAnterior->month)
            ->first();

        $saldoInicial = $saldoAnterior ? $saldoAnterior->saldo_acumulado : 0;

        $ingresosMes = $this->calcularIngresosDelMes();
        $gastosMes = $this->calcularGastosDelMes();

        $saldoNetoMes = $ingresosMes - $gastosMes;
        $saldoAcumulado = $saldoInicial + $saldoNetoMes;

        SaldoAcumulado::updateOrCreate(
            [
                'ano' => $this->anoSeleccionado,
                'mes' => $this->mesSeleccionado
            ],
            [
                'saldo_acumulado' => $saldoAcumulado
            ]
        );
    }

    private function calcularIngresosDelMes()
    {
        $pagos = Pago::whereBetween('fecha_pago', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])->sum('monto');

        $ingresos = Ingreso::whereBetween('fecha_ingreso', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])->where('estado', '!=', 'anulado')
            ->sum('monto');

        return $pagos + $ingresos;
    }

    /**
     * Calcula los gastos del mes con el nuevo enfoque de gastos recurrentes
     */
    private function calcularGastosDelMes()
    {
        // Gastos normales (NO recurrentes)
        $gastosNormales = Gasto::whereBetween('fecha_gasto', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])->where('estado', 'pagado')
            ->sum('valor');

        // Gastos recurrentes pagados en el mes (de la tabla gastos_recurrentes)
        $gastosRecurrentes = GastoRecurrente::where('ano', $this->anoSeleccionado)
            ->where('mes', $this->mesSeleccionado)
            ->where('pagado', true)
            ->sum('valor');

        return $gastosNormales + $gastosRecurrentes;
    }

    // Propiedades computadas públicas
    public function getIngresosDelMesProperty()
    {
        return $this->calcularIngresosDelMes();
    }

    public function getGastosDelMesProperty()
    {
        return $this->calcularGastosDelMes();
    }

    public function getSaldoNetoProperty()
    {
        return $this->ingresos_del_mes - $this->gastos_del_mes;
    }

    public function getSaldoAcumuladoProperty()
    {
        $saldo = SaldoAcumulado::where('ano', $this->anoSeleccionado)
            ->where('mes', $this->mesSeleccionado)
            ->first();

        return $saldo ? $saldo->saldo_acumulado : 0;
    }

    public function getSaldoAnteriorProperty()
    {
        $mesAnterior = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->subMonth();
        $saldo = SaldoAcumulado::where('ano', $mesAnterior->year)
            ->where('mes', $mesAnterior->month)
            ->first();

        return $saldo ? $saldo->saldo_acumulado : 0;
    }

    public function getIngresosPorDiaProperty()
    {
        $sql = "
        SELECT 
            fecha,
            SUM(total) as total
        FROM (
            SELECT 
                DATE(fecha_pago) as fecha,
                SUM(monto) as total
            FROM pagos
            WHERE fecha_pago BETWEEN ? AND ?
            GROUP BY DATE(fecha_pago)
            
            UNION ALL
            
            SELECT 
                DATE(fecha_ingreso) as fecha,
                SUM(monto) as total
            FROM ingresos
            WHERE fecha_ingreso BETWEEN ? AND ?
                AND estado != 'anulado'
            GROUP BY DATE(fecha_ingreso)
        ) as combined
        GROUP BY fecha
        ORDER BY fecha
        ";

        $resultados = DB::select($sql, [
            $this->rangoFechas['inicio']->format('Y-m-d'),
            $this->rangoFechas['fin']->format('Y-m-d'),
            $this->rangoFechas['inicio']->format('Y-m-d'),
            $this->rangoFechas['fin']->format('Y-m-d')
        ]);

        return collect($resultados)->map(function ($item) {
            return [
                'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                'total' => (float) $item->total
            ];
        });
    }

    public function getGastosPorDiaProperty()
    {
        return Gasto::select(
            DB::raw('DATE(fecha_gasto) as fecha'),
            DB::raw('SUM(valor) as total')
        )
            ->whereBetween('fecha_gasto', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ])
            ->where('estado', 'pagado')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get()
            ->map(function ($item) {
                return [
                    'fecha' => Carbon::parse($item->fecha)->format('d/m'),
                    'total' => $item->total
                ];
            });
    }

    public function getGastosPorCategoriaProperty()
    {
        return Gasto::select(
            'categorias_gastos.nombre',
            'categorias_gastos.color',
            DB::raw('SUM(gastos.valor) as total')
        )
            ->join('categorias_gastos', 'gastos.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->whereBetween('gastos.fecha_gasto', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ])
            ->where('gastos.estado', 'pagado')
            ->groupBy('categorias_gastos.id', 'categorias_gastos.nombre', 'categorias_gastos.color')
            ->orderBy('total', 'desc')
            ->get();
    }

    public function getFacturasPendientesProperty()
    {
        return Factura::where('estado', 'pendiente')
            ->where(function ($query) {
                $query->whereBetween('fecha_emision', [
                    $this->rangoFechas['inicio'],
                    $this->rangoFechas['fin']
                ])
                    ->orWhereBetween('fecha_vencimiento', [
                        $this->rangoFechas['inicio'],
                        $this->rangoFechas['fin']
                    ]);
            })
            ->sum('saldo_pendiente');
    }

    public function getTotalFacturasProperty()
    {
        return Factura::where(function ($query) {
            $query->whereBetween('fecha_emision', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ])
                ->orWhereBetween('fecha_vencimiento', [
                    $this->rangoFechas['inicio'],
                    $this->rangoFechas['fin']
                ]);
        })->sum('monto_total');
    }

    public function getFacturasPagadasProperty()
    {
        return Factura::where('estado', 'pagada')
            ->where(function ($query) {
                $query->whereBetween('fecha_emision', [
                    $this->rangoFechas['inicio'],
                    $this->rangoFechas['fin']
                ])
                    ->orWhereBetween('fecha_vencimiento', [
                        $this->rangoFechas['inicio'],
                        $this->rangoFechas['fin']
                    ]);
            })
            ->sum('monto_total');
    }

    /**
     * Obtener gastos recurrentes base (total de gastos fijos mensuales)
     */
    public function getGastosRecurrentesMensualesProperty()
    {
        return GastoRecurrente::whereNull('ano')
            ->whereNull('mes')
            ->where('activo', true)
            ->sum('valor');
    }

    /**
     * Obtener gastos recurrentes pagados en el mes actual
     */
    public function getGastosRecurrentesPagadosProperty()
    {
        return GastoRecurrente::where('ano', $this->anoSeleccionado)
            ->where('mes', $this->mesSeleccionado)
            ->where('pagado', true)
            ->sum('valor');
    }

    public function getTopGastosProperty()
    {
        $gastosNormales = Gasto::select(
            'gastos.concepto',
            'gastos.valor',
            'gastos.fecha_gasto',
            'categorias_gastos.nombre as categoria',
            'categorias_gastos.color',
            DB::raw("'Normal' as tipo_gasto")
        )
            ->join('categorias_gastos', 'gastos.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->whereBetween('gastos.fecha_gasto', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ])
            ->where('gastos.estado', 'pagado');

        // Gastos recurrentes pagados en el mes (solo los que se pagaron)
        $gastosRecurrentes = GastoRecurrente::select(
            'gastos_recurrentes.concepto',
            'gastos_recurrentes.valor',
            DB::raw('NULL as fecha_gasto'),
            'categorias_gastos.nombre as categoria',
            'categorias_gastos.color',
            DB::raw("'Recurrente' as tipo_gasto")
        )
            ->join('categorias_gastos', 'gastos_recurrentes.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->where('gastos_recurrentes.ano', $this->anoSeleccionado)
            ->where('gastos_recurrentes.mes', $this->mesSeleccionado)
            ->where('gastos_recurrentes.pagado', true);

        return $gastosNormales->union($gastosRecurrentes)
            ->orderBy('valor', 'desc')
            ->limit(5)
            ->get();
    }

    public function getPagosFacturasProperty()
    {
        return Pago::select(
            'pagos.fecha_pago as fecha',
            'pagos.monto as valor',
            DB::raw("CONCAT('Pago de factura #', facturas.numero_factura) as concepto"),
            'facturas.estado as estado_factura'
        )
            ->join('facturas', 'pagos.factura_id', '=', 'facturas.id')
            ->whereBetween('pagos.fecha_pago', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ])
            ->orderBy('pagos.fecha_pago', 'desc')
            ->limit(10)
            ->get();
    }

    public function getGastosMovimientosProperty()
    {
        // Gastos normales
        $gastosNormales = Gasto::select(
            'gastos.fecha_gasto as fecha',
            'gastos.valor',
            'gastos.concepto',
            'categorias_gastos.nombre as categoria',
            DB::raw("'Normal' as tipo_gasto"),
            'gastos.estado'
        )
            ->join('categorias_gastos', 'gastos.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->whereBetween('gastos.fecha_gasto', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ]);

        // Gastos recurrentes pagados en el mes (solo los que se pagaron)
        $gastosRecurrentes = GastoRecurrente::select(
            'gastos_recurrentes.fecha_pago as fecha',
            'gastos_recurrentes.valor',
            DB::raw("CONCAT(gastos_recurrentes.concepto, ' (Recurrente)') as concepto"),
            'categorias_gastos.nombre as categoria',
            DB::raw("'Recurrente' as tipo_gasto"),
            DB::raw("'pagado' as estado")
        )
            ->join('categorias_gastos', 'gastos_recurrentes.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->where('gastos_recurrentes.ano', $this->anoSeleccionado)
            ->where('gastos_recurrentes.mes', $this->mesSeleccionado)
            ->where('gastos_recurrentes.pagado', true);

        $gastos = $gastosNormales->union($gastosRecurrentes)
            ->orderBy('fecha', 'desc')
            ->limit(15)
            ->get();

        return $gastos;
    }

    /**
     * Detalle de gastos recurrentes - solo gastos base
     */
    /**
     * 🔥 DETALLE DE GASTOS RECURRENTES - SOLO LOS PAGADOS EN EL MES
     */
    public function getGastosRecurrentesDetalleProperty()
    {
        // 🔥 SOLO GASTOS RECURRENTES PAGADOS EN EL MES SELECCIONADO
        $gastosPagados = GastoRecurrente::where('ano', $this->anoSeleccionado)
            ->where('mes', $this->mesSeleccionado)
            ->where('pagado', true)
            ->with('categoria')
            ->get();

        $resultado = [];
        foreach ($gastosPagados as $gasto) {
            $resultado[] = (object)[
                'id' => $gasto->id,
                'concepto' => $gasto->concepto,
                'valor' => $gasto->valor,
                'frecuencia' => $gasto->frecuencia,
                'dia_ejecucion' => $gasto->dia_ejecucion,
                'categoria' => $gasto->categoria ? $gasto->categoria->nombre : 'Sin categoría',
                'color' => $gasto->categoria ? $gasto->categoria->color : '#ffc107',
                'fecha_pago_mes' => $gasto->fecha_pago,
            ];
        }

        return collect($resultado);
    }

    public function getTasaRetencionProperty()
    {
        $totalIngresos = $this->ingresos_del_mes;
        $totalGastos = $this->gastos_del_mes;

        if ($totalIngresos == 0) return 0;

        return round((($totalIngresos - $totalGastos) / $totalIngresos) * 100, 2);
    }

    public function getIngresosRegistradosProperty()
    {
        return Ingreso::whereBetween('fecha_ingreso', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])->where('estado', '!=', 'anulado')
            ->sum('monto');
    }

    public function generarReporte()
    {
        $this->mostrarReportePDF = true;
    }

    public function cerrarReporte()
    {
        $this->mostrarReportePDF = false;
    }

    public function generarPDF()
    {
        $ingresosList = Ingreso::whereBetween('fecha_ingreso', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])
            ->where('estado', '!=', 'anulado')
            ->orderBy('fecha_ingreso', 'desc')
            ->get();

        // Obtener gastos recurrentes pagados en el mes
        $gastosRecurrentesPagados = GastoRecurrente::where('ano', $this->anoSeleccionado)
            ->where('mes', $this->mesSeleccionado)
            ->where('pagado', true)
            ->get();

        $data = [
            'nombreMes' => $this->getNombreMes(),
            'ingresos' => $this->ingresos_del_mes,
            'ingresosRegistrados' => $this->ingresos_registrados,
            'gastos' => $this->gastos_del_mes,
            'gastosRecurrentesPagados' => $gastosRecurrentesPagados,
            'saldoNeto' => $this->saldo_neto,
            'saldoAcumulado' => $this->saldo_acumulado,
            'saldoAnterior' => $this->saldo_anterior,
            'tasaRetencion' => $this->tasa_retencion,
            'gastosPorCategoria' => $this->gastos_por_categoria,
            'topGastos' => $this->top_gastos,
            'gastosRecurrentes' => $this->gastos_recurrentes_mensuales,
            'gastosRecurrentesDetalle' => $this->gastos_recurrentes_detalle,
            'gastosMovimientos' => $this->gastos_movimientos,
            'pagosFacturas' => $this->pagos_facturas,
            'ingresosList' => $ingresosList,
            'totalFacturas' => $this->total_facturas,
            'facturasPagadas' => $this->facturas_pagadas,
            'facturasPendientes' => $this->facturas_pendientes,
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
            'mesSeleccionado' => $this->mesSeleccionado,
            'anoSeleccionado' => $this->anoSeleccionado
        ];

        $pdf = PDF::loadView('pdf.reporte-financiero', $data);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-financiero-' . $this->anoSeleccionado . '-' . $this->mesSeleccionado . '.pdf');
    }

    public function getNombreMes()
    {
        return Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->translatedFormat('F Y');
    }

    public function render()
    {
        $ingresosList = Ingreso::whereBetween('fecha_ingreso', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])
            ->where('estado', '!=', 'anulado')
            ->orderBy('fecha_ingreso', 'desc')
            ->limit(15)
            ->get();

        return view('livewire.finanzas.dashboard-financiero', [
            'ingresos' => $this->ingresos_del_mes,
            'ingresosRegistrados' => $this->ingresos_registrados,
            'gastos' => $this->gastos_del_mes,
            'gastosRecurrentesPagados' => $this->gastos_recurrentes_pagados,
            'saldoNeto' => $this->saldo_neto,
            'saldoAcumulado' => $this->saldo_acumulado,
            'saldoAnterior' => $this->saldo_anterior,
            'ingresosPorDia' => $this->ingresos_por_dia,
            'gastosPorDia' => $this->gastos_por_dia,
            'gastosPorCategoria' => $this->gastos_por_categoria,
            'facturasPendientes' => $this->facturas_pendientes,
            'totalFacturas' => $this->total_facturas,
            'facturasPagadas' => $this->facturas_pagadas,
            'gastosRecurrentes' => $this->gastos_recurrentes_mensuales,
            'gastosRecurrentesDetalle' => $this->gastos_recurrentes_detalle,
            'topGastos' => $this->top_gastos,
            'pagosFacturas' => $this->pagos_facturas,
            'gastosMovimientos' => $this->gastos_movimientos,
            'tasaRetencion' => $this->tasa_retencion,
            'nombreMes' => $this->getNombreMes(),
            'ingresosList' => $ingresosList,
        ]);
    }
}
