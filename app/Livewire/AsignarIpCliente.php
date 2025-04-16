<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Plan;
use App\Models\Pool;
use App\Models\Nodo;
use Livewire\Component;

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
            $this->cliente->update([
                'ip' => $this->ip,
                'pool_id' => $this->pool_id
            ]);
            
            session()->flash('success', 'IP asignado correctamente.');
            return redirect()->route('asignarIPindex');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Ocurrió un error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.asignar-ip-cliente');
    }
}