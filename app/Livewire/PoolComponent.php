<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Models\pool;
use Livewire\Component;

class PoolComponent extends Component
{
    public $nodos, $pool_id, $nodo_id, $nombre, $start_ip, $end_ip, $descripcion;
    public $pools;
    public $showModal = false;

    protected $rules = [
        'nodo_id' => 'required|exists:nodos,id',
        'nombre' => 'required|string|max:255|',
        'start_ip' => 'required|ip',
        'end_ip' => 'required|ip|different:start_ip',
        'descripcion' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        $this->loadPools();
        $this->nodos = Nodo::all();
    }

    public function loadPools()
    {
        $this->pools = Pool::with('nodo')->latest()->get();
    }

    public function save()
    {
        $rules = [
            'nodo_id' => 'required|exists:nodos,id',
            'nombre' => 'required|string|max:255' . $this->pool_id,
            'start_ip' => 'required|ip',
            'end_ip' => 'required|ip|different:start_ip',
            'descripcion' => 'nullable|string|max:500',
        ];

        $validated = $this->validate($rules);

        try {
            if ($this->pool_id) {
                // Modo edición
                $pool = Pool::findOrFail($this->pool_id);
                $pool->update($validated);

                $this->dispatch('notify', 
                    type: 'success',
                    message: 'Pool actualizado exitosamente!');
            } else {
                // Modo creación
                Pool::create($validated);

                $this->dispatch('notify', 
                    type: 'success',
                    message: 'Pool creado exitosamente!');
            }

            $this->resetFields();
            $this->loadPools();

        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $pool = Pool::findOrFail($id);
        
        $this->pool_id = $pool->id;
        $this->nodo_id = $pool->nodo_id;
        $this->nombre = $pool->nombre;
        $this->start_ip = $pool->start_ip;
        $this->end_ip = $pool->end_ip;
        $this->descripcion = $pool->descripcion;
        
        $this->showModal = true;
    }


    public function delete($id)
    {
        try {
            $pool = Pool::findOrFail($id);
            
            // Verificar si el pool tiene clientes asociados
            if ($pool->clientes()->count() > 0) {
                $this->dispatch('notify', 
                    type: 'error',
                    message: 'No se puede eliminar el pool porque tiene clientes asignados.'
                );
                return; // Esto detiene la ejecución
            }
            
            $pool->delete();
            $this->loadPools();
            
            $this->dispatch('notify', 
                type: 'success',
                message: 'Pool eliminado exitosamente!'
            );
            
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error al eliminar pool: '.$e->getMessage()
            );
        }
    }

    public function resetFields()
    {
        $this->reset([
            'pool_id', 'nodo_id', 'nombre', 
            'start_ip', 'end_ip', 'descripcion'
        ]);
    }

    public function hide()
    {
        $this->showModal = false;
        $this->resetFields();
    }

    public function render()
    {
        return view('livewire.pool-component');
    }
}