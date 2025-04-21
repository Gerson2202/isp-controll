<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Plan;
use Livewire\Component;

class EditarPlanCliente extends Component
{
    public Cliente $cliente;
    public $id_nodo;
    public $planes;
    public $plan_seleccionado;
    public $isLoading = false;
    public $mensaje = '';
    public $tipoMensaje = ''; // 'success' o 'error'

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->loadClienteData();
    }

    protected function loadClienteData()
    {
        $this->isLoading = true;
        $this->mensaje = '';
        
        $this->cliente = Cliente::with(['contrato.plan.nodo'])->find($this->cliente->id);
        
        if (!$this->cliente->contrato) {
            $this->mensaje = 'El cliente no tiene un contrato asociado';
            $this->tipoMensaje = 'error';
            return;
        }

        $this->id_nodo = $this->cliente->contrato->plan->nodo->id;
        $this->planes = Plan::where('nodo_id', $this->id_nodo)
                          ->orderBy('nombre')
                          ->get();
        $this->plan_seleccionado = $this->cliente->contrato->plan_id;
        
        $this->isLoading = false;
    }

    public function actualizarPlan()
    {
        $this->validate([
            'plan_seleccionado' => [
                'required',
                'exists:plans,id',
                function ($attribute, $value, $fail) {
                    if (!in_array($value, $this->planes->pluck('id')->toArray())) {
                        $fail('El plan seleccionado no pertenece al nodo actual.');
                    }
                },
            ],
        ]);

        $this->isLoading = true;
        $this->mensaje = '';

        try {
            $this->cliente->contrato->update(['plan_id' => $this->plan_seleccionado]);
            $this->loadClienteData();
            $this->mensaje = 'Plan actualizado correctamente!';
            $this->tipoMensaje = 'success';

        } catch (\Exception $e) {
            $this->mensaje = 'Error al actualizar: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.editar-plan-cliente');
    }
}
