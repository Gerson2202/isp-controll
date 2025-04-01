<?php

namespace App\Livewire;

use App\Models\Ticket;
use App\Models\Visita;
use Carbon\Carbon;
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
    // varibales de visitas
    public $fecha_inicio;
    public $fecha_cierre;
    public $descripcion;
    public $ticket_id;
    public $estadoVisita = 'pendiente'; // Estado por defecto

    // Método para actualizar la fecha de cierre automáticamente
    public function updatedFechaInicio($value)
    {
        // Si la fecha de inicio es válida
        if ($value) {
            $fecha_inicio = new \DateTime($value);
            $fecha_inicio->modify('+2 hours'); // Aumentar 2 horas

            // Establecer la fecha de cierre
            $this->fecha_cierre = $fecha_inicio->format('Y-m-d\TH:i');
        }
    }
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
    public function agendar()
    {
        // Convertir las fechas a un formato que FullCalendar entienda
        $fecha_inicio = Carbon::parse($this->fecha_inicio)->format('Y-m-d H:i:s');
        $fecha_cierre = Carbon::parse($this->fecha_cierre)->format('Y-m-d H:i:s');
    
        // Crear la visita
        Visita::create([
            'fecha_inicio' => $fecha_inicio,
            'fecha_cierre' => $fecha_cierre,
            'descripcion' => $this->descripcion,
            'ticket_id' => $this->ticket->id,
            'estado' => $this->estadoVisita,
        ]);
    
        // Cambiar estado del ticket a cerrado
        $this->ticket->update([
            'estado' => 'cerrado',
        ]);
    
        // Redirigir a la vista del calendario
        return redirect()->route('calendarioIndex'); // Asegúrate de tener esta ruta definida
    }


    public function render()
    {
        return view('livewire.ticket-edit');
    }
}
