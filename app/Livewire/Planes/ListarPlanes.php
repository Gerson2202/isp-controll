<?php

namespace App\Livewire\Planes;

use App\Models\Cliente;
use Livewire\Component;
use App\Models\Nodo;
use App\Models\Plan;
use App\Services\MikroTikService;


use Illuminate\Support\Facades\Log; // Importar la clase Log
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ListarPlanes extends Component
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
    public $rafaga_max_bajada;
    public $rafaga_max_subida;
    public $velocidad_media_bajada;
    public $velocidad_media_subida;
    public $tiempo_rafaga_bajada;
    public $tiempo_rafaga_subida;
    public $prioridad;
    public $usar_rafaga = false;



    public $tiempo_input_bajada;
    public $tiempo_input_subida;

    // Calculados (solo display)
    public $burst_time_bajada;
    public $burst_time_subida;


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
        $plan = Plan::findOrFail($id);

        $this->planHasContracts = $plan->contratos()->exists();

        $this->plan_id = $plan->id;
        $this->nombre = $plan->nombre;
        $this->descripcion = $plan->descripcion;
        $this->velocidad_bajada = $plan->velocidad_bajada;
        $this->velocidad_subida = $plan->velocidad_subida;
        $this->rehuso = $plan->rehuso;
        $this->nodo_id = $plan->nodo_id;

        // 🔥 RÁFAGAS
        $this->usar_rafaga = !is_null($plan->rafaga_max_bajada);

        $this->rafaga_max_bajada = $plan->rafaga_max_bajada;
        $this->rafaga_max_subida = $plan->rafaga_max_subida;

        $this->velocidad_media_bajada = $plan->velocidad_media_bajada;
        $this->velocidad_media_subida = $plan->velocidad_media_subida;

        // input editable
        $this->tiempo_input_bajada = (
            $plan->rafaga_max_bajada && $plan->tiempo_rafaga_bajada
        )
            ? (int) ($plan->tiempo_rafaga_bajada / $plan->rafaga_max_bajada)
            : null;

        $this->tiempo_input_subida = (
            $plan->rafaga_max_subida && $plan->tiempo_rafaga_subida
        )
            ? (int) ($plan->tiempo_rafaga_subida / $plan->rafaga_max_subida)
            : null;


        // valores finales display
        $this->burst_time_bajada = $plan->tiempo_rafaga_bajada;
        $this->burst_time_subida = $plan->tiempo_rafaga_subida;

        $this->showModal = true;
    }

    // Funcion reactiva para cambiar el valor dinamicamente
    public function updated($property)
    {
        if (!$this->usar_rafaga) {
            $this->burst_time_bajada = null;
            $this->burst_time_subida = null;
            return;
        }

        if ($this->rafaga_max_bajada && $this->tiempo_input_bajada) {
            $this->burst_time_bajada =
                $this->rafaga_max_bajada * $this->tiempo_input_bajada;
        }

        if ($this->rafaga_max_subida && $this->tiempo_input_subida) {
            $this->burst_time_subida =
                $this->rafaga_max_subida * $this->tiempo_input_subida;
        }
    }


    // Actualizar el plan-- ultimo cambio solo se agrego la condicion de if para dejar actualizar sino tiene contrato
    public function updatePlan()
    {
        try {
            // 🔥 VALIDACIONES
            $this->validate();
            $this->validateRafagas();
        } catch (\Illuminate\Validation\ValidationException $e) {

            foreach ($e->validator->errors()->all() as $message) {
                $this->dispatch(
                    'notify',
                    type: 'error',
                    message: $message
                );
            }

            return; // ⛔ NO SIGUE
        }

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

            $this->dispatch(
                'notify',
                type: 'success',
                message: '¡Descripción del plan actualizada!'
            );
            $this->showModal = false;
            $this->resetForm();
            return; // Termina la ejecución aquí
        }
        $ningunCambio =
            $plan->nombre === $this->nombre &&
            $plan->velocidad_bajada == $this->velocidad_bajada &&
            $plan->velocidad_subida == $this->velocidad_subida &&
            $plan->rehuso === $this->rehuso &&
            $plan->nodo_id == $this->nodo_id &&
            $plan->descripcion === $this->descripcion &&
            // ===== RÁFAGAS =====
            (bool) $plan->rafaga_max_bajada === (bool) $this->rafaga_max_bajada &&
            (bool) $plan->rafaga_max_subida === (bool) $this->rafaga_max_subida &&
            (bool) $plan->velocidad_media_bajada === (bool) $this->velocidad_media_bajada &&
            (bool) $plan->velocidad_media_subida === (bool) $this->velocidad_media_subida &&
            (int) $plan->tiempo_rafaga_bajada === (int) $this->burst_time_bajada &&
            (int) $plan->tiempo_rafaga_subida === (int) $this->burst_time_subida;
        // Si no hay cambios, salir sin hacer nada
        if ($ningunCambio) {
            return;
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
                    ->whereHas('contratos', function ($query) {
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
                $mikroTikService->crearColaPadre($this->nombre, $this->velocidad_subida, $this->velocidad_bajada);
                // Indicadores de carga
                $this->totalClients = count($this->clientesAsociados);
                $processedClients = 0;

                // 7. Crear colas hijas
                foreach ($this->clientesAsociados as $cliente) {
                    $mikroTikService->crearColaHija(
                        $cliente,
                        [
                            'nombre' => $this->nombre,
                            'velocidad_subida' => $this->velocidad_subida,
                            'velocidad_bajada' => $this->velocidad_bajada,
                            'rehuso' => $this->rehuso,
                            'rafaga_max_bajada' => $this->usar_rafaga ? $this->rafaga_max_bajada : null,
                            'rafaga_max_subida' => $this->usar_rafaga ? $this->rafaga_max_subida : null,
                            'velocidad_media_bajada' => $this->usar_rafaga ? $this->velocidad_media_bajada : null,
                            'velocidad_media_subida' => $this->usar_rafaga ? $this->velocidad_media_subida : null,
                            'tiempo_rafaga_bajada' => $this->usar_rafaga ? (int) $this->burst_time_bajada : null,
                            'tiempo_rafaga_subida' => $this->usar_rafaga ? (int) $this->burst_time_subida : null,
                            'prioridad' => $this->prioridad,
                        ],
                        $cliente->ip
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

                    'rafaga_max_bajada' => $this->usar_rafaga ? $this->rafaga_max_bajada : null,
                    'rafaga_max_subida' => $this->usar_rafaga ? $this->rafaga_max_subida : null,

                    'velocidad_media_bajada' => $this->usar_rafaga ? $this->velocidad_media_bajada : null,
                    'velocidad_media_subida' => $this->usar_rafaga ? $this->velocidad_media_subida : null,

                    // 🔥 BURST TIME → SEGUNDOS (INT)
                    'tiempo_rafaga_bajada' => $this->usar_rafaga ? (int) $this->burst_time_bajada : null,
                    'tiempo_rafaga_subida' => $this->usar_rafaga ? (int) $this->burst_time_subida : null,

                ]);


                // 9. Confirmar la transacción si todo fue exitoso
                DB::commit();

                // Actualizar la lista de planes
                $this->plans = Plan::all();

                // Notificación Toastr
                $this->dispatch(
                    'notify',
                    type: 'success',
                    message: 'Plan actualizado exitosamente!'
                );

                // Cerrar el modal y resetear formulario
                $this->showModal = false;
                $this->resetForm();
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                DB::rollBack();
                $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'Error: El plan no fue encontrado'
                );
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'Error al actualizar el plan: ' . $e->getMessage()
                );
            } finally {
                $this->isProcessing = false; // Desactivar indicador
            }
        } else {
            // Si no tiene contrato asociado me deja actualizar con normalidad
            $plan->update([
                'nombre' => $this->nombre,
                'descripcion' => $this->descripcion,
                'velocidad_bajada' => $this->velocidad_bajada,
                'velocidad_subida' => $this->velocidad_subida,
                'rehuso' => $this->rehuso,
                'nodo_id' => $this->nodo_id,

                'rafaga_max_bajada' => $this->usar_rafaga ? $this->rafaga_max_bajada : null,
                'rafaga_max_subida' => $this->usar_rafaga ? $this->rafaga_max_subida : null,

                'velocidad_media_bajada' => $this->usar_rafaga ? $this->velocidad_media_bajada : null,
                'velocidad_media_subida' => $this->usar_rafaga ? $this->velocidad_media_subida : null,

                // 🔥 BURST TIME → SEGUNDOS (INT)
                'tiempo_rafaga_bajada' => $this->usar_rafaga ? (int) $this->burst_time_bajada : null,
                'tiempo_rafaga_subida' => $this->usar_rafaga ? (int) $this->burst_time_subida : null,

            ]);


            $this->plans = Plan::all();
            // Notificación Toastr
            $this->dispatch(
                'notify',
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
                $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'No se puede eliminar el plan porque está asociado a contratos existentes.'
                );
                return; // Detener la ejecución
            }

            $plan->delete();
            $this->plans = Plan::all(); // Volver a cargar los planes

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Plan eliminado con éxito!'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al eliminar plan: ' . $e->getMessage()
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


    public function resetForm()
    {
        $this->nombre = '';
        $this->descripcion = '';
        $this->velocidad_bajada = '';
        $this->velocidad_subida = '';
        $this->rehuso = '';
        $this->nodo_id = '';

        $this->rafaga_max_bajada = null;
        $this->rafaga_max_subida = null;
        $this->velocidad_media_bajada = null;
        $this->velocidad_media_subida = null;
        $this->tiempo_rafaga_bajada = null;
        $this->tiempo_rafaga_subida = null;
        $this->prioridad = null;
    }

    // Validacion de campos
    protected function validateRafagas()
    {
        if (!$this->usar_rafaga) {
            return;
        }

        $errors = [];

        // ===== REQUIRED =====
        if (!$this->rafaga_max_bajada) {
            $errors[] = 'La ráfaga máxima de bajada es obligatoria.';
        }

        if (!$this->rafaga_max_subida) {
            $errors[] = 'La ráfaga máxima de subida es obligatoria.';
        }

        if (!$this->velocidad_media_bajada) {
            $errors[] = 'La velocidad media de bajada es obligatoria.';
        }

        if (!$this->velocidad_media_subida) {
            $errors[] = 'La velocidad media de subida es obligatoria.';
        }

        if (!$this->tiempo_input_bajada) {
            $errors[] = 'El tiempo de ráfaga de bajada es obligatorio.';
        }

        if (!$this->tiempo_input_subida) {
            $errors[] = 'El tiempo de ráfaga de subida es obligatorio.';
        }

        // ===== LÓGICA =====
        if ($this->rafaga_max_bajada <= $this->velocidad_bajada) {
            $errors[] = 'La ráfaga de bajada debe ser mayor que la velocidad base.';
        }

        if ($this->rafaga_max_subida <= $this->velocidad_subida) {
            $errors[] = 'La ráfaga de subida debe ser mayor que la velocidad base.';
        }



        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'rafaga' => $errors
            ]);
        }
    }


    public function render()
    {
        $this->applyFilter();
        return view('livewire.planes.listar-planes', [
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

                $this->dispatch(
                    'notify',
                    type: 'success',
                    message: 'Cola padre creada exitosamente en el nodo ' . $plan->nodo->nombre
                );
            }
        } catch (\Exception $e) {
            $this->addError('activation', 'Error al activar plan: ' . $e->getMessage());
            Log::error("Error al activar plan {$planId}: " . $e->getMessage());
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al activar plan {$planId}: ' . $e->getMessage()
            );
        } finally {
            $this->loadingActivation = false;
            $this->currentPlanActivating = null;
        }
    }
}
