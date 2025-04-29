<?php

namespace App\Livewire\Facturacion;

use App\Models\Factura;
use Livewire\Component;

class PanelFacturacion extends Component
{

    public $facturas;

    public function mount() {
        $this->facturas = Factura::with('contrato.cliente')->latest()->take(5)->get();
    }
    public function render()
    {
        return view('livewire.facturacion.panel-facturacion');
    }
}
