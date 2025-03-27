<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Modelo;
use App\Models\Nodo;
use Livewire\Component;
use Livewire\Attributes\On;

class InventarioShow extends Component
{
    public $inventario;
    public $modelos;
    public $clientes;
    public $nodos;
    public $modelo_id;
    public $mac;
    public $descripcion;
    public $cliente_id;
    public $nodo_id;
    public $modalVisible = false;
    public $errorMessage;
    public $successMessage;
    

#[On('clienteSeleccionado')]
public function setCliente($data)
{
    $this->cliente_id = $data['value'];
}

    public function mount($inventarioId)
    {
        $this->inventario = Inventario::find($inventarioId);
        $this->modelos = Modelo::all();
        $this->clientes = Cliente::all();
        $this->nodos = Nodo::all();
        $this->modelo_id = $this->inventario->modelo_id;
        $this->mac = $this->inventario->mac;
        $this->descripcion = $this->inventario->descripcion;
        $this->cliente_id = $this->inventario->cliente_id;
        $this->nodo_id = $this->inventario->nodo_id;
    }

    
// Livewire Component - InventarioShow.php
public function guardar()
{
    if (($this->cliente_id && $this->nodo_id)) {
        $this->errorMessage = "Solo se puede asignar el inventario a un cliente o a un nodo, no ambos.";
        return;
    }
    
    $this->inventario->update([
        'modelo_id' => $this->modelo_id,
        'mac' => $this->mac,
        'descripcion' => $this->descripcion,
        'cliente_id' => empty($this->cliente_id) ? null : $this->cliente_id,
        'nodo_id' => empty($this->nodo_id) ? null : $this->nodo_id,
    ]);

    $this->cerrarModal();
    $this->successMessage = "Inventario actualizado con éxito.";
}

        public function mostrarModal()
    {
        $this->modalVisible = true;
    }

    public function cerrarModal()
    {
        $this->modalVisible = false;
        $this->errorMessage = null;
        $this->successMessage = null;
    }

    public function render()
    {
        return view('livewire.inventario-show');
    }
}
