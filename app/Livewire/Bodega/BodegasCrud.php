<?php

namespace App\Livewire\Bodega;

use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Nodo;
use App\Models\User;
use GuzzleHttp\Client;
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

    public function render()
    {
        $this->bodegas = Bodega::all();
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
    }

    public function save()
    {
            $this->validate(
                [
                    'nombre' => 'required|string|max:255',
                    'tipo'   => 'required',
                ],
                [
                    'nombre.required' => 'El nombre de la bodega es obligatorio.',
                    'nombre.string'   => 'El nombre debe ser un texto válido.',
                    'nombre.max'      => 'El nombre no puede tener más de 255 caracteres.',
                    'tipo.required'   => 'Debe seleccionar un tipo de bodega.',
                    // Si quieres, puedes agregar más reglas y mensajes aquí
                ]
            );


        Bodega::updateOrCreate(
            ['id' => $this->bodega_id],
            [
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'ubicacion' => $this->ubicacion,
                'descripcion' => $this->descripcion,
            ]
        );

        $this->dispatch('hide-modals');
            $this->dispatch('notify', 
            type: 'success',
            message: '¡Guardado con exito!'
        ); 

        $this->closeModal();
    }

    public function edit($id)
    {
        $bodega = Bodega::findOrFail($id);
        $this->bodega_id = $bodega->id;
        $this->nombre = $bodega->nombre;
        $this->tipo = $bodega->tipo;
        $this->ubicacion = $bodega->ubicacion;
        $this->descripcion = $bodega->descripcion;
        $this->modal = true;
    }

    public function delete($id)
    {
        Bodega::find($id)?->delete();
        $this->dispatch('hide-modals');
            $this->dispatch('notify', 
            type: 'success',
            message: 'Eliminado con exito!'
        );
    }
}
