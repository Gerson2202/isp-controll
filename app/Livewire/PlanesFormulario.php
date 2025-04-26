<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Models\Plan;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\Log; // Importar la clase Log

class PlanesFormulario extends Component
{
    public $loadingActivation = false;
    public $currentPlanActivating = null;
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
        try {
            // Buscar el plan
            $plan = Plan::findOrFail($this->plan_id);
            
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

            // Notificación Toastr
            $this->dispatch('notify', 
                type: 'success',
                message: 'Plan actualizado exitosamente!'
            );

            // Cerrar el modal y resetear formulario
            $this->showModal = false;
            $this->resetForm();

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error: El plan no fue encontrado'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Error de validación: ' . implode(' ', $e->validator->errors()->all())
            );

        } catch (\Exception $e) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Error al actualizar el plan: ' . $e->getMessage()
            );
        }
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
        try {
            // Validar los datos del formulario (sin cambios)
            $this->validate();
            
            // Crear un nuevo plan (sin cambios)
            Plan::create([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'velocidad_bajada' => $this->velocidad_bajada,
                'velocidad_subida' => $this->velocidad_subida,
                'rehuso' => $this->rehuso,
                'nodo_id' => $this->nodo_id,
            ]);

            // Actualizar la lista de planes (sin cambios)
            $this->plans = Plan::all();
            
            // Vaciar los campos del formulario (sin cambios)
            $this->resetForm();
            
            // Notificaciones existentes (sin cambios)
            $this->dispatch('notify', 
                type: 'success', 
                message: 'Plan Creado exitosamente'
            );

        } catch (\Exception $e) {
            // Solo agregamos esta parte para capturar errores
            $this->dispatch('notify',
                type: 'error',
                message: 'Error al crear el plan: ' . $e->getMessage()
            );
        }
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->descripcion = '';
        $this->velocidad_bajada = '';
        $this->velocidad_subida = '';
        $this->rehuso = '';
        $this->nodo_id = '';
    }
   
    public function render()
    {
        return view('livewire.planes-formulario');
    }

    // Función para activar un plan en MikroTik
    public function activatePlan($planId)
    {
        $this->loadingActivation = true;
        $this->currentPlanActivating = $planId;
        $this->resetErrorBag(); // Limpiar errores anteriores
        $this->clearSuccessMessage(); // Limpiar mensajes anteriores

        try {
            $plan = Plan::with('nodo')->findOrFail($planId);
            
            if (!$plan->nodo) {
                $this->addError('activation', 'Este plan no tiene nodo asignado');
                return;
            }

            $mikroTikService = new MikroTikService(
                $plan->nodo->ip,
                $plan->nodo->user,
                $plan->nodo->pass,
                $plan->nodo->puerto_api ?? 8728
            );

            // Verificar si la cola ya existe primero
            $nombreCola = $plan->nombre;
            $existe = $mikroTikService->verificarColaExistente($nombreCola);
            
            if ($existe) {
                throw new \Exception("La cola padre '{$nombreCola}' ya existe en este nodo");
            }

            // Si no existe, crear la cola
            $result = $mikroTikService->crearColaPadre(
                $plan->nombre,
                $plan->velocidad_subida,
                $plan->velocidad_bajada
            );

            // Solo mostrar éxito si realmente se creó
            if ($result) {
                $this->successMessage = 'Cola padre creada exitosamente en el nodo '.$plan->nodo->nombre;
                $this->dispatch('show-success-message');
            }

        } catch (\Exception $e) {
            $this->addError('activation', 'Error al activar plan: '.$e->getMessage());
            Log::error("Error al activar plan {$planId}: ".$e->getMessage());
        } finally {
            $this->loadingActivation = false;
            $this->currentPlanActivating = null;
        }
    }
}
