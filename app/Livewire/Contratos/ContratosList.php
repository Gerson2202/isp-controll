<?php

namespace App\Livewire\Contratos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\Nodo;
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
        'estado' => 'required|in:activo,cancelado,suspendido',
        'precio' => 'required|regex:/^[\d.,]+$/',
    ];

    
    public function updateContrato()
    {
         if (!auth()->user()->can('editar contrato')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        // Validar los datos usando las reglas definidas
        $this->validate();

        // Buscar el cliente por ID
        $cliente = Cliente::find($this->cliente_id);

        // Verificar si el cliente tiene IP asignada
        if (!$cliente || empty($cliente->ip)) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'El cliente no tiene una IP asignada, no se puede actualizar el contrato.'
            );
            return; // Detener la ejecución
        }
        // Formatear el precio: eliminar puntos y comas
        $precioFormateado = str_replace(['.', ','], '', $this->precio);
        
        $contrato = Contrato::findOrFail($this->contratoId);

        $contrato->update([
            'cliente_id' => $this->cliente_id,
            'plan_id' => $this->plan_id,
            'tecnologia' => $this->tecnologia,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
            'precio' => $precioFormateado, // Usamos el precio formateado
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
    
     protected function getEstadisticasNodos()
    {
        return Nodo::select('nodos.id', 'nodos.nombre as nodo_nombre')
            ->selectRaw('COUNT(DISTINCT clientes.id) as total_clientes')
            ->selectRaw('SUM(CASE WHEN contratos.estado = "activo" THEN 1 ELSE 0 END) as total_activos')
            ->leftJoin('plans', 'plans.nodo_id', '=', 'nodos.id')
            ->leftJoin('contratos', 'contratos.plan_id', '=', 'plans.id')
            ->leftJoin('clientes', 'clientes.id', '=', 'contratos.cliente_id')
            ->groupBy('nodos.id', 'nodos.nombre')
            ->orderBy('nodos.nombre')
            ->get();
    }
    public function render()
    {
        $contratos = Contrato::select('contratos.*')
            ->with(['cliente', 'plan.nodo'])
            ->join('clientes', 'clientes.id', '=', 'contratos.cliente_id')
            ->join('plans', 'plans.id', '=', 'contratos.plan_id')
            ->join('nodos', 'nodos.id', '=', 'plans.nodo_id')
            ->when($this->search, function ($query) {
                $query->where(function($q) {
                    $q->where('clientes.nombre', 'like', "%{$this->search}%")
                    ->orWhere('clientes.ip', 'like', "%{$this->search}%")
                    ->orWhere('contratos.tecnologia', 'like', "%{$this->search}%")
                    ->orWhere('contratos.estado', 'like', "%{$this->search}%")
                    ->orWhere('nodos.nombre', 'like', "%{$this->search}%")
                    ->orWhere('clientes.estado', 'like', "%{$this->search}%");
                });
            })
            ->orderBy(
                $this->sortField === 'ip' ? 'clientes.ip' : 
                ($this->sortField === 'cliente_id' ? 'clientes.nombre' : 
                ($this->sortField === 'nodo' ? 'nodos.nombre' : 
                ($this->sortField === 'estado_cliente' ? 'clientes.estado' : 'contratos.'.$this->sortField))),
                $this->sortDirection
            )
            ->paginate($this->perPage);

        return view('livewire.contratos.contratos-list', [
            'contratos' => $contratos,
            'clientes' => Cliente::orderBy('nombre')->get(),
            'planes' => Plan::orderBy('nombre')->get(),
            'estadisticasNodos' => $this->getEstadisticasNodos()
        ]);
    }

}
