<?php

namespace App\Livewire\Visitas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Visita;

class TablaVisitas extends Component
{
      use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'id';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'id'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $visitas = Visita::with(['usuarios' => function ($q) {
                $q->orderBy('visita_user.fecha_inicio', 'asc');
            }, 'ticket.cliente'])
            ->withMin('usuarios as fecha_inicio', 'visita_user.fecha_inicio')
            ->withMax('usuarios as fecha_cierre', 'visita_user.fecha_cierre')
            ->when($this->search, function ($query) {
                $query->whereHas('ticket', function ($subQuery) {
                    $subQuery->where('id', 'like', "%{$this->search}%")
                             ->orWhere('descripcion', 'like', "%{$this->search}%")
                             ->orWhereHas('cliente', function ($clienteQuery) {
                                 $clienteQuery->where('nombre', 'like', "%{$this->search}%");
                             });
                });
            })
            ->orderBy($this->sortField === 'fecha_inicio' ? 'fecha_inicio' : $this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.visitas.tabla-visitas', [
            'visitas' => $visitas,
        ]);
    }
    
}
