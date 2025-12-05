<?php

namespace App\Livewire\Tecnico\Bodega;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Inventario;

class Equipos extends Component
{
    use WithPagination;

    public $buscar = '';

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        $usuario = auth()->user();

        // 🔹 Equipos directamente asignados al técnico
        $equiposPropios = Inventario::with('modelo')
            ->where('user_id', $usuario->id)
            ->where(function ($query) {
                $query->where('mac', 'like', "%{$this->buscar}%")
                    ->orWhere('serial', 'like', "%{$this->buscar}%")
                    ->orWhereHas('modelo', function ($q) {
                        $q->where('nombre', 'like', "%{$this->buscar}%");
                    });
            })
            ->paginate(10, ['*'], 'equiposPropios');

        // 🔹 Equipos en bodegas asignadas al técnico
        $bodegas = $usuario->bodegas()
            ->with(['inventarios.modelo' => function ($q) {
                $q->orderBy('nombre');
            }])
            ->get();

        return view('livewire.tecnico.bodega.equipos', compact('equiposPropios', 'bodegas'));
    }
}
