<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visita;
use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;


class AgendarVisita extends Component

{
    public $ticket_id;
    public $fecha_inicio;
    public $fecha_cierre;
    public $usuarios = [];
    public $tickets;

    public function mount()
    {
        // Obtener todos los tickets y usuarios para mostrarlos en el formulario
        $this->tickets = Ticket::all();
    }

    // Método para agendar una nueva visita
    public function agendarVisita()
    {
        $this->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'fecha_inicio' => 'required|date',
            'fecha_cierre' => 'required|date|after:fecha_inicio',
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:users,id',
        ]);

        // Crear la visita
        $visita = Visita::create([
            'ticket_id' => $this->ticket_id,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_cierre' => $this->fecha_cierre,
        ]);

        // Asignar los usuarios a la visita
        $visita->usuarios()->attach($this->usuarios);

        // Resetear los campos después de crear la visita
        $this->reset();

        // Emitir evento de actualización si es necesario
        session()->flash('message', 'Visita agendada exitosamente!');
    }

    public function render()
    {
        $visitas = Visita::with('usuarios')->get(); // Obtener visitas con los usuarios asignados
    
        return view('livewire.agendar-visita', [
            'usuarios' => User::all(),
            'visitas' => $visitas, // Pasa las visitas para mostrar en el calendario
        ]);
    }
}
