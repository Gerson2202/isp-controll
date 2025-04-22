<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log; // Importar la clase Log
use App\Models\Cliente;
use App\Services\MikroTikService;
use Livewire\Component;
use App\Models\Pool;

class AsignarIpCliente extends Component
{
    public $cliente_id;
    public $ip;
    public $pool_id;
    public $cliente;
    public $contrato;
    public $plan;
    public $pools = [];
    public $availableIps = [];
    public $ipsUsadas = []; // IPs ya asignadas en el nodo

    protected $rules = [
        'ip' => 'required|ip|unique:clientes,ip',
        'pool_id' => 'required|exists:pools,id',
    ];

    public function mount($cliente_id)
    {
        $this->cliente_id = $cliente_id;
        $this->loadClienteData();
        $this->loadUsedIps();
    }

    public function loadClienteData()
    {
        $this->cliente = Cliente::with(['contratos.plan.nodo.pools'])->find($this->cliente_id);
        
        if ($this->cliente) {
            $this->contrato = $this->cliente->contratos->first();
            $this->plan = $this->contrato->plan ?? null;
            
            if ($this->plan && $this->plan->nodo) {
                $this->pools = $this->plan->nodo->pools()
                    ->orderBy('nombre')
                    ->get();
            }
        }
    }

    // Cargar IPs usadas por otros clientes del mismo nodo
    public function loadUsedIps()
    {
        if ($this->plan && $this->plan->nodo) {
            $this->ipsUsadas = Cliente::whereHas('contratos.plan', function($query) {
                    $query->where('nodo_id', $this->plan->nodo->id);
                })
                ->whereNotNull('ip')
                ->where('id', '!=', $this->cliente_id) // Excluir al cliente actual
                ->pluck('ip')
                ->toArray();
        }
    }

    public function updatedPoolId($value)
    {
        $this->reset('ip');
        
        if ($value) {
            $pool = Pool::find($value);
            $this->availableIps = $this->generateIpRange($pool->start_ip, $pool->end_ip);
        }
    }

    private function generateIpRange($startIp, $endIp)
    {
        $start = ip2long($startIp);
        $end = ip2long($endIp);
        $range = [];

        for ($ip = $start; $ip <= $end; $ip++) {
            $range[] = long2ip($ip);
        }

        return $range;
    }

    public function asignarIp()
    {
        $this->validate();

        // Validar que la IP no esté usada
        if (in_array($this->ip, $this->ipsUsadas)) {
            $this->addError('ip', 'Esta IP ya está en uso por otro cliente en el nodo.');
            return;
        }

        try {       
            
            // Obtener datos necesarios para MikroTik
            $clienteConRelaciones = $this->cliente->load('contrato.plan.nodo');
            
            // Llamar al método para crear cola hija
            $this->crearColaHija($clienteConRelaciones, $this->ip);
             
            // Guardar la IP en el cliente
            $this->cliente->update([
                'ip' => $this->ip,
                'pool_id' => $this->pool_id
            ]);

            session()->flash('success', 'IP asignada correctamente y cola hija configurada en MikroTik.');
            return redirect()->route('asignarIPindex');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error parece que no se tiene conexion con la mikrotik: ' . $e->getMessage());
        }
    }

    protected function crearColaHija($cliente, $ipAsignada)
    {
    
        try {
            // Verificar que existan todas las relaciones necesarias
            if (!$cliente->contrato || !$cliente->contrato->plan || !$cliente->contrato->plan->nodo) {
                throw new \Exception("Faltan datos necesarios para configurar MikroTik");
            }

            $plan = $cliente->contrato->plan;
            $nodo = $plan->nodo;

            // Crear instancia del servicio MikroTik
            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            // Llamar al método del servicio para crear cola hija
            $resultado = $mikroTikService->crearColaHija(
                $this->cliente->id,
                $ipAsignada,
                $plan->nombre,          // Nombre de la cola padre
                $plan->velocidad_subida,
                $plan->velocidad_bajada,
                $plan->rehuso ?? '1:1'  // Valor por defecto si no está especificado
            );

            // Opcional: Log del resultado
            \Log::info("Cola hija creada en MikroTik", [
                'cliente_id' => $cliente->id,
                'ip' => $ipAsignada,
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            \Log::error("Error al crear cola hija en MikroTik: " . $e->getMessage());
            throw $e; // Re-lanzamos la excepción para manejarla en el método principal
        }
    }

    public function render()
    {
        return view('livewire.asignar-ip-cliente');
    }
}