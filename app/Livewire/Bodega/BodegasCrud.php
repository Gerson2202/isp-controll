<?php
namespace App\Livewire\Bodega;

use App\Models\Bodega;
use App\Models\User;
use Livewire\Component;

class BodegasCrud extends Component
{
    public $bodegas;
    public $bodega_id;
    public $nombre;
    public $tipo = 'lugar';
    public $ubicacion;
    public $descripcion;
    public $modal = false;

    // 🔹 NUEVO: usuarios disponibles y seleccionados
    public $usuariosDisponibles = [];
    public $usuariosSeleccionados = [];

    public function render()
    {
        $this->bodegas = Bodega::with('users')->get(); // incluye usuarios
        $this->usuariosDisponibles = User::orderBy('name')->get();
        return view('livewire.bodega.bodegas-crud');
    }

    public function openModal()
    {
        $this->resetInput();
        $this->modal = true;
    }

    public function closeModal()
    {
        $this->modal = false;
    }

    public function resetInput()
    {
        $this->bodega_id = null;
        $this->nombre = '';
        $this->tipo = 'lugar';
        $this->ubicacion = '';
        $this->descripcion = '';
        $this->usuariosSeleccionados = [];
    }

    public function save()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'tipo'   => 'required',
        ]);

        $bodega = Bodega::updateOrCreate(
            ['id' => $this->bodega_id],
            [
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'ubicacion' => $this->ubicacion,
                'descripcion' => $this->descripcion,
            ]
        );

        // 🔹 Guardar relación usuarios - bodegas
        $bodega->users()->sync($this->usuariosSeleccionados);

        $this->dispatch('hide-modals');
        $this->dispatch('notify', type: 'success', message: '¡Guardado con éxito!');

        $this->closeModal();
    }

    public function edit($id)
    {
        $bodega = Bodega::with('users')->findOrFail($id);
        $this->bodega_id = $bodega->id;
        $this->nombre = $bodega->nombre;
        $this->tipo = $bodega->tipo;
        $this->ubicacion = $bodega->ubicacion;
        $this->descripcion = $bodega->descripcion;
        $this->usuariosSeleccionados = $bodega->users->pluck('id')->toArray();
        $this->modal = true;
    }

    public function delete($id)
    {
        Bodega::find($id)?->delete();
        $this->dispatch('notify', type: 'success', message: 'Eliminado con éxito!');
    }
}
