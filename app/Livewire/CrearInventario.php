<?php

namespace App\Livewire;
use App\Models\Inventario;
use Livewire\WithFileUploads;

use Livewire\Component;

class CrearInventario extends Component
{
    use WithFileUploads; // Para manejar la carga de archivos

    public $modelo;
    public $mac;
    public $descripcion;
    public $foto;

    protected $rules = [
        'modelo' => 'required|string|max:255',
        'mac' => 'required|string|max:255|unique:inventarios,mac',
        'descripcion' => 'required|string',
        'foto' => 'nullable|image|max:1024', // Asegura que la foto sea una imagen
    ];

    public function guardar()
    {
        $this->validate();

        $fotoPath = null;

        if ($this->foto) {
            $fotoPath = $this->foto->store('fotos_inventarios', 'public'); // Guarda la imagen
        }

        // Crear un nuevo registro en la tabla inventarios
        $inventario = Inventario::create([
            'modelo' => $this->modelo,
            'mac' => $this->mac,
            'descripcion' => $this->descripcion,
            'foto' => $fotoPath,
        ]);

        // Redirigir al detalle del equipo con un mensaje de éxito
        session()->flash('message', 'Inventario creado con éxito');
        return redirect()->route('equipos.show', $inventario->id);
    }
    public function render()
    {
        return view('livewire.crear-inventario');
    }
}
