<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Models\Plan;
use Livewire\Component;

class PlanesFormulario extends Component
{
    
    public $showModal = false;
    public $plans;
    public $nodos;
    public $nombre, $descripcion, $velocidad_bajada, $velocidad_subida, $rehuso, $plan_id, $nodo_id;
    public $successMessage = ''; // Propiedad para el mensaje de éxito

    public function mount()
    {
        // Cargar todos los planes con su relación de nodo, ordenados por el nombre del nodo
        $this->plans = Plan::with('nodo')->get();
        $this->nodos = Nodo::all();
       
    }
    // Funcion oculatar modal
    public function hide()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // Mostrar el modal para actualizar
    public function editPlan($id)
    {
         $plan = Plan::find($id);
         $this->plan_id = $plan->id;
         $this->nombre = $plan->nombre;
         $this->descripcion = $plan->descripcion;
         $this->velocidad_bajada = $plan->velocidad_bajada;
         $this->velocidad_subida = $plan->velocidad_subida;
         $this->rehuso = $plan->rehuso;
         $this->nodo_id = $plan->nodo_id;
         $this->showModal = true;  
         $this->clearSuccessMessage();  // Limpiar cualquier mensaje anterior
    }

    // Actualizar el plan
    public function updatePlan()
    {
        $plan = Plan::find($this->plan_id);
        // Actualizar plan
        $plan->update([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'velocidad_bajada' => $this->velocidad_bajada,
            'velocidad_subida' => $this->velocidad_subida,
            'rehuso' => $this->rehuso,
            'nodo_id' => $this->nodo_id,
            
        ]);

         // Actualizar la lista de planes
        $this->plans = Plan::all();

        // Mostrar el mensaje de éxito
        $this->successMessage = 'Plan actualizado exitosamente!';

        // Cerrar el modal
        $this->showModal = false;
        $this->resetForm();
        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');
     }


     // Limpiar el mensaje de éxito
    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }

    // Borrar el plan
    public function deletePlan($id)
    {
       
       Plan::find($id)->delete();
       $this->plans = Plan::all(); // Volver a cargar los planes
        $this->successMessage = 'Plan Eliminado con exito!';

    }

    // Validación de los campos
    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'velocidad_bajada' => 'required|integer|min:0',
        'velocidad_subida' => 'required|integer|min:0',
        'rehuso' => 'required|in:1:1,1:2,1:4,1:6',
        'nodo_id' => 'exists:nodos,id', // Validar que el nodo_id existe en la tabla nodos
    ];

    // Función para Crear un nuevo plan
    public function submitPlan()
    {
      
        // Validar los datos del formulario
        $this->validate();
        
        // Crear un nuevo plan
        Plan::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'velocidad_bajada' => $this->velocidad_bajada,
            'velocidad_subida' => $this->velocidad_subida,
            'rehuso' => $this->rehuso,
            'nodo_id' => $this->nodo_id,
        ]);

        // Actualizar la lista de planes
        $this->plans = Plan::all();

        // Mostrar el mensaje de éxito
        $this->successMessage = 'Plan Creado exitosamente!';

        // Vaciar los campos del formulario después de guardar
        $this->resetForm();
        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');
    }
    public function resetForm()
    {
        $this->nombre = '';
        $this->descripcion = '';
        // $this->precio = '';
        $this->velocidad_bajada = '';
        $this->velocidad_subida = '';
        $this->rehuso = '';
        $this->nodo_id = '';
    }
   
    public function render()
    {
        return view('livewire.planes-formulario');
    }
}
