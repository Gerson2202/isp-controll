<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Plan;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\DB; // <-- Añade esta línea


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
            'plan_seleccionado' => 'required|exists:plans,id'
        ]);

        $this->isLoading = true;

        try {
            DB::beginTransaction();

            // Datos actuales
            $planAnterior = $this->cliente->contrato->plan;
            $nuevoPlan = Plan::find($this->plan_seleccionado);
            $ipCliente = $this->cliente->ip;

            if (empty($ipCliente)) {
                throw new \Exception("El cliente no tiene IP asignada");
            }

            // 1. Actualizar en base de datos primero
            $this->cliente->contrato->update(['plan_id' => $this->plan_seleccionado]);

            // 2. Actualizar en MikroTik
            $mikroTikService = new MikroTikService(
                $planAnterior->nodo->ip,
                $planAnterior->nodo->user,
                $planAnterior->nodo->pass,
                $planAnterior->nodo->puerto_api ?? 8728
            );

            // 3. Proceso completo de actualización
            $mikroTikService->actualizarPlanMikroTik(
                $this->cliente->id,
                $ipCliente,
                $planAnterior->nombre,
                $nuevoPlan->nombre,
                $nuevoPlan->velocidad_subida,
                $nuevoPlan->velocidad_bajada,
                $nuevoPlan->rehuso ?? '1:1'
            );

            DB::commit();
            $this->mensaje = 'Plan actualizado correctamente!';
            $this->tipoMensaje = 'success';

        } catch (\Exception $e) {
            DB::rollBack();
            $this->mensaje = 'Error: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
            \Log::error("Error actualizando plan", ['error' => $e->getMessage()]);
        }

        $this->isLoading = false;
    }
    public function render()
    {
        return view('livewire.editar-plan-cliente');
    }
}
