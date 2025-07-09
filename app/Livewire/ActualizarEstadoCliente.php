<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ActualizarEstadoCliente extends Component
{
    public $cliente;
    public $estado;
    public $estadoAnterior;

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente;
        $this->estado = $cliente->estado; // Cargar el estado actual
        $this->estadoAnterior= $this->cliente->estado;
    }

    public function actualizarEstado()
    {
        $this->validate([
            'estado' => 'required|in:activo,suspendido,cortado',
        ]);

        // Validar que el cliente tenga IP asignada
        if (empty($this->cliente->ip)) {
            Session::flash('error', 'No se puede actualizar el estado: El cliente no tiene una IP asignada.');
            return redirect()->route('clientes.show', $this->cliente->id);
        }

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // 1. Actualizar estado en la base de datos
            $this->cliente->update(['estado' => $this->estado]);
            // Creamos ticket de reporte de modificacion de plan            
            $situacionTexto = "Se actualizo estado del cliente de  {$this->estadoAnterior} a {$this->estado} . Actualizado por el usuario: " . auth()->user()->name;
            Ticket::create([
                'user_id' => auth()->id(), // O el ID del usuario correspondiente
                'tipo_reporte' => 'cambio de estado',
                'situacion' => $situacionTexto,
                'estado' => 'cerrado',
                'fecha_cierre' => now(), 
                'cliente_id' => $this->cliente->id,
                'solucion' => 'Estado actualizado correctamente desde el panel',
            ]);
            $plan = $this->cliente->contrato->plan;
            $nodo = $plan->nodo;
            
            // 2. Actualizar en MikroTik (solo si tiene IP)
            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            if (!$mikroTikService->isReachable()) {
                throw new \Exception("No se pudo conectar al router MikroTik del nodo.");
            }

            // Ejecutar cambio en MikroTik
            $mikroTikService->manejarEstadoCliente($this->cliente->ip, $this->estado);

            // Confirmar transacción si todo fue exitoso
            DB::commit();

            Session::flash('success', 'Estado actualizado correctamente en el sistema y en el router MikroTik.');
            return redirect()->route('clientes.show', $this->cliente->id);

        } catch (\Exception $e) {
            // Revertir cambios en caso de error
            DB::rollBack();

            Log::error('Error al actualizar estado del cliente ID ' . $this->cliente->id . ': ' . $e->getMessage());

            Session::flash('error', 'Error al actualizar el estado: ' . $e->getMessage());
            return redirect()->route('clientes.show', $this->cliente->id);
        }
    }
    public function render()
    {
        return view('livewire.actualizar-estado-cliente');
    }
}
