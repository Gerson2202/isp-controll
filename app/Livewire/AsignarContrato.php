<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Nodo;
use App\Models\Plan;
use Livewire\Component;

class AsignarContrato extends Component
{
    
    public $cliente_id; //ID del cliente, enviado desde la vista
    public $cliente;    //Objeto cliente para mostrar datos ala vista
    public $plan_id;      // Plan seleccionado
    public $fecha_inicio; // Fecha de inicio
    public $fecha_fin;    // Fecha de fin
    public $precio;       // Precio
    public $planes = [];  // Planes disponibles
    public $nodos ;   // Nodos disponibles
    public $selectedNodeId; // ID del nodo seleccionado
    public $selectedPlanId; // ID del plan seleccionado
    public $tecnologia;

    public function mount($cliente)
    {
        $this->cliente_id = $cliente; // Aquí asignas la variable recibida a la propiedad
        $this->cliente = Cliente::find($this->cliente);
        $this->planes = Plan::all();
        $this->nodos = Nodo::all();   // Cargamos todos los nodos disponibles
    }

   
     public function changeNode()
     {
         // Obtener los planes asociados al nodo seleccionado
         $this->planes = Plan::where('nodo_id', $this->selectedNodeId)->get();
 
         // Limpiar la selección de plan al cambiar de nodo
         $this->selectedPlanId = null;
     }


    public function guardarContrato()
    {
        try {
            // Validación de los datos
            $validatedData = $this->validate([
                'plan_id' => 'required|exists:plans,id',
                'fecha_inicio' => 'required|date|before:fecha_fin',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'precio' => 'required|regex:/^[\d.,]+$/',
            ], 
            [
                'fecha_inicio.before' => 'La fecha de inicio debe ser anterior a la fecha de fin',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'precio.regex' => 'El precio debe tener formato X.000 (ej: 10.000, 300.000)',
                ]);
            // Guardar el contrato
                 $precioLimpio = str_replace(['.', ','], '', $this->precio);            $contrato = Contrato::create([
                'cliente_id' => $this->cliente_id, // Ya tenemos el cliente_id en la propiedad
                'plan_id' => $this->plan_id,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'precio' => $precioLimpio,
                'estado' => 'suspendido',
                'tecnologia' => $this->tecnologia,
            ]);
    
            session()->flash('message', 'Contrato asignado correctamente.');
            return redirect()->route('contratoIndex');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessage = $e->validator->errors()->first();
            $this->dispatch('notify', 
                type: 'error',
                message: $errorMessage
            );
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error al guardar el contrato: ' . $e->getMessage()
            );
            report($e);
        }
    }
    

    public function render()
    {
        return view('livewire.asignar-contrato');
    }
}