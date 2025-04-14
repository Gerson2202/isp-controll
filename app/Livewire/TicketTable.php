<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class TicketTable extends Component
{
    use WithPagination;
    public $search = '';
    public $perPage = 10;

  

    public function render()
    {
        $tickets = Ticket::where('estado', 'abierto')
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('tipo_reporte', 'like', '%'.$this->search.'%')
                      ->orWhere('situacion', 'like', '%'.$this->search.'%')
                      ->orWhereHas('cliente', function($q) {
                          $q->where('nombre', 'like', '%'.$this->search.'%');
                      });
                });
            })
            ->paginate($this->perPage);
            
        return view('livewire.ticket-table', compact('tickets'));
    }
}