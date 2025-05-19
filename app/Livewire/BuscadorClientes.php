<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;

class BuscadorClientes extends Component
{
    public $query = ''; // Texto de búsqueda
    public $clientes = []; // Resultados filtrados

    // Método que se ejecuta al actualizar $query
    public function updatedQuery()
    {
        if (empty($this->query)) {
            $this->clientes = [];
        } else {
            $query = $this->query;
            $this->clientes = Cliente::where('nombre', 'like', '%' . $query . '%')
                ->when(is_numeric($query), function($q) use ($query) {
                    $q->orWhere('id', $query);
                }, function($q) use ($query) {
                    $q->orWhere('id', 'like', '%' . $query . '%');
                })
                ->limit(10)
                ->get();
        }
    }

    // Método para redirigir a la vista del cliente
    public function verCliente($clienteId)
    {
        return redirect()->route('clientes.show', $clienteId);
    }

    public function render()
    {
        return view('livewire.buscador-clientes');
    }

   
}
