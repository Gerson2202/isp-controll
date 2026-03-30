<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log; // Importar la clase Log
use App\Models\Cliente;
use App\Services\MikroTikService;
use Livewire\Component;
use App\Models\pool;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB; // Agrega esta línea

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
            $this->ipsUsadas = Cliente::whereHas('contratos.plan', function ($query) {
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

        DB::beginTransaction();

        try {
            // Obtener relaciones necesarias
            $clienteConRelaciones = $this->cliente->load('contrato.plan.nodo');

            // 🔥 1. Crear cola + activar cliente (TODO EN UNO)
            $this->crearColaYActivar($clienteConRelaciones, $this->ip);

            // 🔥 2. Guardar IP en cliente
            $this->cliente->update([
                'ip' => $this->ip,
                'pool_id' => $this->pool_id,
                'estado' => 'activo'
            ]);

            // 🔥 3. Actualizar contrato
            if ($this->cliente->contrato) {
                $this->cliente->contrato->update([
                    'estado' => 'activo',
                    'fecha_activacion' => now()
                ]);
            }

            // 🔥 4. CREAR TICKET
            Ticket::create([
                'tipo_reporte' => 'Activacion de servicio',
                'situacion' => "IP {$this->ip} asignada y servicio activado correctamente",
                'fecha_cierre' => now(),
                'solucion' => 'Configuracion aplicada en MikroTik',
                'estado' => 'cerrado',
                'cliente_id' => $this->cliente->id,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            session()->flash(
                'success',
                'La IP fue asignada, el cliente fue activado y la cola se creó correctamente.'
            );

            return redirect()->route('asignarIPindex');
        } catch (\Throwable $e) {

            DB::rollBack();

            logger()->error('Error en asignarIp: ' . $e->getMessage(), [
                'cliente_id' => $this->cliente->id ?? null,
                'ip' => $this->ip,
                'trace' => $e->getTraceAsString()
            ]);

            $mensajeError = 'Error al crear la restriccion: ' . $e->getMessage();

            if (str_contains($e->getMessage(), 'MikroTik')) {
                $mensajeError = 'Error de conexion con MikroTik: ' . $e->getMessage();
            }

            session()->flash('error', $mensajeError);

            return back();
        }
    }

    protected function crearColaYActivar($cliente, $ipAsignada)
    {
        try {
            // Validar relaciones
            if (!$cliente->contrato || !$cliente->contrato->plan || !$cliente->contrato->plan->nodo) {
                throw new \Exception("Faltan datos necesarios para configurar MikroTik");
            }

            $plan = $cliente->contrato->plan;
            $nodo = $plan->nodo;

            // 🔥 Instancia del service
            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            // 🔥 1. Crear cola
            $resultadoCola = $mikroTikService->crearColaHija(
                $cliente,
                $plan,
                $ipAsignada
            );

            // 🔥 2. Activar cliente
            $mikroTikService->activarCliente($ipAsignada);

            // 🧾 Log completo
            Log::info("Cola creada y cliente activado en MikroTik", [
                'cliente_id' => $cliente->id,
                'ip' => $ipAsignada,
                'plan' => $plan->nombre,
                'resultado' => $resultadoCola
            ]);
        } catch (\Exception $e) {

            Log::error("Error en MikroTik (cola + activacion): " . $e->getMessage(), [
                'cliente_id' => $cliente->id ?? null,
                'ip' => $ipAsignada
            ]);

            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.asignar-ip-cliente');
    }
}
