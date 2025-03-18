<?php

namespace App\Livewire;

use App\Services\MikroTikService;
use Livewire\Component;

class ShowInterfaces extends Component
{
    public $interfaces;
    
    public function mount(MikroTikService $mikroTikService)
    {
        $this->interfaces = $mikroTikService->getInterfaces();
    }
    
    public function render()
    {
        return view('livewire.show-interfaces');
    }
}
