<?php

namespace App\Livewire;

use Livewire\Component;

class ModalComponent extends Component
{
    public $showModal = false;

    // Función para mostrar el modal
    public function show()
    {
        $this->showModal = true;
    }

    // Función para ocultar el modal
    public function hide()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.modal-component');
    }
}
