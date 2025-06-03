<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClienteCortes extends Component
{
    use WithPagination;

    public $search = '';
    public $filterEstado = '';
    public $filterMikrotik = '';
    public $estadoAnterior = '';
    public $perPage = 10;

    // Progreso y corte masivo
    public $procesandoCorteMasivo = false;
    public $clientesProcesados = 0;
    public $totalClientes = 0;
    public $idsPendientes = [];
    public $chunkSize = 1; // Uno por uno para ver el contador avanzar

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterMikrotik' => ['except' => '']
    ];

    // Iniciar el proceso de corte masivo (prepara la lista)
    public function iniciarCorteMasivo()
    {
        $this->procesandoCorteMasivo = true;
        $this->clientesProcesados = 0;

        $facturasPendientes = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])
            ->whereMonth('fecha_emision', Carbon::now()->month)
            ->whereYear('fecha_emision', Carbon::now()->year)
            ->where('estado', 'pendiente')
            ->whereHas('contrato.cliente', function($q) {
                $q->where('estado', 'activo')
                  ->whereNotNull('ip');
            })
            ->pluck('id')
            ->toArray();

        $this->idsPendientes = $facturasPendientes;
        $this->totalClientes = count($facturasPendientes);
    }

    // Procesa un cliente por vez
    public function procesarChunk()
    {
        if (!$this->procesandoCorteMasivo) return;

        $chunk = array_splice($this->idsPendientes, 0, $this->chunkSize);

        foreach ($chunk as $facturaId) {
            try {
                DB::transaction(function() use ($facturaId) {
                    $factura = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])->find($facturaId);
                    if (!$factura) return;
                    $cliente = $factura->contrato->cliente;

                    // 1. Actualizar estado del cliente
                    $cliente->update(['estado' => 'cortado']);

                    // 2. Crear ticket
                    Ticket::create([
                        'tipo_reporte' => 'corte masivo',
                        'situacion' => 'Corte automatico por factura pendiente',
                        'estado' => 'cerrado',
                        'fecha_cierre' => now(),
                        'cliente_id' => $cliente->id,
                        'solucion' => 'Corte realizado automáticamente por sistema'
                    ]);

                    // 3. Actualizar MikroTik
                    if ($nodo = $factura->contrato->plan->nodo) {
                        $mikroTikService = new MikroTikService(
                            $nodo->ip,
                            $nodo->user,
                            $nodo->pass,
                            $nodo->puerto_api ?? 8728
                        );
                        if ($mikroTikService->isReachable()) {
                            $mikroTikService->manejarEstadoCliente($cliente->ip, 'cortado');
                        }
                    }
                });
                $this->clientesProcesados++;
            } catch (\Exception $e) {
                Log::error("Error cortando cliente ID {$facturaId}: " . $e->getMessage());
                continue;
            }
        }

        if (empty($this->idsPendientes)) {
            $this->procesandoCorteMasivo = false;
            $this->dispatch('notify',
                type: 'success',
                message: "Corte masivo completado: {$this->clientesProcesados} de {$this->totalClientes} clientes procesados"
            );
        }
    }

    public function cambiarEstado($clienteId)
    {
        $cliente = Cliente::with(['contrato.plan.nodo'])->findOrFail($clienteId);
        $this->estadoAnterior = $cliente->estado;
        $nuevoEstado = $cliente->estado == 'activo' ? 'cortado' : 'activo';

        // Validar que el cliente tenga IP asignada
        if (empty($cliente->ip)) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Error al actualizar el estado cliente sin ip',
            );
            return;
        }

        DB::beginTransaction();

        try {
            $cliente->update(['estado' => $nuevoEstado]);
            $situacionTexto = "Se actualizo estado del cliente de {$this->estadoAnterior} a {$nuevoEstado}. Actualizado por el usuario: " . auth()->user()->name;

            Ticket::create([
                'tipo_reporte' => 'cambio de estado',
                'situacion' => $situacionTexto,
                'estado' => 'cerrado',
                'fecha_cierre' => now(),
                'cliente_id' => $cliente->id,
                'solucion' => 'Estado actualizado correctamente desde el panel de cortes',
            ]);

            $nodo = $cliente->contrato->plan->nodo;

            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            if (!$mikroTikService->isReachable()) {
                throw new \Exception("No se pudo conectar al router MikroTik del nodo.");
            }

            $mikroTikService->manejarEstadoCliente($cliente->ip, $nuevoEstado);

            DB::commit();

            $this->dispatch('notify',
                type: 'success',
                message: 'Estado actualizado correctamente en el sistema y en el router MikroTik.'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar estado del cliente ID ' . $cliente->id . ': ' . $e->getMessage());

            $this->dispatch('notify',
                type: 'error',
                message: 'Error al actualizar el estado: ' . $e->getMessage()
            );
        }
    }

    public function aplicarFiltros()
    {
        $this->resetPage();
    }

    public function render()
    {
        $fechaActual = Carbon::now();
        $mesActual = $fechaActual->month;
        $anioActual = $fechaActual->year;

        $query = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])
            ->whereMonth('fecha_emision', $mesActual)
            ->whereYear('fecha_emision', $anioActual);

        if (!empty($this->search)) {
            $query->whereHas('contrato.cliente', function($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->filterEstado)) {
            $query->where('estado', $this->filterEstado);
        }

        if (!empty($this->filterMikrotik)) {
            $query->whereHas('contrato.cliente', function($q) {
                $q->where('estado', $this->filterMikrotik);
            });
        }

        $facturas = $query->orderBy('fecha_vencimiento', 'asc')->get();

        return view('livewire.clientes.cliente-cortes', [
            'facturas' => $facturas
        ]);
    }
}