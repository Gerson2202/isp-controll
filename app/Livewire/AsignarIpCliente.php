<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Nodo;
use App\Models\Plan;
use Livewire\Component;

class AsignarIpCliente extends Component
{

    public $cliente_id;
    public $nodo;
    public $ip;
    public $cliente;
    public $contrato;
    public $plan;
    public $nodos; // Propiedad pública para los nodos
    
    public function mount($cliente_id)
    {
        $this->cliente_id = $cliente_id; // Recibe el cliente_id
        $this->loadClienteData(); // Carga los datos del cliente, contrato y plan
        $this->nodos = Nodo::all(); // Carga todos los nodos disponibles
    }
    // Método que se ejecuta cuando se selecciona el cliente
    public function loadClienteData()
    {
        // Obtener el contrato y el plan asociado al cliente
        $this->cliente = Cliente::find($this->cliente_id);
        $this->contrato = Contrato::where('cliente_id', $this->cliente_id)->first();
        if ($this->contrato) {
            $this->plan = Plan::find($this->contrato->plan_id);
        }
    }

    public function asignarIp()
    {
        $this->validate([
            'ip' => 'required|ip|unique:clientes,ip', // Asegura que la IP sea única en la tabla clientes
        ]);

        $cliente = Cliente::findOrFail($this->cliente_id);
        $cliente->update(['ip' => $this->ip]);

        $this->reset('ip');
        session()->flash('message', 'IP asignada correctamente.');
        return redirect()->route('asignarIPindex');

    }
    // Método para guardar la IP asignada
    // public function asignarIp()
    // {
        
    //     // Validar y guardar la IP (puedes agregar más validaciones si es necesario)
    //     // Aquí, puedes integrar la lógica para asignar la IP al cliente en tu Mikrotik o en la base de datos.
        
    //     // Ejemplo de validación
    //     $this->validate([
    //         'ip' => 'required|ip',
    //     ]);
    //        // Crear el ticket
    //     Cliente::update([
    //         'ip' => $this->ip,
    //     ]);
    //     // Lógica de asignación aquí (por ejemplo, guardar en la base de datos o en Mikrotik)

    //    // Redirigir o mostrar un mensaje de éxito
    //    session()->flash('message', 'Ip  asignado correctamente.');
    //    return redirect()->route('asignarIPindex');
    // }

    public function render()
    {
        return view('livewire.asignar-ip-cliente');
    }
}
