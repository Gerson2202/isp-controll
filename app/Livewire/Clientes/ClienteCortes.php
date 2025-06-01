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
use Illuminate\Support\Facades\Session;

class ClienteCortes extends Component
{
   
    use WithPagination;

    public $search = '';
    public $filterEstado = '';
    public $filterMikrotik = '';
    public $estadoAnterior = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterEstado' => ['except' => ''],
        'filterMikrotik' => ['except' => '']
    ];

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
        }

        // Iniciar transacción
        DB::beginTransaction();

        try {
            // 1. Actualizar estado en la base de datos
            $cliente->update(['estado' => $nuevoEstado]);
            
            // 2. Crear ticket de reporte
            $situacionTexto = "Se actualizó estado del cliente de {$this->estadoAnterior} a {$nuevoEstado}. Actualizado por el usuario: " . auth()->user()->name;
            
            Ticket::create([
                'tipo_reporte' => 'cambio de estado',
                'situacion' => $situacionTexto,
                'estado' => 'cerrado',
                'fecha_cierre' => now(), 
                'cliente_id' => $cliente->id,
                'solucion' => 'Estado actualizado correctamente desde el panel de cortes',
            ]);

            // 3. Actualizar en MikroTik
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

            // Ejecutar cambio en MikroTik
            $mikroTikService->manejarEstadoCliente($cliente->ip, $nuevoEstado);

            // Confirmar transacción si todo fue exitoso
            DB::commit();

            // Notificaciones existentes (sin cambios)
            $this->dispatch('notify', 
                type: 'success', 
                message: 'Estado actualizado correctamente en el sistema y en el router MikroTik.'
            );

        } catch (\Exception $e) {
            // Revertir cambios en caso de error
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
        // Obtener el mes y año actual
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
