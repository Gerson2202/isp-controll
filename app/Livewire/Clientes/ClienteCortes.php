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
    public $procesando = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterMikrotik' => ['except' => '']
    ];

    public function iniciarCorteMasivo()
    {
        $this->procesando = true;

        try {
            $facturasPendientes = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])
                ->whereMonth('fecha_emision', Carbon::now()->month)
                ->whereYear('fecha_emision', Carbon::now()->year)
                ->where('estado', 'pendiente')
                ->whereHas('contrato.cliente', function($q) {
                    $q->where('estado', 'activo')
                      ->whereNotNull('ip');
                })
                ->get();

            foreach ($facturasPendientes as $factura) {
                DB::transaction(function() use ($factura) {
                    $cliente = $factura->contrato->cliente;
                    $cliente->update(['estado' => 'cortado']);

                    Ticket::create([
                        'user_id' => auth()->id(), // O el ID del usuario correspondiente
                        'tipo_reporte' => 'corte masivo',
                        'situacion' => 'Corte automatico por factura pendiente',
                        'estado' => 'cerrado',
                        'fecha_cierre' => now(),
                        'cliente_id' => $cliente->id,
                        'solucion' => 'Corte realizado automaticamente por sistema'
                    ]);

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
            }

            $this->dispatch('notify',
                type: 'success',
                message: "Corte masivo completado: " . count($facturasPendientes) . " clientes procesados"
            );
        } catch (\Exception $e) {
            Log::error("Error en corte masivo: " . $e->getMessage());
            $this->dispatch('notify',
                type: 'error',
                message: "Ocurrio un error durante el proceso: " . $e->getMessage()
            );
        } finally {
            $this->procesando = false;
        }
    }

    public function cambiarEstado($clienteId)
    {
        $cliente = Cliente::with(['contrato.plan.nodo'])->findOrFail($clienteId);
        $this->estadoAnterior = $cliente->estado;
        $nuevoEstado = $cliente->estado == 'activo' ? 'cortado' : 'activo';

        if (empty($cliente->ip)) {
            $this->dispatch('notify',
                type: 'error',
                message: 'Error: El cliente no tiene IP asignada',
            );
            return;
        }

        DB::beginTransaction();

        try {
            $cliente->update(['estado' => $nuevoEstado]);
            $situacionTexto = "Estado cambiado de {$this->estadoAnterior} a {$nuevoEstado}. Usuario: " . auth()->user()->name;

            Ticket::create([
                'user_id' => auth()->id(), // O el ID del usuario correspondiente
                'tipo_reporte' => 'cambio de estado',
                'situacion' => $situacionTexto,
                'estado' => 'cerrado',
                'fecha_cierre' => now(),
                'cliente_id' => $cliente->id,
                'solucion' => 'Estado actualizado desde panel de cortes',
            ]);

            $nodo = $cliente->contrato->plan->nodo;

            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            if (!$mikroTikService->isReachable()) {
                throw new \Exception("No se pudo conectar al MikroTik");
            }

            $mikroTikService->manejarEstadoCliente($cliente->ip, $nuevoEstado);

            DB::commit();

            $this->dispatch('notify',
                type: 'success',
                message: 'Estado actualizado correctamente'
            );

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar estado: ' . $e->getMessage());

            $this->dispatch('notify',
                type: 'error',
                message: 'Error: ' . $e->getMessage()
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