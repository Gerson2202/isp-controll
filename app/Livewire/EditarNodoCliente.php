<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Nodo;
use App\Models\Plan;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\DB; // <-- Añade esta línea
use Illuminate\Support\Facades\Log;

class EditarNodoCliente extends Component
{
    
    public Cliente $cliente;
    public $plan_id;
    public $precio;
    public $planes = [];
    public $nodos = [];
    public $selectedNodeId;
    public Contrato $contrato;
    public $plan_idAnterior;


    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->nodos = Nodo::all();
        
        // Obtener el contrato existente (asumimos que siempre existe)
        $this->contrato = Contrato::where('cliente_id', $this->cliente->id)->firstOrFail();
        
        // Cargar datos del contrato
        $this->plan_id = $this->contrato->plan_id;
        $this->precio = $this->contrato->precio;
        $this->selectedNodeId = $this->contrato->plan->nodo_id;
        $this->planes = Plan::where('nodo_id', $this->selectedNodeId)->get();
    }

    public function changeNode()
    {
        $this->validate(['selectedNodeId' => 'required|exists:nodos,id']);
        $this->planes = Plan::where('nodo_id', $this->selectedNodeId)->get();
        $this->plan_id = null;
    }

    public function actualizarContrato()
    {
        $this->validate([
            'plan_id' => 'required|exists:plans,id',
            'precio' => 'required|numeric|min:0',
        ]);

        // Iniciamos la transacción
        DB::beginTransaction();

        try {
            // Guardar datos importantes antes de cualquier cambio
            $ipCliente = $this->cliente->ip;
            $planAnterior = $this->contrato->plan;
            $nombrePlan = $planAnterior->nombre;
            $clienteId = $this->cliente->id;

            // ------------- Variables para el mensaje
            // Datos del NUEVO plan seleccionado
            $nuevoPlan = Plan::findOrFail($this->plan_id);
            $nodoAnterior=$this->contrato->plan->nodo->nombre;
            $precioAnterior=$this->contrato->precio;
            $nuevoPlanNombre = $nuevoPlan->nombre;
            $nuevoNodoNombre = $nuevoPlan->nodo->nombre; // Nombre del nuevo nodo

            if (empty($ipCliente)) {
                throw new \Exception("El cliente no tiene una IP asignada");
            }

            // 1. Primero actualizamos MikroTik
            $mikroTikService = new MikroTikService(
                $planAnterior->nodo->ip,
                $planAnterior->nodo->user,
                $planAnterior->nodo->pass,
                $planAnterior->nodo->puerto_api ?? 8728
            );

            // Esto lanzará una excepción si falla
            $mikroTikService->eliminarCola($ipCliente, $nombrePlan, $clienteId);

            // 2. Si MikroTik se actualizó correctamente, actualizamos la base de datos
            $this->contrato->update([
                'plan_id' => $this->plan_id,
                'precio' => $this->precio,
            ]);

            $this->cliente->update(['ip' => null]);

             // Creamos ticket de reporte de modificacion de plan 
             $situacionTexto = "Se realizó cambio de Nodo de {$nodoAnterior} con precio de : {$precioAnterior} y plan {$nombrePlan} al Nodo: {$nuevoNodoNombre} con precio $ {$this->precio} con nuevo plan {$nuevoPlanNombre}. Actualizado por el usuario: " . auth()->user()->name;
             Ticket::create([
                 'tipo_reporte' => 'cambio de nodo',
                 'situacion' => $situacionTexto,
                 'estado' => 'cerrado',
                 'fecha_cierre' => now(), 
                 'cliente_id' => $this->cliente->id,
                 'solucion' => 'Nodo actualizado correctamente desde el panel',
             ]);

            // Confirmamos la transacción
            DB::commit();

            session()->flash('success', 'Contrato actualizado y colas en MikroTik eliminadas correctamente.');
            return redirect()->route('asignarIPindex');

        } catch (\Exception $e) {
            // Revertimos la transacción
            DB::rollBack();
            
            session()->flash('error', 'Error al actualizar: '.$e->getMessage());
            return back()->withErrors(['mikrotik' => $e->getMessage()]);
        }
    }
   
    
    public function render()
    {
        return view('livewire.editar-nodo-cliente');
    }
}
