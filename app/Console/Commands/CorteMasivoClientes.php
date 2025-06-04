<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Factura;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CorteMasivoClientes extends Command
{
    protected $signature = 'clientes:corte-masivo';
    protected $description = 'Corta clientes pendientes automáticamente el 4 de cada mes';

    public function handle()
    {
        $facturasPendientes = Factura::with(['contrato.cliente', 'contrato.plan.nodo'])
            ->whereMonth('fecha_emision', Carbon::now()->month)
            ->whereYear('fecha_emision', Carbon::now()->year)
            ->where('estado', 'pendiente')
            ->whereHas('contrato.cliente', function($q) {
                $q->where('estado', 'activo')
                  ->whereNotNull('ip');
            })
            ->get();

        $totalClientes = $facturasPendientes->count();
        $clientesProcesados = 0;

        foreach ($facturasPendientes as $factura) {
            try {
                DB::transaction(function() use ($factura) {
                    $cliente = $factura->contrato->cliente;
                    $cliente->update(['estado' => 'cortado']);

                    Ticket::create([
                        'tipo_reporte' => 'corte masivo',
                        'situacion' => 'Corte automático por factura pendiente',
                        'estado' => 'cerrado',
                        'fecha_cierre' => now(),
                        'cliente_id' => $cliente->id,
                        'solucion' => 'Corte realizado automáticamente por sistema'
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
                $clientesProcesados++;
            } catch (\Exception $e) {
                Log::error("Error cortando cliente ID {$factura->contrato->cliente->id}: " . $e->getMessage());
                continue;
            }
        }

        $this->info("Corte masivo completado: {$clientesProcesados} de {$totalClientes} clientes procesados");
        return 0;
    }
}