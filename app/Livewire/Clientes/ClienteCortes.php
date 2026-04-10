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
            // 🔥 Subconsulta: última factura por contrato (más segura por ID)
            $sub = Factura::selectRaw('MAX(id) as max_id')
                ->groupBy('contrato_id');

            $facturasPendientes = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])
                ->joinSub($sub, 'ultimas', function ($join) {
                    $join->on('facturas.id', '=', 'ultimas.max_id');
                })
                ->where('facturas.estado', 'pendiente')

                // 🔥 NUEVA CONDICIÓN CLAVE
                ->whereDate('facturas.fecha_vencimiento', '<', now())
                // ✅ CONTRATO ACTIVO
                ->whereHas('contrato', function ($q) {
                    $q->where('estado', 'activo');
                })

                // ✅ CLIENTE ACTIVO + CON IP
                ->whereHas('contrato.cliente', function ($q) {
                    $q->where('estado', 'activo')
                        ->whereNotNull('ip');
                })

                ->get();

            foreach ($facturasPendientes as $factura) {
                DB::transaction(function () use ($factura) {

                    $cliente = $factura->contrato->cliente;

                    // 🔒 Evitar reprocesar si ya está cortado
                    if ($cliente->estado === 'cortado') {
                        return;
                    }

                    // 🔻 Cambiar estado del cliente
                    $cliente->update(['estado' => 'cortado']);

                    // 🧾 Crear ticket
                    Ticket::create([
                        'user_id' => auth()->id(),
                        'tipo_reporte' => 'corte masivo',
                        'situacion' => 'Corte automatico por factura pendiente',
                        'estado' => 'cerrado',
                        'fecha_cierre' => now(),
                        'cliente_id' => $cliente->id,
                        'solucion' => 'Corte realizado automaticamente por sistema'
                    ]);

                    // 📡 Corte en MikroTik
                    if ($nodo = optional($factura->contrato->plan)->nodo) {

                        $mikroTikService = new MikroTikService(
                            $nodo->ip,
                            $nodo->user,
                            $nodo->pass,
                            $nodo->puerto_api ?? 8728
                        );

                        if ($mikroTikService->isReachable()) {
                            $mikroTikService->manejarEstadoCliente($cliente->ip, 'cortado');
                        } else {
                            Log::warning("Nodo no alcanzable para cliente ID: {$cliente->id}");
                        }
                    } else {
                        Log::warning("Cliente sin nodo asignado ID: {$cliente->id}");
                    }
                });
            }

            $this->dispatch(
                'notify',
                type: 'success',
                message: "Corte masivo completado: " . count($facturasPendientes) . " clientes procesados"
            );
        } catch (\Exception $e) {
            Log::error("Error en corte masivo: " . $e->getMessage());

            $this->dispatch(
                'notify',
                type: 'error',
                message: "Ocurrió un error durante el proceso: " . $e->getMessage()
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
            $this->dispatch(
                'notify',
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

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Estado actualizado correctamente'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar estado: ' . $e->getMessage());

            $this->dispatch(
                'notify',
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
        // 🔥 Usar el mismo criterio que el corte (por ID)
        $sub = Factura::selectRaw('MAX(id) as max_id')
            ->groupBy('contrato_id');

        $query = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])
            ->joinSub($sub, 'ultimas', function ($join) {
                $join->on('facturas.id', '=', 'ultimas.max_id');
            })

            ->whereHas('contrato.cliente') // evita nulls
            ->whereHas('contrato', function ($q) {
                $q->where('estado', 'activo'); // 🔥 filtro contrato activo
            });

        // 🔍 BUSCADOR
        if (!empty($this->search)) {
            $query->whereHas('contrato.cliente', function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%');
            });
        }

        // 📌 FILTRO ESTADO FACTURA
        if (!empty($this->filterEstado)) {
            $query->where('facturas.estado', $this->filterEstado);
        }

        // 📡 FILTRO MIKROTIK (estado cliente)
        if (!empty($this->filterMikrotik)) {
            $query->whereHas('contrato.cliente', function ($q) {
                $q->where('estado', $this->filterMikrotik);
            });
        }

        $facturas = $query
            ->orderBy('facturas.fecha_emision', 'desc')
            ->get();

        return view('livewire.clientes.cliente-cortes', [
            'facturas' => $facturas
        ]);
    }
}
