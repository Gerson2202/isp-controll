<?php

namespace App\Livewire\Tecnico\Visitas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Visita;

class Tabla extends Component
{
    use WithPagination;

    public $search = '';
    public $visitaSeleccionada;

    protected $queryString = ['search'];

    // Si quieres que la paginación se reinicie al cambiar búsqueda:
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function verInformacion($id)
    {
        $this->visitaSeleccionada = \App\Models\Visita::with([
            'ticket.cliente.pool.nodo',
            'usuarios' // 👈 importante
        ])->find($id);

        $this->dispatch('abrir-modal');
    }


    public function cerrarVisita($id)
    {
        return redirect()->route('tecnico.visitas.cerrar', $id);
    }

    public function render()
    {
        $userId = auth()->id();

        $visitas = Visita::with('ticket.cliente')
            ->whereHas('usuarios', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereIn('estado', ['Pendiente', 'En progreso'])
            ->when($this->search, function ($query) {
                $query->whereHas('ticket', function ($ticketQuery) {
                    $ticketQuery->where('id', 'like', '%' . $this->search . '%')
                        ->orWhereHas('cliente', function ($clienteQuery) {
                            $clienteQuery->where('nombre', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.tecnico.visitas.tabla', [
            'visitas' => $visitas,
        ]);
    }
}
