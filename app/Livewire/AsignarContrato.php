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

  
    // Función para guardar el contrato
    public function validarPrecio()
    {
        $this->validate([
            'precio' => [
                'required',
                'regex:/^\$?\d{1,3}(\.\d{3})*$/u', // Valida formato colombiano
                function ($attribute, $value, $fail) {
                    // Verifica que sea múltiplo de 1.000
                    $numero = (int) str_replace('.', '', $value);
                    if ($numero % 1000 != 0) {
                        $fail('El precio debe ser múltiplo de 1.000 (ej: 10.000, 1.000.000)');
                    }
                },
            ],
        ]);
    }
    public function guardarContrato()
    {
        try {
            // Validación de los datos
            $validatedData = $this->validate([
                'plan_id' => 'required|exists:plans,id',
                'fecha_inicio' => 'required|date|before:fecha_fin',
                'fecha_fin' => 'required|date|after:fecha_inicio',
                'precio' => [
                    'required',
                    'regex:/^\d{1,3}(\.\d{3})*$/u', // Valida formato colombiano
                    function ($attribute, $value, $fail) {
                        // Verifica que cada grupo después del punto tenga 3 dígitos
                        $partes = explode('.', $value);
                        
                        // El primer grupo puede tener 1-3 dígitos, los siguientes exactamente 3
                        foreach (array_slice($partes, 1) as $grupo) {
                            if (strlen($grupo) !== 3) {
                                $fail('Formato incorrecto. Use puntos cada 3 dígitos (ej: 1.000.000, 25.550)');
                                return;
                            }
                        }
                        
                        // Opcional: Validar que el número sea positivo
                        $numero = (int) str_replace('.', '', $value);
                        if ($numero <= 0) {
                            $fail('El precio debe ser mayor a cero');
                        }
                    },
                ],
            ], 
            [
                'fecha_inicio.before' => 'La fecha de inicio debe ser anterior a la fecha de fin',
                'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                'precio.regex' => 'El precio debe tener formato X.000 (ej: 10.000, 300.000)',
                ]);
            // Guardar el contrato
            $this->precio = str_replace('.', '', $this->precio);
            $contrato = Contrato::create([
                'cliente_id' => $this->cliente_id, // Ya tenemos el cliente_id en la propiedad
                'plan_id' => $this->plan_id,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'precio' => $this->precio,
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