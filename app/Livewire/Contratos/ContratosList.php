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
    protected $queryString = ['search', 'perPage', 'sortField', 'sortDirection'];
    // Variables para edición
    public $showModal = false;
    public $contratoId;
    public $cliente_id;
    public $plan_id;
    public $tecnologia;
    public $fecha_inicio;
    public $fecha_fin;
    public $estado;
    public $precio;

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'plan_id' => 'required|exists:plans,id',
        'tecnologia' => 'required|string|max:50',
        'fecha_inicio' => 'required|date_format:Y-m-d',
        'fecha_fin' => 'nullable|date_format:Y-m-d|after:fecha_inicio',
        'estado' => 'required|in:activo,cancelado,suspendido'
    ];

    
    public function updateContrato()
    {
       // Validar los datos usando las reglas definidas
        $this->validate();
        $contrato = Contrato::findOrFail($this->contratoId);

        $contrato->update([
            'cliente_id' => $this->cliente_id,
            'plan_id' => $this->plan_id,
            'tecnologia' => $this->tecnologia,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
            'precio' => $this->precio,
        ]);

        $this->dispatch('cerrar-modal');
            
        // Notificación Toastr
        $this->dispatch('notify', 
            type: 'success',
            message: 'Contrato actualizado exitosamente!'
            );
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
    
    // Funcion oculatar modal
    public function hide()
    {
        $this->showModal = false;
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function openEditModal($contratoId)
    {
        $this->dispatch('abrir-modal');
        // $this->showModal = true;
        $contrato = Contrato::findOrFail($contratoId);
        $this->contratoId = $contrato->id;
        $this->cliente_id = $contrato->cliente_id;
        $this->plan_id = $contrato->plan_id;
        $this->tecnologia = $contrato->tecnologia;
        $this->estado = $contrato->estado;
        $this->fecha_inicio = $contrato->fecha_inicio; // Formato Y-m-d
        $this->fecha_fin = $contrato->fecha_fin;       // Formato Y-m-d o null
        $this->precio = $contrato->precio;
        
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
