<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Inventario;
use App\Models\Nodo;
use App\Models\Pago;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | CLIENTES POR MES
        |--------------------------------------------------------------------------
        */

        $clientesPorMes = Cliente::select(
            DB::raw("MONTH(created_at) as mes"),
            DB::raw("COUNT(*) as total")
        )
            ->whereYear('created_at', date('Y'))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        $nombresMeses = [
            1 => 'Ene',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Abr',
            5 => 'May',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Ago',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dic'
        ];

        $mesesClientes = [];
        $totalesClientes = [];

        foreach ($clientesPorMes as $item) {
            $mesesClientes[] = $nombresMeses[$item->mes];
            $totalesClientes[] = $item->total;
        }

        /*
        |--------------------------------------------------------------------------
        | CONTABILIDAD: FACTURADO VS PAGADO
        |--------------------------------------------------------------------------
        */

        // inicializar 12 meses en 0
        $facturadoPorMes = array_fill(1, 12, 0);
        $pagadoPorMes = array_fill(1, 12, 0);

        /*
        |-------------------------
        | FACTURADO (REAL)
        |-------------------------
        */
        $facturas = Factura::select(
            DB::raw("MONTH(fecha_emision) as mes"),
            DB::raw("SUM(monto_total) as total")
        )
            ->whereYear('fecha_emision', date('Y'))
            ->groupBy('mes')
            ->get();

        foreach ($facturas as $item) {
            $facturadoPorMes[$item->mes] = (float) $item->total;
        }

        /*
        |-------------------------
        | PAGADO (REAL)
        |-------------------------
        */
        $pagos = Pago::select(
            DB::raw("MONTH(fecha_pago) as mes"),
            DB::raw("SUM(monto) as total")
        )
            ->whereYear('fecha_pago', date('Y'))
            ->groupBy('mes')
            ->get();

        foreach ($pagos as $item) {
            $pagadoPorMes[$item->mes] = (float) $item->total;
        }

        /*
        |--------------------------------------------------------------------------
        | ORDEN FINAL (ENERO - DICIEMBRE)
        |--------------------------------------------------------------------------
        */

        $labels = [];
        $facturado = [];
        $pagado = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = $nombresMeses[$i];
            $facturado[] = $facturadoPorMes[$i];
            $pagado[] = $pagadoPorMes[$i];
        }

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        return view('dashboard', [

            'clientesCount' => Cliente::whereHas('contratos', function ($query) {
                $query->where('estado', 'activo');
            })->count(),

            'equiposCount' => Inventario::count(),
            'nodosCount' => Nodo::count(),

            'ticketsAbiertos' => Ticket::where('estado', 'Abierto')->count(),

            'ticketsRecientes' => Ticket::whereHas('cliente', function ($q) {
                $q->whereNull('deleted_at');
            })
                ->with('cliente')
                ->latest()
                ->take(5)
                ->get(),

            /*
        |--------------------------------------------------------------------------
        | GRAFICA CLIENTES
        |--------------------------------------------------------------------------
        */

            'mesesClientes' => $mesesClientes,
            'totalesClientes' => $totalesClientes,

            /*
        |--------------------------------------------------------------------------
        | GRAFICA CONTABLE
        |--------------------------------------------------------------------------
        */

            'mesesIngresos' => $labels,
            'totalesFacturado' => $facturado,
            'totalesPagado' => $pagado,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
