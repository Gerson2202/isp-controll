<?php

namespace App\Livewire\Facturacion;

use App\Models\Factura;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Pago; // Añade esto al inicio
use Livewire\Attributes\On; // Añade esto al inicio

class HistorialFacturas extends Component
{
    
    use WithPagination;

    public $cliente;
    public $search = '';
    public $sortField = 'fecha_emision';
    public $sortDirection = 'desc';
    public $facturaSeleccionada = null; // Inicializa como null
    public $pagos = [];

    public function mount($cliente)
    {
        $this->cliente = $cliente;
    }
    
    public function mostrarPagos($facturaId)
    {
        $this->facturaSeleccionada = Factura::with('pagos')->find($facturaId);
        $this->pagos = $this->facturaSeleccionada ? $this->facturaSeleccionada->pagos : [];
        $this->dispatch('abrirModalPagos'); // Cambiado a dispatch
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $facturas = Factura::query()
            ->whereHas('contrato', fn($q) => $q->where('cliente_id', $this->cliente->id))
            ->when($this->search, fn($q) => $q->where('numero_factura', 'like', '%'.$this->search.'%'))
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.facturacion.historial-facturas', [
            'facturas' => $facturas
        ]);
    }
}

