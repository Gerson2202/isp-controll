<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Factura;
use Carbon\Carbon;

class DetalleFactura extends Component
{
    public $cliente;
    public $factura;
    public $pagos = [];
    public $items = [];
    
    public function mount($cliente)
    {
        $this->cliente = $cliente;
        $this->loadFacturaDelMes();
    }
    
    public function loadFacturaDelMes()
    {
        // Obtener el contrato del cliente
        $contrato = Contrato::where('cliente_id', $this->cliente->id)->first();
        
        if ($contrato) {
            // Obtener la factura del mes actual
            $this->factura = Factura::where('contrato_id', $contrato->id)
                ->whereMonth('fecha_emision', Carbon::now()->month)
                ->whereYear('fecha_emision', Carbon::now()->year)
                ->with(['pagos', 'items'])
                ->first();
                
            if ($this->factura) {
                $this->pagos = $this->factura->pagos;
                $this->items = $this->factura->items;
            }
        }
    }
    public function render()
    {
        return view('livewire.facturacion.detalle-factura');
    }
}
