<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use App\Services\MikroTikService;
use Illuminate\Support\Facades\Log;

class GraficasConsumoCliente extends Component
{
    public $cliente;
    public $labels = [];
    public $subidaData = [];
    public $bajadaData = [];
    public $isLoading = false;
    public $error = null;
    protected $maxDataPoints = 60; // 60 puntos = 1 minuto de datos

    public function mount()
    {
        if ($this->cliente) {
            $this->iniciarMonitoreo();
        }
    }

    public function iniciarMonitoreo()
    {
        $this->obtenerDatosConsumo();
        $this->dispatch('iniciar-monitoreo');
    }

    public function obtenerDatosConsumo()
    {
        $this->isLoading = true;
        $this->error = null;

        try {
            $nodo = $this->cliente->contrato->plan->nodo;

            // Obtener estadísticas del cliente
            $estadisticas = (new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            ))->obtenerEstadisticasCliente(
                $this->cliente->ip,
                $this->cliente->id
            );

            // Agregar nuevos datos
            $timestamp = now()->format('H:i:s');
            $this->labels[] = $timestamp;
            $this->subidaData[] = $estadisticas['subida'];
            $this->bajadaData[] = $estadisticas['bajada'];

            // Limitar la cantidad de puntos a mostrar
            if (count($this->labels) > $this->maxDataPoints) {
                array_shift($this->labels);
                array_shift($this->subidaData);
                array_shift($this->bajadaData);
            }

            // Log para depuración
            Log::info('Datos acumulados:', [
                'labels' => $this->labels,
                'subidaData' => $this->subidaData,
                'bajadaData' => $this->bajadaData,
            ]);
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            Log::error("Error obteniendo estadísticas: " . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.clientes.graficas-consumo-cliente');
    }
}
