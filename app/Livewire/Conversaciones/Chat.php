<?php

namespace App\Livewire\Conversaciones;

use Livewire\Component;
use App\Models\Conversacion;
use App\Models\Mensaje;
use Carbon\Carbon;

class Chat extends Component
{
    public $conversaciones;
    public $conversacionActiva;
    public $mensajes;
    public $clienteActivo;
    public $search = '';

    public function mount()
    {
        $this->cargarConversaciones();
        
        if ($this->conversaciones->count() > 0) {
            $this->seleccionarConversacion($this->conversaciones->first()->id);
        }
    }

    public function cargarConversaciones()
    {
        $query = Conversacion::with(['cliente', 'mensajes' => function($q) {
            $q->latest('fecha_mensaje')->limit(1);
        }]);

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_contacto', 'like', $searchTerm)
                  ->orWhere('telefono', 'like', $searchTerm)
                  ->orWhereHas('cliente', function($sub) use ($searchTerm) {
                      $sub->where('nombre', 'like', $searchTerm)
                          ->orWhere('telefono', 'like', $searchTerm); // Cambiar email por telefono
                  });
            });
        }

        $this->conversaciones = $query->orderBy('ultima_actividad', 'desc')->get();
    }

    public function seleccionarConversacion($id)
    {
        $this->conversacionActiva = Conversacion::with(['cliente', 'mensajes' => function($q) {
            $q->orderBy('fecha_mensaje', 'asc');
        }])->find($id);

        if ($this->conversacionActiva) {
            $this->mensajes = $this->conversacionActiva->mensajes;
            $this->clienteActivo = $this->conversacionActiva->cliente;
            $this->dispatch('mensajes-cargados');
        }
    }

    public function buscarConversaciones()
    {
        $this->cargarConversaciones();
    }

    public function render()
    {
        return view('livewire.conversaciones.chat', [
            'conversaciones' => $this->conversaciones,
            'conversacionActiva' => $this->conversacionActiva,
            'mensajes' => $this->mensajes ?? collect(),
            'clienteActivo' => $this->clienteActivo,
        ]);
    }
}