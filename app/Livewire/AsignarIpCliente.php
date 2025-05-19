<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Log; // Importar la clase Log
use App\Models\Cliente;
use App\Services\MikroTikService;
use Livewire\Component;
use App\Models\pool;
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

        DB::beginTransaction();

        try {
            // Obtener datos necesarios para MikroTik
            $clienteConRelaciones = $this->cliente->load('contrato.plan.nodo');
            
            // Llamar al método para crear cola hija
            $this->crearColaHija($clienteConRelaciones, $this->ip);
            
            // Actualizaciones atómicas
            $this->cliente->update([
                'ip' => $this->ip,
                'pool_id' => $this->pool_id
            ]);

            // Actualizar estado del contrato si existe
            if ($this->cliente->contrato) {
                $this->cliente->contrato->update([
                    'estado' => 'Activo',
                    'fecha_activacion' => now() // Opcional: registrar fecha de activación
                ]);
            }

            DB::commit();

            session()->flash('success', 'IP asignada correctamente y cola hija configurada en MikroTik.');
            return redirect()->route('asignarIPindex');
            
        } catch (\Throwable $e) {
            DB::rollBack();
            
            // Registro detallado del error
            logger()->error('Error en asignarIp: ' . $e->getMessage(), [
                'cliente_id' => $this->cliente->id ?? null,
                'ip' => $this->ip,
                'trace' => $e->getTraceAsString()
            ]);

            $mensajeError = 'Ocurrió un error: ' . $e->getMessage();
            
            // Mensaje más específico para errores de MikroTik
            if (str_contains($e->getMessage(), 'MikroTik')) {
                $mensajeError = 'Error de conexión con MikroTik: ' . $e->getMessage();
            }

            session()->flash('error', $mensajeError);
            return back();
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
            Log::info("Cola hija creada en MikroTik", [
                'cliente_id' => $cliente->id,
                'ip' => $ipAsignada,
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            Log::error("Error al crear cola hija en MikroTik: " . $e->getMessage());
            throw $e; // Re-lanzamos la excepción para manejarla en el método principal
        }
    }

    public function render()
    {
        return view('livewire.asignar-ip-cliente');
    }
}