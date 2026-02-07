<?php

namespace App\Livewire\Planes;

use Livewire\Component;
use App\Models\Plan;
use App\Models\Nodo;
use Illuminate\Support\Str;

class CrearPlan extends Component
{
    // ===== BASICOS =====
    public $nombre;
    public $descripcion;
    public $velocidad_bajada;
    public $velocidad_subida;
    public $rehuso;
    public $nodo_id;

    // ===== RAFAGA =====
    public $usar_rafaga = false;
    public $rafaga_max_bajada;
    public $rafaga_max_subida;
    public $velocidad_media_bajada;
    public $velocidad_media_subida;

    // 👉 inputs de tiempo (NO se guardan)
    public $tiempo_input_bajada;
    public $tiempo_input_subida;

    // 👉 valores reales que se guardan
    public $burst_time_bajada = 0;
    public $burst_time_subida = 0;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string',
        'velocidad_bajada' => 'required|numeric|min:1',
        'velocidad_subida' => 'required|numeric|min:1',
        'rehuso' => 'required',
        'nodo_id' => 'required|exists:nodos,id',
    ];

    // 🔥 REACTIVIDAD
    public function updated($property)
    {
        if (!$this->usar_rafaga) {
            $this->burst_time_bajada = 0;
            $this->burst_time_subida = 0;
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

    public function submitPlan()
    {
        $this->validate();

        // 🚨 VALIDACIÓN: Si la ráfaga está activada, todos los campos deben estar llenos
        if ($this->usar_rafaga) {
            $camposRafaga = [
                'rafaga_max_bajada',
                'rafaga_max_subida',
                'velocidad_media_bajada',
                'velocidad_media_subida',
                'tiempo_input_bajada',
                'tiempo_input_subida'
            ];

            foreach ($camposRafaga as $campo) {
                if (empty($this->$campo)) {
                    return $this->dispatch(
                        'notify',
                        type: 'error',
                        message: 'Todos los campos de ráfaga son obligatorios cuando la ráfaga está activada'
                    );
                }
            }

            // 🚨 VALIDACIONES MIKROTIK (las que ya tenías)
            if ($this->rafaga_max_bajada <= $this->velocidad_bajada) {
                return $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'La ráfaga de bajada debe ser mayor a la velocidad base'
                );
            }

            if ($this->rafaga_max_subida <= $this->velocidad_subida) {
                return $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'La ráfaga de subida debe ser mayor a la velocidad base'
                );
            }
        }

        $nombreNormalizado = strtoupper(Str::slug($this->nombre, '_'));

        if (Plan::where('nombre', $nombreNormalizado)->exists()) {
            return $this->dispatch(
                'notify',
                type: 'error',
                message: 'El plan ya existe'
            );
        }

        Plan::create([
            'nombre' => $nombreNormalizado,
            'descripcion' => $this->descripcion,
            'velocidad_bajada' => $this->velocidad_bajada,
            'velocidad_subida' => $this->velocidad_subida,
            'rehuso' => $this->rehuso,
            'nodo_id' => $this->nodo_id,

            'rafaga_max_bajada' => $this->usar_rafaga ? $this->rafaga_max_bajada : null,
            'rafaga_max_subida' => $this->usar_rafaga ? $this->rafaga_max_subida : null,
            'velocidad_media_bajada' => $this->usar_rafaga ? $this->velocidad_media_bajada : null,
            'velocidad_media_subida' => $this->usar_rafaga ? $this->velocidad_media_subida : null,

            // 🔥 AQUÍ SE GUARDA EL BURST TIME
            'tiempo_rafaga_bajada' => $this->usar_rafaga ? $this->burst_time_bajada : null,
            'tiempo_rafaga_subida' => $this->usar_rafaga ? $this->burst_time_subida : null,
        ]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Plan creado con exito'
        );
        $this->reset();
    }

    public function render()
    {
        return view('livewire.planes.crear-plan', [
            'nodos' => Nodo::all()
        ]);
    }
}
