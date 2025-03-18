<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Models\pool;
use Livewire\Component;

class PoolComponent extends Component
{
    public $nodos, $pool_id, $nodo_id, $nombre, $start_ip, $end_ip, $descripcion;
    public $pools;  // Para almacenar todos los pools
    public $showModal = false;
    public $successMessage = ''; // Propiedad para el mensaje de éxito


    // Método para cargar los pools y nodos al cargar la página
    public function mount()
    {
        $this->nodos = Nodo::all();  // Obtener todos los nodos
        $this->pools = Pool::all();  // Obtener todos los pools
    }

    // Método para guardar un nuevo pool
    public function store()
    {
        $this->validate([
            'nodo_id' => 'required|exists:nodos,id',
            'nombre' => 'required|string|max:255',
            'start_ip' => 'required|ip',
            'end_ip' => 'required|ip',
            'descripcion' => 'nullable|string|max:500',  // Validación del nuevo campo
        ]);

        Pool::create([
            'nodo_id' => $this->nodo_id,
            'nombre' => $this->nombre,
            'start_ip' => $this->start_ip,
            'end_ip' => $this->end_ip,
            'descripcion' => $this->descripcion,  // Guardar el campo descripción
        ]);

        // Limpiar los campos después de guardar
        $this->resetFields();

        // Actualizar la lista de pools
        $this->pools = Pool::all();

        $this->successMessage = 'Pool Creado exitosamente!';

        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');
    }

    // Método para mostrar los datos del pool en el modal para edición
    public function edit($id)
    {
        $pool = Pool::find($id);
        
        // Asignar los valores para editar
        $this->pool_id = $pool->id;
        $this->nodo_id = $pool->nodo_id;
        $this->nombre = $pool->nombre;
        $this->start_ip = $pool->start_ip;
        $this->end_ip = $pool->end_ip;
        $this->descripcion = $pool->descripcion;  // Agregar la descripción
        // Abrir el modal para editar
        $this->showModal = true;  
        $this->successMessage = '';

    }

    // Método para actualizar el pool
    public function update()
    {
        $this->validate([
            'nodo_id' => 'required|exists:nodos,id',
            'nombre' => 'required|string|max:255',
            'start_ip' => 'required|ip',
            'end_ip' => 'required|ip',
            'descripcion' => 'nullable|string|max:500',  // Validación del nuevo campo
        ]);

        $pool = Pool::find($this->pool_id);
        $pool->update([
            'nodo_id' => $this->nodo_id,
            'nombre' => $this->nombre,
            'start_ip' => $this->start_ip,
            'end_ip' => $this->end_ip,
            'descripcion' => $this->descripcion,  // Actualizar el campo descripción
        ]);

        // Limpiar los campos después de actualizar
        $this->resetFields();
        $this->showModal = false;
        $this->successMessage = 'Pool Actualizado exitosamente!';

        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');

        // Actualizar la lista de pools
        $this->pools = Pool::all();

       
    }

    // Método para eliminar un pool
    public function delete($id)
    {
        Pool::find($id)->delete();

        // Actualizar la lista de pools después de eliminar
        $this->pools = Pool::all();

        $this->successMessage = 'Pool Eliminado exitosamente!';

        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');
    }

    // Método para limpiar los campos del formulario
    public function resetFields()
    {
        $this->pool_id = null;
        $this->nodo_id = null;
        $this->nombre = '';
        $this->start_ip = '';
        $this->end_ip = '';
        $this->descripcion = '';  // Limpiar el campo descripción
    }
    // Funcion oculatar modal
    public function hide()
    {
        $this->showModal = false;
        $this->resetFields();
    }
        // Funcion Limpiar el mensaje de éxito
    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }
    public function render()
    {
        return view('livewire.pool-component');
    }
}
