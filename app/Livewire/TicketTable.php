<?php

namespace App\Livewire;
use App\Models\Ticket;
use Livewire\Component;

class TicketTable extends Component
{
    public function render()
    {
         // Filtra los tickets con estado "abierto"
         $tickets = Ticket::where('estado', 'abierto')->get();
         return view('livewire.ticket-table', compact('tickets'));
    }
}
