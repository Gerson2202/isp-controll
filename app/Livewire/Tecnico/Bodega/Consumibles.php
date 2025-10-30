<?php

namespace App\Livewire\Tecnico\Bodega;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConsumibleStock;

class Consumibles extends Component
{
    use WithPagination;

    public $buscar = '';

    public function render()
    {
        $usuario = auth()->user();

        $consumibles = ConsumibleStock::with('consumible')
            ->where('usuario_id', $usuario->id)
            ->whereHas('consumible', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%");
            })
            ->paginate(10);

        return view('livewire.tecnico.bodega.consumibles', compact('consumibles'));
    }
}
