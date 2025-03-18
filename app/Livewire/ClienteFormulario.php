<?php

namespace App\Livewire;

use App\Models\Cliente;
use Livewire\Component;

class ClienteFormulario extends Component
{
   
    // Definimos las propiedades del formulario
    public $nombre;
    public $telefono;
    public $direccion;
    public $email;
    public $cedula;
    public $estado = 'activo'; // Valor predeterminado
    public $successMessage = ''; // Propiedad para el mensaje de éxito

   

    // Método para guardar los datos
    public function save()
    {
        
        // Creamos un nuevo cliente en la base de datos
        Cliente::create([
            'nombre' => $this->nombre,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'email' => $this->email,
            'cedula' => $this->cedula,
            'estado' => $this->estado,
        ]);

        // Limpiamos los campos del formulario después de guardar
        $this->reset();

        $this->successMessage = 'Cliente Creado exitosamente!';

        // Despachar evento para mostrar el mensaje en frontend
        $this->dispatch('show-success-message');
    }

    public function render()
    {
        return view('livewire.cliente-formulario');
    }
}
