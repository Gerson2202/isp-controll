<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Plan;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\DB; // <-- A帽ade esta l铆nea
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class EditarPlanCliente extends Component
{
    public Cliente $cliente;
    public $id_nodo;
    public $planes;
    public $plan_seleccionado;
    public $isLoading = false;
    public $mensaje = '';
    public $tipoMensaje = ''; // 'success' o 'error'
    public $precio; // Nueva propiedad para el nuevo precio
    public $precio_anterior;

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->loadClienteData();
        $this->precio = $cliente->contrato->precio ?? 'Sin contrato';
        $this->precio_anterior = $this->cliente->contrato->precio ?? 'Sin contrato'; // <- guardalo antes del update
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

        $this->id_nodo = optional($this->cliente->contrato?->plan?->nodo)->id;
        $this->planes = Plan::where('nodo_id', $this->id_nodo)
                          ->orderBy('nombre')
                          ->get();
        $this->plan_seleccionado = $this->cliente->contrato->plan_id;
        
        $this->isLoading = false;
    }

   public function actualizarPlan()
    {
        // Permiso para modificar plan del cliente 
         if (!auth()->user()->can('modificar plan de cliente')) {
        abort(403, 'No tienes permiso para modificar plan de  clientes');
         }
        $this->validate([
            'plan_seleccionado' => 'required|exists:plans,id',
            'precio' => 'required|numeric|min:0',
        ]);
        
        // Limpiar el precio
        $this->precio = str_replace(['.', ','], '', $this->precio);
        $this->isLoading = true;

        try {
            DB::beginTransaction();

            // Datos actuales
            $planAnterior = $this->cliente->contrato->plan;
            $nuevoPlan = Plan::find($this->plan_seleccionado);
            
            // 1. Actualizar en base de datos
            $this->cliente->contrato->update([
                'plan_id' => $this->plan_seleccionado,
                'precio' => $this->precio,
            ]);
            
            // Crear ticket de modificación
            $situacionTexto = "Se realizo cambio de plan de {$planAnterior->nombre} con precio de: $ {$this->precio_anterior} al plan: {$nuevoPlan->nombre} con precio $ {$this->precio}. Actualizado por el usuario: " . auth()->user()->name;
            Ticket::create([
                'user_id' => auth()->id(),
                'tipo_reporte' => 'cambio de plan',
                'situacion' => $situacionTexto,
                'estado' => 'cerrado',
                'fecha_cierre' => now(), 
                'cliente_id' => $this->cliente->id,
                'solucion' => 'Plan actualizado correctamente desde el panel',
            ]);

            // Verificar si hubo cambio de plan (no solo de precio)
            if ($planAnterior->id != $this->plan_seleccionado) {
                $ipCliente = $this->cliente->ip;
                if (empty($ipCliente)) {
                    throw new \Exception("El cliente no tiene IP asignada");
                }

                // Solo ejecutar MikroTik si hubo cambio de plan
                $mikroTikService = new MikroTikService(
                    $planAnterior->nodo->ip,
                    $planAnterior->nodo->user,
                    $planAnterior->nodo->pass,
                    $planAnterior->nodo->puerto_api ?? 8728
                );

                $mikroTikService->actualizarPlanMikroTik(
                    $this->cliente->id,
                    $ipCliente,
                    $planAnterior->nombre,
                    $nuevoPlan->nombre,
                    $nuevoPlan->velocidad_subida,
                    $nuevoPlan->velocidad_bajada,
                    $nuevoPlan->rehuso ?? '1:1'
                );
            }

            DB::commit();
            
            $this->dispatch('notify', 
                type: 'success',
                message: 'Actualizacion exitosa!'
            );

            $this->cliente->contrato->refresh();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->mensaje = 'Error: ' . $e->getMessage();
            $this->tipoMensaje = 'error';
            Log::error("Error actualizando plan", ['error' => $e->getMessage()]);
        }

        $this->isLoading = false;
    }
    public function render()
    {
        return view('livewire.editar-plan-cliente');
    }
}
