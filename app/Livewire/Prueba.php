<?php

namespace App\Livewire;

use App\Models\Plan;
use Livewire\Component;

class Prueba extends Component
{
    public $plans;
    public $selectedPlanId;

    public function mount()
    {
        // Obtener todos los planes de la base de datos
        $this->plans = Plan::all();
    }

    public function changePlan()
    {
        // Este método se ejecutará cuando el usuario cambie la selección
        dd($this->selectedPlanId); // Muestra el ID del plan seleccionado
    }

    public function render()
    {
        return view('livewire.prueba', [
            'plans' => $this->plans,
        ]);
    }
}
