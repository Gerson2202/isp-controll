<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class CrearTicket extends Component
{

      // Definir las propiedades del formulario
      public $tipo_reporte;
      public $situacion;
      public $estado = 'Abierto'; // Por defecto el estado es 'Abierto'
      public $cliente_id;
      public $successMessage = ''; // Propiedad para el mensaje de éxito


      // Validación de los campos
    protected $rules = [
        'tipo_reporte' => 'required|string',
        'situacion' => 'required|string',
        'estado' => 'required|string',
    ];

    public function crearTicket()
    {
        // Validar los datos antes de crear el ticket
        $this->validate();

        // Crear el ticket
        Ticket::create([
            'tipo_reporte' => $this->tipo_reporte,
            'situacion' => $this->situacion,
            'estado' => $this->estado,
            'cliente_id' => $this->cliente_id,  
            'user_id' => auth()->id(), // Asigna el ID del usuario autenticado
        ]);

        // Limpiar los campos del formulario después de crear el ticket
        $this->reset(['tipo_reporte', 'situacion', 'estado']);
        // Mostrar el mensaje de éxito
        $this->successMessage = 'Ticket Creado exitosamente!';
        // Emitir un evento o redirigir si lo deseas (por ejemplo, mostrar un mensaje de éxito)
        $this->dispatch('show-success-message');
        // Realiza alguna acción y luego redirige a la misma página con la misma id
        // return redirect()->route('clientes.show', ['id' => $this->cliente_id]);
    }

    public function render()
    {
        return view('livewire.crear-ticket');
    }
}
