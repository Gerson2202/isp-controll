<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;
use Livewire\WithPagination;

class TicketTable extends Component
{
    use WithPagination;
    
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

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
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
            
        return view('livewire.ticket-table', compact('tickets'));
    }
}