<?php

namespace App\Livewire\Contratos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\Plan;

class ContratosList extends Component
{
   
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'fecha_inicio';
    public $sortDirection = 'desc';

    // Variables para edición
    public $showModal = false;
    public $contratoId;
    public $cliente_id;
    public $plan_id;
    public $tecnologia;
    public $fecha_inicio;
    public $fecha_fin;
    public $estado;

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'plan_id' => 'required|exists:plans,id',
        'tecnologia' => 'required|string|max:50',
        'fecha_inicio' => 'required|date_format:Y-m-d',
        'fecha_fin' => 'nullable|date_format:Y-m-d|after:fecha_inicio',
        'estado' => 'required|in:activo,inactivo,suspendido'
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

    public function openEditModal($contratoId)
    {
        $contrato = Contrato::findOrFail($contratoId);
        
        $this->contratoId = $contrato->id;
        $this->cliente_id = $contrato->cliente_id;
        $this->plan_id = $contrato->plan_id;
        $this->tecnologia = $contrato->tecnologia;
        $this->estado = $contrato->estado;
        $this->fecha_inicio = $contrato->fecha_inicio; // Formato Y-m-d
        $this->fecha_fin = $contrato->fecha_fin;       // Formato Y-m-d o null
        
        $this->showModal = true;
    }

    public function updateContrato()
    {
        $this->validate();

        Contrato::find($this->contratoId)->update([
            'cliente_id' => $this->cliente_id,
            'plan_id' => $this->plan_id,
            'tecnologia' => $this->tecnologia,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado
        ]);

        $this->showModal = false;
        $this->dispatch('notify', type: 'success', message: 'Contrato actualizado!');
    }

    public function render()
    {
        $contratos = Contrato::with(['cliente', 'plan'])
            ->when($this->search, function ($query) {
                $query->whereHas('cliente', fn($q) => $q->where('nombre', 'like', "%{$this->search}%"))
                      ->orWhere('tecnologia', 'like', "%{$this->search}%")
                      ->orWhere('estado', 'like', "%{$this->search}%");
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.contratos.contratos-list', [
            'contratos' => $contratos,
            'clientes' => Cliente::orderBy('nombre')->get(),
            'planes' => Plan::orderBy('nombre')->get()
        ]);
    }
}
