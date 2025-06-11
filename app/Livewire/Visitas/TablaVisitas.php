<?php

namespace App\Livewire\Visitas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Visita;

class TablaVisitas extends Component
{
     use WithPagination;

    public $sortField = 'fecha_inicio';
    public $sortDirection = 'desc';
    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'fecha_inicio'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        
        $this->sortField = $field;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $visitas = Visita::with(['ticket', 'ticket.cliente'])
        ->when($this->search, function ($query) {
            $query->where(function($q) {
                $q->whereHas('ticket.cliente', function ($subQuery) {
                    $subQuery->where('nombre', 'like', '%'.$this->search.'%');
                })
                ->orWhereHas('ticket', function ($subQuery) {
                    $subQuery->where('id', 'like', '%'.$this->search.'%');
                })
                ->orWhere('estado', 'like', '%'.$this->search.'%'); // Nueva línea para búsqueda por estado
            });
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);

        return view('livewire.visitas.tabla-visitas', [
            'visitas' => $visitas
        ]);
    }
}
