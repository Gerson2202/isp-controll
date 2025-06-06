<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Contrato;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardFinanciero extends Component
{
    public $rangoFechas = 'mes_actual';
    public $fechaInicio;
    public $fechaFin;
    public $mostrarPagosPorUsuario = false;
    public $usuarioSeleccionado = null;
    public Collection $detallePagosUsuario;   
    public array $resumenPagos = [
        'efectivo' => ['cantidad' => 0, 'total' => 0],
        'transferencia' => ['cantidad' => 0, 'total' => 0],
        'tarjeta' => ['cantidad' => 0, 'total' => 0],
        'total_general' => 0
    ];

    public function mount()
    {
        $this->fechaInicio = now()->startOfMonth()->toDateString();
        $this->fechaFin = now()->endOfMonth()->toDateString();
        $this->detallePagosUsuario = collect();
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
            'ingresosPorPlan' => $this->calcularIngresosPorPlan(),
            'pagosPorUsuario' => $this->getPagosPorUsuario()
        ];
    }

    protected function calcularIngresosPorPlan()
    {
        return Contrato::with('plan')
            ->selectRaw('SUM(facturas.monto_total) as total, contratos.plan_id')
            ->join('facturas', 'facturas.contrato_id', '=', 'contratos.id')
            ->groupBy('contratos.plan_id')
            ->get()
            ->pluck('total', 'plan.nombre')
            ->toArray();
    }

    public function getPagosPorUsuario()
    {
        $query = Pago::with('usuario')
            ->selectRaw('user_id, COUNT(*) as count, SUM(monto) as total')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('total');

        // Filtrar por rango de fechas
        if ($this->rangoFechas === 'mes_actual') {
            $query->whereBetween('fecha_pago', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($this->rangoFechas === 'mes_pasado') {
            $query->whereBetween('fecha_pago', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
        } else {
            $query->whereBetween('fecha_pago', [$this->fechaInicio, $this->fechaFin]);
        }

        $pagos = $query->get();

        // Agrupar por usuario real
        $grouped = $pagos->mapWithKeys(function ($row) {
            $usuario = $row->usuario;
            $nombre = $usuario ? ($usuario->name ?? $usuario->email ?? 'Usuario #' . $row->user_id) : 'Desconocido';
            return [
                $row->user_id => [
                    'nombre' => $nombre,
                    'total' => $row->total,
                    'count' => $row->count,
                    'user_id' => $row->user_id
                ]
            ];
        });

        return $grouped;
    }

     public function verDetalleUsuario($userId)
    {
        $this->usuarioSeleccionado = $userId;

        $query = Pago::with(['factura.contrato.cliente'])
            ->where('user_id', $userId)
            ->orderBy('fecha_pago', 'desc');

        // Filtrar por rango de fechas
        if ($this->rangoFechas === 'mes_actual') {
            $query->whereBetween('fecha_pago', [now()->startOfMonth(), now()->endOfMonth()]);
        } elseif ($this->rangoFechas === 'mes_pasado') {
            $query->whereBetween('fecha_pago', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
        } else {
            $query->whereBetween('fecha_pago', [$this->fechaInicio, $this->fechaFin]);
        }

        $this->detallePagosUsuario = $query->get();
        
        $this->calcularResumenPagos();
    }

    protected function calcularResumenPagos()
    {
        $this->resumenPagos = [
            'efectivo' => [
                'cantidad' => $this->detallePagosUsuario->where('metodo_pago', 'efectivo')->count(),
                'total' => $this->detallePagosUsuario->where('metodo_pago', 'efectivo')->sum('monto')
            ],
            'transferencia' => [
                'cantidad' => $this->detallePagosUsuario->where('metodo_pago', 'transferencia')->count(),
                'total' => $this->detallePagosUsuario->where('metodo_pago', 'transferencia')->sum('monto')
            ],
            'tarjeta' => [
                'cantidad' => $this->detallePagosUsuario->where('metodo_pago', 'tarjeta')->count(),
                'total' => $this->detallePagosUsuario->where('metodo_pago', 'tarjeta')->sum('monto')
            ],
            'total_general' => $this->detallePagosUsuario->sum('monto')
        ];
    }

    public function cerrarModal()
    {
        $this->usuarioSeleccionado = null;
        $this->detallePagosUsuario = collect(); // Reiniciar como colección vacía
    }

    public function render()
    {
        return view('livewire.facturacion.dashboard-financiero', [
            'estadisticas' => $this->estadisticas
        ]);
    }
}