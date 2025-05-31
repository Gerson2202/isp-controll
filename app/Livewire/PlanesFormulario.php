<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Nodo;
use App\Models\Plan;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\Log; // Importar la clase Log
use Illuminate\Support\Facades\DB;

class PlanesFormulario extends Component
{
    public $loadingActivation = false;
    public $currentPlanActivating = null;
    public $showModal = false;
    public $plans;
    public $nodos;
    public $nombre, $descripcion, $velocidad_bajada, $velocidad_subida, $rehuso, $plan_id;
    public $nodo_id = ''; // o null
    public $successMessage = ''; // Propiedad para el mensaje de éxito
    public $clientesAsociados = []; // clientes que tienen el plan
    public $nodo;
    public $isProcessing = false;
    public $progress = 0;
    public $totalClients = 0;
    public $filteredPlans; // Plans filtrados
    public $plan;
    public $planHasContracts = false; // Agrega esta propiedad
    public $nodo_id_Filtro;
    
    public function mount()
    {
        // Cargar todos los planes con su relación de nodo, ordenados por el nombre del nodo
        $this->filteredPlans = Plan::with('nodo')->get();     
        $this->nodos = Nodo::all();
       
    }
    // Funcion oculatar modal
    public function hide()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function applyFilter()
    {
        $query = Plan::with('nodo');
        
        if (!empty($this->nodo_id_Filtro)) {
            $query->where('nodo_id', $this->nodo_id_Filtro);
        }
        
        $this->filteredPlans = $query->get();
    }
    // Mostrar el modal para actualizar
    public function editPlan($id)
    {       
        
         $plan = Plan::find($id);
         $this->planHasContracts = $plan->contratos()->exists(); // Asigna el resultado aquí
         $this->plan_id = $plan->id;
         $this->nombre = $plan->nombre;
         $this->descripcion = $plan->descripcion;
         $this->velocidad_bajada = $plan->velocidad_bajada;
         $this->velocidad_subida = $plan->velocidad_subida;
         $this->rehuso = $plan->rehuso;
         $this->nodo_id = $plan->nodo_id;
         $this->showModal = true;  
    }

