<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ActualizarEstadoCliente extends Component
{
    public $cliente;
    public $estado;

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->estado = $cliente->estado; // Cargar el estado actual
    }

    public function actualizarEstado()
    {
        $this->validate([
            'estado' => 'required|in:activo,suspendido,cortado',
        ]);

        $this->cliente->update(['estado' => $this->estado]);

        // Mensaje de éxito y redirección
        Session::flash('success', 'Estado actualizado correctamente.');
        return redirect()->route('clientes.show', $this->cliente->id);
    }

    public function render()
    {
        return view('livewire.actualizar-estado-cliente');
    }
}
