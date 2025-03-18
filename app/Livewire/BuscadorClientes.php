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
            // Filtrar clientes por nombre
            $this->clientes = Cliente::where('nombre', 'like', '%' . $this->query . '%')
                ->limit(10) // Limitar resultados
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