    // Actualizar el plan-- ultimo cambio solo se agrego la condicion de if para dejar actualizar sino tiene contrato
    public function updatePlan()
    {
        $plan = Plan::findOrFail($this->plan_id);
        // 1. Verificar si SOLO cambió la descripción (y ningún otro campo relevante para MikroTik)
        $soloDescripcionCambio = 
        $plan->nombre === $this->nombre &&
        $plan->velocidad_bajada == $this->velocidad_bajada &&
        $plan->velocidad_subida == $this->velocidad_subida &&
        $plan->rehuso === $this->rehuso &&
        $plan->nodo_id == $this->nodo_id &&
        $plan->descripcion !== $this->descripcion; // Solo esto cambió
         // 2. Si solo es la descripción, actualiza directo en DB sin tocar MikroTik
        if ($soloDescripcionCambio) {
            $plan->update(['descripcion' => $this->descripcion]);
            
            $this->dispatch('notify', 
                type: 'success',
                message: '¡Descripción del plan actualizada!'
            );
            $this->showModal = false;
            $this->resetForm();
            return; // Termina la ejecución aquí
        }
        //Verificar si el plan tiene contratos asociados
        if ($plan->contratos()->exists()) {
                
            $this->isProcessing = true;
            $this->progress = 0;
            // Iniciar transacción de base de datos
            DB::beginTransaction();
                try {
                    // 1. Obtener el plan y su nodo relacionado
                    $plan = Plan::with('nodo')->findOrFail($this->plan_id);
                    $this->nodo = $plan->nodo;

                    // 2. Obtener clientes asociados al plan
                    $this->clientesAsociados = Cliente::with('contratos')
                        ->whereHas('contratos', function($query) {
                            $query->where('plan_id', $this->plan_id);
                        })->get();

                    // 3. Validar que el nodo tenga credenciales de API
                    if (!$this->nodo || !$this->nodo->ip || !$this->nodo->user) {
                        throw new \Exception("El nodo asociado no tiene credenciales de API configuradas.");
                    }

                    // 4. Inicializar servicio MikroTik
                    $mikroTikService = new MikroTikService(
                        $this->nodo->ip,
                        $this->nodo->user,
                        $this->nodo->pass,
                        $this->nodo->puerto_api ?? 8728
                    );

                    // 5. Eliminar cola padre y sus hijas
                    $mikroTikService->eliminarColaPadreYHijas($plan->nombre);   
                    // 6. Crear nueva cola padre
                    $mikroTikService->crearColaPadre($this->nombre,$this->velocidad_subida,$this->velocidad_bajada);
                    // Indicadores de carga
                    $this->totalClients = count($this->clientesAsociados);
                    $processedClients = 0;

                    // 7. Crear colas hijas
                    foreach ($this->clientesAsociados as $cliente) {
                        $mikroTikService->crearColaHija(
                            $cliente->id,
                            $cliente->ip,
                            $this->nombre,
                            $this->velocidad_subida,
                            $this->velocidad_bajada,
                            $this->rehuso ?? '1:1'
                        );
                        $processedClients++;
                        $this->progress = intval(($processedClients / $this->totalClients) * 100);
                    }

                    // 8. Actualizar el plan en la base de datos (dentro de la transacción)
                    $plan->update([
                        'nombre' => $this->nombre,
                        'descripcion' => $this->descripcion,
                        'velocidad_bajada' => $this->velocidad_bajada,
                        'velocidad_subida' => $this->velocidad_subida,
                        'rehuso' => $this->rehuso,
                        'nodo_id' => $this->nodo_id,
                    ]);

                    // 9. Confirmar la transacción si todo fue exitoso
                    DB::commit();

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
                    DB::rollBack();
                    $this->dispatch('notify', 
                        type: 'error',
                        message: 'Error: El plan no fue encontrado'
                    );

                } catch (\Illuminate\Validation\ValidationException $e) {
                    DB::rollBack();
                    $this->dispatch('notify',
                        type: 'error',
                        message: 'Error de validación: ' . implode(' ', $e->validator->errors()->all())
                    );

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->dispatch('notify',
                        type: 'error',
                        message: 'Error al actualizar el plan: ' . $e->getMessage()
                    );
                }
                finally {
                    $this->isProcessing = false; // Desactivar indicador
                }
              
                
        }else {
            // Si no tiene contrato asociado me deja actualizar con normalidad
            $plan->update([
                    'nombre' => $this->nombre,
                    'descripcion' => $this->descripcion,
                    'velocidad_bajada' => $this->velocidad_bajada,
                    'velocidad_subida' => $this->velocidad_subida,
                    'rehuso' => $this->rehuso,
                    'nodo_id' => $this->nodo_id,
                ]);

                
                $this->plans = Plan::all();
                // Notificación Toastr
                $this->dispatch('notify', 
                    type: 'success',
                    message: 'Plan actualizado exitosamente!'
                );
                // Cerrar el modal y resetear formulario
                $this->showModal = false;
                $this->resetForm();
              
        }    
    }

    // Borrar el plan
    public function deletePlan($id)
    {
        try {
            $plan = Plan::findOrFail($id);
            
            // Verificar si el plan tiene contratos asociados
            if ($plan->contratos()->exists()) {
                $this->dispatch('notify', 
                    type: 'error',
                    message: 'No se puede eliminar el plan porque está asociado a contratos existentes.'
                );
                return; // Detener la ejecución
            }
            
            $plan->delete();
            $this->plans = Plan::all(); // Volver a cargar los planes
            
            $this->dispatch('notify', 
                type: 'success',
                message: 'Plan eliminado con éxito!'
            );
            
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error al eliminar plan: '.$e->getMessage()
            );
        }
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
        $this->applyFilter();
        return view('livewire.planes-formulario', [
            'nodos' => Nodo::all(),
            // Resto de tus datos...
        ]);
       
    }

    // Función para activar un plan en MikroTik
    public function activatePlan($planId)
    {
        $this->loadingActivation = true;
        $this->currentPlanActivating = $planId;
        $this->resetErrorBag(); // Limpiar errores anteriores

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

                $this->dispatch('notify', 
                type: 'success', 
                message: 'Cola padre creada exitosamente en el nodo '.$plan->nodo->nombre
                );
            }

        } catch (\Exception $e) {
            $this->addError('activation', 'Error al activar plan: '.$e->getMessage());
            Log::error("Error al activar plan {$planId}: ".$e->getMessage());
            $this->dispatch('notify', 
                type: 'error', 
                message: 'Error al activar plan {$planId}: '.$e->getMessage()
                );
        } finally {
            $this->loadingActivation = false;
            $this->currentPlanActivating = null;
        }
    }
}
