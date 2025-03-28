<?php

namespace App\Livewire;

use App\Models\Ticket;
use Livewire\Component;

class TicketEdit extends Component
{
    
    public $ticket;
    public $tipo_reporte;
    public $situacion;
    public $solucion;
    public $estado;
    public $isUpdated = false; // Para controlar si el ticket ha sido actualizado
    public $showMessage = false; // Para mostrar el mensaje de éxito

    public function mount($ticketId)
    {
        // Cargar el ticket basado en el ID pasado como parámetro
        $this->ticket = Ticket::findOrFail($ticketId);

        // Asignar los valores del ticket a las propiedades del componente
        $this->tipo_reporte = $this->ticket->tipo_reporte;
        $this->situacion = $this->ticket->situacion;
        $this->solucion = $this->ticket->solucion;
        $this->estado = $this->ticket->estado;

        if ($this->estado== 'cerrado') {
            $this->isUpdated = true;
        } else {
            $this->isUpdated = false;

        }
    }

    
    // Método para actualizar el ticket
    public function updateTicket()
    {
        // Validar los datos del formulario
        $this->validate([
            'tipo_reporte' => 'required|string',
            'situacion' => 'required|string',
            'solucion' => 'required|string',
        ]);

        // Actualizar el ticket con los nuevos valores
        $this->ticket->update([
            'tipo_reporte' => $this->tipo_reporte,
            'situacion' => $this->situacion,
            'solucion' => $this->solucion,
            'estado' => 'cerrado', // Cambiar estado a cerrado
            'fecha_cierre' => now(), // Asignar la fecha actual al campo fecha_cierre
        ]);

        // Activar el flag isUpdated para que se deshabiliten los campos
        $this->isUpdated = true;
        $this->showMessage = true; // Mostrar mensaje de éxito
    }

    // Método para agendar visita
    public function agendarVisita()
    {
        // Lógica para agendar la visita (puede ser un cambio en el estado del ticket, por ejemplo)
        $this->ticket->update(['estado' => 'visita agendada']);
        session()->flash('message', 'Visita agendada correctamente');
    }


    public function render()
    {
        return view('livewire.ticket-edit');
    }
}
