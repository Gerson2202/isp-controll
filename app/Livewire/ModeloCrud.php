<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Modelo;
use Illuminate\Support\Facades\File;
use Livewire\WithPagination;

class ModeloCrud extends Component
{
    use WithFileUploads, WithPagination;

    public $nombre, $foto, $modelo_id;
    public $isEdit = false;
    public $search = '';
    public $uploading = false;

    // Validación de campos
    protected $rules = [
        'nombre' => 'required|string|max:255',
        'foto' => 'nullable|image|max:1024',
    ];

    // Guardar o actualizar el modelo
    public function save()
    {
        $this->validate();

        // Si estamos editando, actualizamos el registro
        if ($this->isEdit) {
            $modelo = Modelo::find($this->modelo_id);
            $modelo->update([
                'nombre' => $this->nombre,
                'foto' => $this->saveFoto($modelo->foto),
            ]);
            $this->dispatch('notify', 
                type: 'success',
                message: 'Modelo actualizado exitosamente'
            );
        } else {
            // Crear un nuevo modelo
            Modelo::create([
                'nombre' => $this->nombre,
                'foto' => $this->saveFoto(),
            ]);
            $this->dispatch('notify', 
                type: 'success',
                message: 'Modelo creado exitosamente'
            );
        }

        $this->resetInputFields();
    }

    // Eliminar un modelo
    public function delete($id)
    {
        $modelo = Modelo::find($id);
        if ($modelo->foto && File::exists(storage_path('app/public/' . $modelo->foto))) {
            File::delete(storage_path('app/public/' . $modelo->foto));
        }
        $modelo->delete();

        $this->dispatch('notify', 
            type: 'error',
            message: 'Modelo eliminado exitosamente'
        );
    }

    // Para cargar la imagen y obtener el path
    private function saveFoto($currentFoto = null)
    {
        if ($this->foto) {
            // Eliminar foto anterior si existe
            if ($currentFoto && File::exists(storage_path('app/public/' . $currentFoto))) {
                File::delete(storage_path('app/public/' . $currentFoto));
            }
            return $this->foto->store('modelos', 'public');
        }

        return $currentFoto;
    }

    // Rellenar los campos cuando se edita
    public function edit($id)
    {
        $modelo = Modelo::find($id);
        $this->modelo_id = $modelo->id;
        $this->nombre = $modelo->nombre;
        $this->foto = null;
        $this->isEdit = true;
    }

    // Resetear los campos
    public function resetInputFields()
    {
        $this->nombre = '';
        $this->foto = null;
        $this->modelo_id = null;
        $this->isEdit = false;
        $this->uploading = false;
    }

    // Resetear búsqueda
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Modelo::query();

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        $modelos = $query->orderBy('nombre')->paginate(10);

        return view('livewire.modelo-crud', compact('modelos'));
    }
}