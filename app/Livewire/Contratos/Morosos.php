<?php

namespace App\Livewire\Contratos;

use App\Services\MikroTikService;
use Livewire\Component;
use App\Models\Cliente;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Morosos extends Component
{
    public $search = '';

    public function darDeBaja($clienteId)
    {
        DB::beginTransaction();

        try {

            $cliente = Cliente::with(['contratos.plan.nodo', 'contratos.facturas'])
                ->lockForUpdate()
                ->find($clienteId);

            if (!$cliente) {
                throw new \Exception('Cliente no encontrado.');
            }

            $ipAsignada = $cliente->ip;
            $ipDisplay = $ipAsignada ?? 'N/A';

            // 🔎 Obtener nodo y plan activo
            $nodo = null;
            $nombrePlan = null;

            foreach ($cliente->contratos as $contrato) {
                if (
                    $contrato->estado === 'activo' &&
                    $contrato->plan &&
                    $contrato->plan->nodo
                ) {
                    $nodo = $contrato->plan->nodo;
                    $nombrePlan = $contrato->plan->nombre;
                    break;
                }
            }

            if (!$nodo) {
                throw new \Exception('No se pudo determinar el nodo del cliente.');
            }

            if (!$ipAsignada || !$nombrePlan) {
                throw new \Exception('No se pudo determinar IP o plan del cliente.');
            }

            // =====================================================
            // 🔥 1️⃣ PRIMERO: PROCESAR EN MIKROTIK
            // =====================================================

            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            if (!$mikroTikService->isReachable()) {
                throw new \Exception("No se pudo conectar al router {$nodo->nombre}");
            }

            // Esto lanzará excepción si falla, la funcion elimina la cola y adiciona corta la ip
            $mikroTikService->eliminarCola($ipAsignada, $nombrePlan, $cliente->id);

            // =====================================================
            // 🔥 2️⃣ SI MIKROTIK FUNCIONÓ → ACTUALIZAR BD
            // =====================================================

            // Calcular deuda
            $deudaTotal = 0;
            $facturasMorosas = 0;

            foreach ($cliente->contratos as $contrato) {
                foreach ($contrato->facturas as $factura) {
                    if (
                        $factura->estado === 'pendiente' &&
                        Carbon::parse($factura->fecha_emision)->lt(now()->subMonths(3))
                    ) {
                        $deudaTotal += $factura->saldo_pendiente;
                        $facturasMorosas++;
                    }
                }
            }

            // Cambiar estado cliente
            $cliente->update([
                'estado' => 'cortado',
                'ip' => null
            ]);

            // Cancelar contratos activos
            $cliente->contratos()
                ->where('estado', 'activo')
                ->update(['estado' => 'cancelado']);

            // Crear ticket
            $situacion = "Cliente dado de baja por morosidad superior a 3 meses.\n";
            $situacion .= "IP asignada liberada: {$ipDisplay}\n";
            $situacion .= "Nodo: {$nodo->nombre}\n";
            $situacion .= "Total deuda: $" . number_format($deudaTotal, 2) . "\n";
            $situacion .= "Facturas pendientes: {$facturasMorosas}\n";
            $situacion .= "MikroTik: Colas eliminadas correctamente.";

            $solucion = "Cliente dado de baja automáticamente.\n";
            $solucion .= "IP {$ipDisplay} liberada.\n";
            $solucion .= "Colas eliminadas en MikroTik.\n";
            $solucion .= "Contrato(s) cancelado(s).\n";
            $solucion .= "Cliente marcado como cortado.";

            Ticket::create([
                'tipo_reporte' => 'Baja por morosidad',
                'situacion' => $situacion,
                'fecha_cierre' => now(),
                'solucion' => $solucion,
                'estado' => 'cerrado',
                'cliente_id' => $cliente->id,
                'user_id' => auth()->id(),
            ]);

            DB::commit();

            $this->dispatch(
                'notify',
                type: 'success',
                message: "Cliente {$cliente->nombre} dado de baja correctamente. IP {$ipDisplay} liberada y colas eliminadas."
            );
        } catch (\Exception $e) {

            DB::rollBack();

            \Log::error("Error en baja cliente {$clienteId}: " . $e->getMessage());

            $this->dispatch(
                'notify',
                type: 'error',
                message: "No se pudo completar la baja: " . $e->getMessage()
            );
        }
    }


    public function render()
    {
        $clientes = Cliente::with(['contratos.plan.nodo', 'contratos.facturas'])

            // Clientes con facturas morosas
            ->whereHas('contratos.facturas', function ($query) {
                $query->where('estado', 'pendiente')
                    ->where('fecha_emision', '<', Carbon::now()->subMonths(3));
            })

            // 🔴 EXCLUIR clientes ya dados de baja completamente
            ->where(function ($query) {
                $query->where('estado', '!=', 'cortado')
                    ->orWhereNotNull('ip')
                    ->orWhereHas('contratos', function ($q) {
                        $q->where('estado', '!=', 'cancelado');
                    });
            })

            // Búsqueda
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('cedula', 'like', '%' . $this->search . '%')
                    ->orWhere('telefono', 'like', '%' . $this->search . '%');
            })

            ->get();

        return view('livewire.contratos.morosos', [
            'clientes' => $clientes
        ]);
    }
}
