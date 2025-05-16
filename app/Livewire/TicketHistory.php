<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ticket;
use Livewire\WithPagination;

class TicketHistory extends Component
{
    use WithPagination;
    public $page = 1; // Para la opción con select
    public $search = '';
    public $perPage = 10;
    public $sortField = 'fecha_cierre';
    public $sortDirection = 'desc';
    public $selectedStatus = '';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $tickets = Ticket::with('cliente')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('tipo_reporte', 'like', '%'.$this->search.'%')
                      ->orWhere('situacion', 'like', '%'.$this->search.'%')
                      ->orWhere('solucion', 'like', '%'.$this->search.'%')
                      ->orWhereHas('cliente', function ($q) {
                          $q->where('nombre', 'like', '%'.$this->search.'%');
                      });
                });
            })
            ->when($this->selectedStatus, function ($query) {
                $query->where('estado', $this->selectedStatus);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.ticket-history', [
            'tickets' => $tickets,
            'statusOptions' => ['abierto' => 'Abierto', 'cerrado' => 'Cerrado', 'en_proceso' => 'En Proceso']
        ]);
    }
}
