<?php

namespace App\Livewire\Finanzas;

use Livewire\Component;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Gasto;
use App\Models\CategoriaGasto;
use App\Models\GastoRecurrente;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

    public function getNombreMes()
    {
        return Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->translatedFormat('F Y');
    }

    public function getIngresosDelMesProperty()
    {
        return Pago::whereBetween('fecha_pago', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])->sum('monto');
    }

    public function getGastosDelMesProperty()
    {
        $gastosNormales = Gasto::whereBetween('fecha_gasto', [
            $this->rangoFechas['inicio'],
            $this->rangoFechas['fin']
        ])->where('estado', 'pagado')
            ->sum('valor');

        $gastosRecurrentes = GastoRecurrente::where('activo', true)
            ->where('frecuencia', 'mensual')
            ->sum('valor');

        return $gastosNormales + $gastosRecurrentes;
    }

    public function getSaldoNetoProperty()
    {
        return $this->ingresos_del_mes - $this->gastos_del_mes;
    }

    public function getIngresosPorDiaProperty()
    {
        return Pago::select(
            DB::raw('DATE(fecha_pago) as fecha'),
            DB::raw('SUM(monto) as total')
        )
            ->whereBetween('fecha_pago', [
                $this->rangoFechas['inicio'],
                $this->rangoFechas['fin']
            ])
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

    public function getGastosRecurrentesMensualesProperty()
    {
        return GastoRecurrente::where('activo', true)
            ->where('frecuencia', 'mensual')
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

        $gastosRecurrentes = GastoRecurrente::select(
            'gastos_recurrentes.concepto',
            'gastos_recurrentes.valor',
            DB::raw('NULL as fecha_gasto'),
            'categorias_gastos.nombre as categoria',
            'categorias_gastos.color',
            DB::raw("'Recurrente' as tipo_gasto")
        )
            ->join('categorias_gastos', 'gastos_recurrentes.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->where('gastos_recurrentes.activo', true)
            ->where('gastos_recurrentes.frecuencia', 'mensual');

        return $gastosNormales->union($gastosRecurrentes)
            ->orderBy('valor', 'desc')
            ->limit(5)
            ->get();
    }

    // CORREGIDO: Pagos de facturas separados
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

    // CORREGIDO: Gastos separados (incluye todos los gastos)
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

        // Gastos recurrentes
        $gastosRecurrentes = GastoRecurrente::select(
            DB::raw("'" . $this->rangoFechas['inicio']->format('Y-m-d') . "' as fecha"),
            'gastos_recurrentes.valor',
            DB::raw("CONCAT(gastos_recurrentes.concepto, ' (Recurrente)') as concepto"),
            'categorias_gastos.nombre as categoria',
            DB::raw("'Recurrente' as tipo_gasto"),
            DB::raw("'pagado' as estado")
        )
            ->join('categorias_gastos', 'gastos_recurrentes.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->where('gastos_recurrentes.activo', true)
            ->where('gastos_recurrentes.frecuencia', 'mensual');

        // Unir ambos y ordenar
        $gastos = $gastosNormales->union($gastosRecurrentes)
            ->orderBy('fecha', 'desc')
            ->limit(15)
            ->get();

        return $gastos;
    }

    public function getGastosRecurrentesDetalleProperty()
    {
        return GastoRecurrente::select(
            'gastos_recurrentes.concepto',
            'gastos_recurrentes.valor',
            'gastos_recurrentes.frecuencia',
            'gastos_recurrentes.dia_ejecucion',
            'categorias_gastos.nombre as categoria',
            'categorias_gastos.color'
        )
            ->join('categorias_gastos', 'gastos_recurrentes.categorias_gasto_id', '=', 'categorias_gastos.id')
            ->where('gastos_recurrentes.activo', true)
            ->where('gastos_recurrentes.frecuencia', 'mensual')
            ->get();
    }

    public function getTasaRetencionProperty()
    {
        $totalIngresos = $this->ingresos_del_mes;
        $totalGastos = $this->gastos_del_mes;

        if ($totalIngresos == 0) return 0;

        return round((($totalIngresos - $totalGastos) / $totalIngresos) * 100, 2);
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
        $data = [
            'nombreMes' => $this->getNombreMes(),
            'ingresos' => $this->ingresos_del_mes,
            'gastos' => $this->gastos_del_mes,
            'saldoNeto' => $this->saldo_neto,
            'tasaRetencion' => $this->tasa_retencion,
            'gastosPorCategoria' => $this->gastos_por_categoria,
            'topGastos' => $this->top_gastos,
            'gastosRecurrentes' => $this->gastos_recurrentes_mensuales,
            'gastosRecurrentesDetalle' => $this->gastos_recurrentes_detalle,
            'gastosMovimientos' => $this->gastos_movimientos,  // Cambiado
            'fechaGeneracion' => Carbon::now()->format('d/m/Y H:i:s'),
            'mesSeleccionado' => $this->mesSeleccionado,
            'anoSeleccionado' => $this->anoSeleccionado
        ];

        $pdf = PDF::loadView('pdf.reporte-financiero', $data);
        $pdf->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'reporte-gastos-' . $this->anoSeleccionado . '-' . $this->mesSeleccionado . '.pdf');
    }
    public function render()
    {
        return view('livewire.finanzas.dashboard-financiero', [
            'ingresos' => $this->ingresos_del_mes,
            'gastos' => $this->gastos_del_mes,
            'saldoNeto' => $this->saldo_neto,
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
        ]);
    }
}
