<?php

namespace App\Livewire\Bodega;

use App\Models\Bodega;
use App\Models\ConsumibleStock;
use Livewire\Component;

class Show extends Component
{
     public $bodega;
    public $stocks;
    public $search = '';

    public function mount(Bodega $bodega)
    {
        $this->bodega = $bodega;
        $this->loadStocks();
    }

    public function updatedSearch()
    {
        $this->loadStocks();
    }

    public function loadStocks()
    {
        $query = ConsumibleStock::with('consumible')
            ->where('bodega_id', $this->bodega->id)
            ->orderBy('consumible_id')
            ->orderBy('created_at');

        if ($this->search) {
            $query->whereHas('consumible', function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%');
            });
        }

        $this->stocks = $query->get();
    }

    public function render()
    {
        return view('livewire.bodega.show');
    }
}
