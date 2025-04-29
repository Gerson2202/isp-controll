<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Contrato;
use Carbon\Carbon;

class DashboardFinanciero extends Component
{
    public $rangoFechas = 'mes_actual'; // mes_actual|mes_pasado|personalizado
    public $fechaInicio;
    public $fechaFin;

    public function mount()
    {
        $this->fechaInicio = now()->startOfMonth()->toDateString();
        $this->fechaFin = now()->endOfMonth()->toDateString();
    }
    public function updatedRangoFechas($value)
    {
        if ($value === 'mes_actual') {
            $this->fechaInicio = now()->startOfMonth()->toDateString();
            $this->fechaFin = now()->endOfMonth()->toDateString();
        } elseif ($value === 'mes_pasado') {
            $this->fechaInicio = now()->subMonth()->startOfMonth()->toDateString();
            $this->fechaFin = now()->subMonth()->endOfMonth()->toDateString();
        }
        // Para "personalizado", usa las fechas ya ingresadas
    }

    public function getEstadisticasProperty()
    {
        $query = Factura::query();
        $queryPagos = Pago::query();

        // Filtrar por rango de fechas
        if ($this->rangoFechas === 'mes_actual') {
            $query->whereBetween('fecha_emision', [now()->startOfMonth(), now()->endOfMonth()]);
            $queryPagos->whereBetween('fecha_pago', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($this->rangoFechas === 'mes_pasado') {
            $query->whereBetween('fecha_emision', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
            $queryPagos->whereBetween('fecha_pago', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
        } else {
            $query->whereBetween('fecha_emision', [$this->fechaInicio, $this->fechaFin]);
            $queryPagos->whereBetween('fecha_pago', [$this->fechaInicio, $this->fechaFin]);
        }

        return [
            'totalFacturado' => $query->sum('monto_total'),
            'totalPagado' => $queryPagos->sum('monto'),
            'facturasPendientes' => $query->where('estado', 'pendiente')->count(),
            'facturasVencidas' => Factura::where('estado', 'vencida')->count(),
            'clientesActivos' => Contrato::where('estado', 'activo')->count(),
            'ingresosPorPlan' => $this->calcularIngresosPorPlan()
        ];
    }

    protected function calcularIngresosPorPlan()
    {
        return Contrato::with('plan')
            ->selectRaw('SUM(facturas.monto_total) as total, contratos.plan_id')
            ->join('facturas', 'facturas.contrato_id', '=', 'contratos.id')
            ->groupBy('contratos.plan_id')
            ->get()
            ->pluck('total', 'plan.nombre') // Transforma a array asociativo [nombre_plan => total]
            ->toArray(); // Convierte explícitamente a array
    }
    

    public function render()
    {
        return view('livewire.facturacion.dashboard-financiero', [
            'estadisticas' => $this->estadisticas
        ]);
    }
}