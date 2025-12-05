<?php

namespace App\Livewire;

use App\Models\Nodo;
use Livewire\Component;

class NodosIndex extends Component
{

    public $showModal = false;
    public $nodos;
    public $nombre, $ip, $latitud, $longitud, $puerto_api,$nodo_id,$pass,$user;
    public $successMessage = ''; // Propiedad para el mensaje de éxito

    public function mount()
    {
        $this->nodos = Nodo::all();
    }
    // Funcion oculatar modal
    public function hide()
    {
        $this->showModal = false;
        $this->resetForm();
    }

      // Mostrar el modal para actualizar
    public function editNodo($id)
    {
           $nodo = Nodo::find($id);
           $this->nodo_id = $nodo->id;
           $this->nombre = $nodo->nombre;
           $this->ip = $nodo->ip;
           $this->latitud = $nodo->latitud;
           $this->longitud = $nodo->longitud;
           $this->puerto_api = $nodo->puerto_api;
        //    $this->pass = $nodo->pass;
        //    $this->user = $nodo->user;
           $this->showModal = true;  
           $this->clearSuccessMessage();  // Limpiar cualquier mensaje anterior
    }

        // Actualizar Nodo
      public function updateNodo()
    {      
         $nodo = Nodo::find($this->nodo_id);
        // // Actualizar Nodo
         $nodo->update([
            'nombre' => $this->nombre,
            'ip' => $this->ip,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            // 'pass' => $this->pass,
            // 'user' => $this->user,
             'puerto_api' => $this->puerto_api,           
         ]);

        //  // Actualizar la lista de nodos
         $this->nodos = Nodo::all();
        // // Mostrar el mensaje de éxito
         $this->successMessage = 'Nodo actualizado exitosamente!';
         $this->resetForm();
        // // Cerrar el modal
        $this->showModal = false;

        // // Despachar evento para mostrar el mensaje en frontend
        //  $this->dispatch('show-success-message');
    }

       // Limpiar el mensaje de éxito
    public function clearSuccessMessage()
    {
        $this->successMessage = '';
    }

    // Función para Crear un nuevo NODO
    public function AgregarNodo()
    {       
       // Crear un nuevo nodo
        Nodo::create([
            'nombre' => $this->nombre,
            'ip' => $this->ip,
            'latitud' => $this->latitud,
            'longitud' => $this->longitud,
            //'puerto_api' => $this->puerto_api,
        ]);

        // Actualizar la lista de planes
        $this->nodos = Nodo::all();

        // Mostrar el mensaje de éxito
        $this->successMessage = 'Nodo Creado exitosamente!';

        // Vaciar los campos del formulario después de guardar
        $this->resetForm();
        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');
    }

    public function resetForm()
    {
        $this->nombre = '';
        $this->ip = '';
        $this->latitud = '';
        $this->longitud = '';
        $this->puerto_api = '';
        // $this->user = '';
        // $this->pass = '';
    }
    public function render()
    {
        return view('livewire.nodos-index');
    }
}
