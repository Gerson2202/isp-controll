<?php

namespace App\Livewire\Tecnico\Bodega;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConsumibleStock;

class Consumibles extends Component
{
    use WithPagination;

    public $buscar = '';

    // 🔹 Para resetear la paginación al cambiar el buscador
    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function render()
    {
        $usuario = auth()->user();

        // IDs de las bodegas relacionadas al usuario
        $bodegas = $usuario->bodegas()->with(['consumiblesStock.consumible'])->get();

        // Consumibles personales del técnico
        $consumiblesUsuario = ConsumibleStock::with('consumible')
            ->where('usuario_id', $usuario->id)
            ->whereHas('consumible', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%");
            })
            ->orderByDesc('updated_at')
            ->paginate(10, ['*'], 'usuario_page');

        return view('livewire.tecnico.bodega.consumibles', [
            'consumiblesUsuario' => $consumiblesUsuario,
            'bodegas' => $bodegas,
        ]);
    }
}
