<?php

namespace App\Livewire\Visitas;

use Livewire\Component;
use App\Models\Visita;

class Show extends Component
{
    public $visita;

    public function mount(Visita $visita)
    {
        // Cargamos las relaciones necesarias
        $this->visita = $visita->load([
            'ticket.cliente',
            'users' => function ($query) {
                $query->withPivot(['fecha_inicio', 'fecha_cierre']);
            },
            'fotos'
        ]);
    }

    public function render()
    {
        return view('livewire.visitas.show');
    }
}
