<?php

namespace App\Livewire;

use Livewire\Component;

class Prueba extends Component
{
    public $datos = [
        ['x' => 'Ene', 'y' => 65],
        ['x' => 'Feb', 'y' => 59],
        ['x' => 'Mar', 'y' => 80],
        ['x' => 'Abr', 'y' => 81],
        ['x' => 'May', 'y' => 56],
        ['x' => 'Jun', 'y' => 55],
        ['x' => 'Jul', 'y' => 40]
    ];

    public function render()
    {
        return view('livewire.prueba');
    }
}