<?php

namespace App\Livewire;

use App\Models\Nodo;
use Livewire\Component;
use Livewire\WithPagination;

class NodoMonitoreo extends Component
{
    use WithPagination;

    public $search = ''; // Búsqueda
    public $sortBy = 'nombre'; // Ordenar por
    public $sortDirection = 'asc'; // Dirección de ordenamiento
    protected $queryString = ['search', 'sortBy', 'sortDirection', 'page']; // Persistir en URL
    protected $paginationTheme = 'bootstrap';

    // Resetear página cuando busca
    public function updatedSearch()
    {
        $this->resetPage();
    }

    // Ordenar
    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $nodos = Nodo::query()
            ->where('nombre', 'like', '%' . $this->search . '%')
            ->orWhere('ip', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.nodo-monitoreo', [
            'nodos' => $nodos,
        ]);
    }
}