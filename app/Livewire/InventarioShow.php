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
    public $fecha;
    public $descripcion;
    public $cliente_id;
    public $nodo_id;
    public $modalVisible = false;
    
// En tu componente Livewire
public $clienteSearch = '';
public $clientesFiltrados = [];

public function updatedClienteSearch($value)
{
    if (strlen($value) < 2) {
        $this->clientesFiltrados = [];
        return;
    }
    
    $this->clientesFiltrados = Cliente::where('nombre', 'like', '%'.$value.'%')
        ->limit(10)
        ->get()
        ->toArray();
}

public function selectCliente($clienteId)
{
    $this->cliente_id = $clienteId;
    
    // Verifica si se está deseleccionando (clienteId vacío)
    if (empty($clienteId)) {
        $this->clienteSearch = '';
        $this->clientesFiltrados = [];
        return;
    }
    
    // Solo busca el nombre si hay un ID válido
    $cliente = Cliente::find($clienteId);
    $this->clienteSearch = $cliente ? $cliente->nombre : '';
    $this->clientesFiltrados = [];
    $this->nodo_id = null; // Limpiar nodo si se selecciona cliente
}
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
        $this->fecha = $this->inventario->fecha ?? 'sin fecha';
    }

    
// Livewire Component - InventarioShow.php
public function guardar()
{
    if (($this->cliente_id && $this->nodo_id)) {
         // Notificaciones existentes (sin cambios)
         $this->dispatch('notify', 
         type: 'error', 
         message: 'Error ,Solo se permite asiganar a un cliente o nodo , No ambos');
         return;
    }
    
    $this->inventario->update([
        'modelo_id' => $this->modelo_id,
        'mac' => $this->mac,
        'descripcion' => $this->descripcion,
        'cliente_id' => empty($this->cliente_id) ? null : $this->cliente_id,
        'nodo_id' => empty($this->nodo_id) ? null : $this->nodo_id,
        'fecha' => empty($this->fecha) ? null : $this->fecha,

    ]);

        $this->cerrarModal();
        // Notificaciones existentes (sin cambios)
        $this->dispatch('notify', 
        type: 'success', 
        message: 'Equipo actualizado  exitosamente'
    );}

        public function mostrarModal()
    {
        $this->modalVisible = true;
    }

    public function cerrarModal()
    {
        $this->modalVisible = false;
    }

    public function render()
    {
        return view('livewire.inventario-show');
    }
}
