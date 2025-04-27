<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Models\Pool;
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

    public function store()
    {
        $validated = $this->validate();
        
        try {
            Pool::create($validated);
            
            $this->resetFields();
            $this->loadPools();
            
            $this->dispatch('notify', 
                type: 'success',
                message: 'Pool creado exitosamente!'
            );
            
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error al crear pool: '.$e->getMessage()
            );
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

    public function update()
    {
        $this->validate([
            'nodo_id' => 'required|exists:nodos,id',
            'nombre' => 'required|string|max:255|unique:pools,nombre,'.$this->pool_id,
            'start_ip' => 'required|ip',
            'end_ip' => 'required|ip|different:start_ip',
            'descripcion' => 'nullable|string|max:500',
        ]);

        try {
            $pool = Pool::find($this->pool_id);
            $pool->update([
                'nodo_id' => $this->nodo_id,
                'nombre' => $this->nombre,
                'start_ip' => $this->start_ip,
                'end_ip' => $this->end_ip,
                'descripcion' => $this->descripcion,
            ]);

            $this->hide();
            $this->loadPools();
            
            $this->dispatch('notify', 
                type: 'success',
                message: 'Pool actualizado exitosamente!'
            );
            
        } catch (\Exception $e) {
            $this->dispatch('notify', 
                type: 'error',
                message: 'Error al actualizar pool: '.$e->getMessage()
            );
        }
    }

    public function delete($id)
    {
        try {
            Pool::findOrFail($id)->delete();
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