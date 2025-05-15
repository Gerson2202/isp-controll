<?php

namespace App\Livewire\Nodo;

use Livewire\Component;
use App\Models\Nodo;

class NodosDetalles extends Component
{
   public $nodo;

    public function mount(Nodo $nodo)
    {
        $this->nodo = $nodo->load('inventarios.modelo'); // Carga relaciones
    }

    public function render()
    {
        return view('livewire.nodo.nodos-detalles');
    }
}
