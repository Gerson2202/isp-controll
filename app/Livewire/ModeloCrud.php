<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Modelo; // Asegúrate de que el modelo Modelo esté creado
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File; // Importa la clase File de Laravel correctamente

class ModeloCrud extends Component
{
    use WithFileUploads; // Permite la carga de archivos

    public $modelos, $nombre, $foto, $modelo_id;
    public $isEdit = false;

    
    // Validación de campos
    protected $rules = [
        'nombre' => 'required|string|max:255',
        'foto' => 'nullable|image|max:1024', // Foto es opcional pero si se carga, debe ser imagen
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
                'foto' => $this->saveFoto(),
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

        $this->resetInputFields(); // Limpiar los campos después de guardar
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
                message: 'Modelo creado exitosamente'
            );
    }

    // Para cargar la imagen y obtener el path
    private function saveFoto()
    {
        if ($this->foto) {
            return $this->foto->store('modelos', 'public'); // Guarda en la carpeta 'modelos'
        }

        return null;
    }

    // Rellenar los campos cuando se edita
    public function edit($id)
    {
        $modelo = Modelo::find($id);
        $this->modelo_id = $modelo->id;
        $this->nombre = $modelo->nombre;
        $this->foto = null; // Reseteamos la foto ya que no la necesitamos en la edición
        $this->isEdit = true;
    }

    // Resetear los campos
    public function resetInputFields()
    {
        $this->nombre = '';
        $this->foto = null;
        $this->modelo_id = null;
        $this->isEdit = false;
    }

    public function render()
    {
        // Obtenemos todos los modelos
        $this->modelos = Modelo::all();
        return view('livewire.modelo-crud');
    }
}
