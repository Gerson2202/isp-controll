<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Contrato;
use App\Models\Factura;
use App\Models\ItemFactura;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class GenerarFacturasMensuales extends Component
{
    use WithPagination; // 👈 Añade esto
    public $mes;
    public $anio;
    public $resultados = [];
    public $search = '';
    public $perPage = 10;

    public function mount()
    {
        $this->mes = now()->month;
        $this->anio = now()->year;
    }

    public function generarFacturas()
    {
        $this->validate([
            'mes' => 'required|numeric|between:1,12',
            'anio' => 'required|numeric|digits:4',
        ]);

        $contratos = Contrato::with(['cliente', 'plan'])
            ->where('estado', 'activo')
            ->get();

        foreach ($contratos as $contrato) {
            try {
                // Verificar si ya existe factura para este período
                $existeFactura = Factura::where('contrato_id', $contrato->id)
                    ->whereMonth('fecha_emision', $this->mes)
                    ->whereYear('fecha_emision', $this->anio)
                    ->exists();

                if ($existeFactura) {
                    $this->resultados[] = [
                        'cliente' => $contrato->cliente->nombre,
                        'estado' => 'omitido',
                        'mensaje' => 'Ya tiene factura para este período'
                    ];
                    continue;
                }

                $fechaEmision = now()->setDate($this->anio, $this->mes, 3);
                // $fechaVencimiento = $fechaEmision->copy()->addDays(30);
                $fechaVencimiento = $fechaEmision->copy()->addMonth()->day(3);

                $factura = Factura::create([
                    'contrato_id' => $contrato->id,
                    'numero_factura' => $this->generarNumeroFactura(),
                    'fecha_emision' => $fechaEmision,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'monto_total' => $contrato->precio,
                    'saldo_pendiente' => $contrato->precio,
                    'estado' => 'pendiente',
                ]);

                // Item principal
                $factura->items()->create([
                    'descripcion' => 'Servicio de internet - ' . $contrato->plan->nombre,
                    'monto' => $contrato->precio,
                ]);

                $this->resultados[] = [
                    'cliente' => $contrato->cliente->nombre,
                    'estado' => 'éxito',
                    'mensaje' => 'Factura generada: ' . $factura->numero_factura
                ];

            } catch (\Exception $e) {
                $this->resultados[] = [
                    'cliente' => $contrato->cliente->nombre,
                    'estado' => 'error',
                    'mensaje' => 'Error: ' . $e->getMessage()
                ];
            }
        }
    }

    protected function generarNumeroFactura()
    {
        return 'FAC-' . strtoupper(Str::random(6)) . '-' . now()->format('YmdHis');
    }

    public function eliminarUltimoLote()
    {
        // Verificar si hay facturas en el período actual
        $facturas = Factura::whereMonth('fecha_emision', $this->mes)
            ->whereYear('fecha_emision', $this->anio)
            ->get();

        if ($facturas->isEmpty()) {
            $this->dispatch('notify', 
                type: 'warning',
                message: 'No hay facturas para el período '.$this->mes.'/'.$this->anio
            );
            return;
        }

        // Verificar pagos
        $facturasConPagos = $facturas->filter(function($factura) {
            return $factura->pagos()->exists();
        });

        if ($facturasConPagos->isNotEmpty()) {
            $this->dispatch('notify', 
                type: 'error',
                message: $facturasConPagos->count().' facturas no pueden eliminarse porque tienen pagos asociados'
            );
            return;
        }

        // Eliminar en transacción
        try {
            DB::transaction(function () use ($facturas) {
                ItemFactura::whereIn('factura_id', $facturas->pluck('id'))->delete();
                Factura::whereIn('id', $facturas->pluck('id'))->delete();
            });

            $this->dispatch('notify', 
                type: 'success',
                message: 'Se eliminaron '.$facturas->count().' facturas'
            );
            
            $this->resultados = [];
            
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error al eliminar facturas: '.$e->getMessage()
            );
        }
    }
    
    public function render()
    {
        return view('livewire.facturacion.generar-facturas-mensuales', [
            'resultados' => $this->resultados // Pasa explícitamente la variable
        ]);
    }
}
