<?php

namespace App\Livewire\Facturacion;

use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class MisPagos extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $mes;
    public $anio;
    public $metodoPago = '';
    public $estadoFactura = '';
    public $perPage = 10;

    protected $queryString = [
        'search',
        'mes',
        'anio',
        'metodoPago',
        'estadoFactura'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->reset([
            'search',
            'mes',
            'anio',
            'metodoPago',
            'estadoFactura'
        ]);

        $this->resetPage();
    }

    public function render()
    {
        $query = Pago::with([
            'factura.contrato.cliente',
            'factura.contrato.plan',
            'user'
        ])

            ->where('user_id', Auth::id())

            ->when($this->search, function ($q) {

                $search = $this->search;

                $q->where(function ($sub) use ($search) {

                    $sub->whereHas('factura.contrato.cliente', function ($cliente) use ($search) {
                        $cliente->where('nombre', 'like', '%' . $search . '%');
                    });

                    $sub->orWhereHas('factura', function ($factura) use ($search) {
                        $factura->where('numero_factura', 'like', '%' . $search . '%');
                    });

                    $sub->orWhereHas('factura.contrato', function ($contrato) use ($search) {
                        $contrato->where('id', 'like', '%' . $search . '%');
                    });
                });
            })

            ->when($this->mes, function ($q) {
                $q->whereMonth('fecha_pago', $this->mes);
            })

            ->when($this->anio, function ($q) {
                $q->whereYear('fecha_pago', $this->anio);
            })

            ->when($this->metodoPago, function ($q) {
                $q->where('metodo_pago', $this->metodoPago);
            })

            ->when($this->estadoFactura, function ($q) {

                $estado = $this->estadoFactura;

                $q->whereHas('factura', function ($factura) use ($estado) {
                    $factura->where('estado', $estado);
                });
            })

            ->latest('fecha_pago');

        $pagos = $query->paginate($this->perPage);

        $totalRecaudado = (clone $query)->sum('monto');

        $totalPagos = (clone $query)->count();

        $promedioPago = (clone $query)->avg('monto');

        $clientesUnicos = Pago::where('user_id', Auth::id())
            ->join('facturas', 'pagos.factura_id', '=', 'facturas.id')
            ->join('contratos', 'facturas.contrato_id', '=', 'contratos.id')
            ->distinct('contratos.cliente_id')
            ->count('contratos.cliente_id');

        $recaudoMensual = Pago::select(
            DB::raw('MONTH(fecha_pago) as mes'),
            DB::raw('YEAR(fecha_pago) as anio'),
            DB::raw('SUM(monto) as total'),
            DB::raw('COUNT(*) as cantidad')
        )
            ->where('user_id', Auth::id())
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('livewire.facturacion.mis-pagos', [
            'pagos' => $pagos,
            'totalRecaudado' => $totalRecaudado,
            'totalPagos' => $totalPagos,
            'promedioPago' => $promedioPago,
            'clientesUnicos' => $clientesUnicos,
            'recaudoMensual' => $recaudoMensual,
        ]);
    }
}
