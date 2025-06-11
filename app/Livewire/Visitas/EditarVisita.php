<?php

namespace App\Livewire\Visitas;

use App\Models\VisitaFoto;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\VisitasFoto;
use Illuminate\Support\Facades\Storage;

class EditarVisita extends Component
{
    use WithFileUploads;

    public $visita;
    public $fotos = [];
    public $descripcion = [];

    protected $rules = [
        'fotos.*' => 'required|image|max:2048',
        'descripcion.*' => 'nullable|string|max:255',
    ];

    public function save()
    {
        $this->validate();

        foreach ($this->fotos as $index => $foto) {
            $ruta = $foto->store('visitas_fotos', 'public');
            VisitaFoto::create([
                'visita_id' => $this->visita->id,
                'ruta' => $ruta,
                'nombre_original' => $foto->getClientOriginalName(),
                'descripcion' => $this->descripcion[$index] ?? null,
            ]);
        }

        $this->reset(['fotos', 'descripcion']);
        session()->flash('success', '¡Imágenes guardadas correctamente!');
    }

    public function eliminarFoto($fotoId)
    {
        $foto = VisitaFoto::find($fotoId);
        if ($foto) {
            Storage::disk('public')->delete($foto->ruta);
            $foto->delete();
            session()->flash('success', 'Imagen eliminada correctamente.');
        }
    }

    public function render()
    {
        $fotosGuardadas = VisitaFoto::where('visita_id', $this->visita->id)->get();
        return view('livewire.visitas.editar-visita', [
            'fotosGuardadas' => $fotosGuardadas,
        ]);
    }
}