<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use App\Services\MikroTikService;
use Illuminate\Support\Facades\Log;

class GraficasConsumoCliente extends Component
{
    public $cliente;
    public $datosConsumo = [];
    public $mostrarGraficas = false;
    public $isLoading = false;
    public $error = null;

    public function cargarGraficas()
    {
        $this->mostrarGraficas = true;
        $this->obtenerDatosConsumo();
    }

    public function obtenerDatosConsumo()
    {
        if (!$this->mostrarGraficas) return;

        $this->isLoading = true;
        $this->error = null;

        try {
            $nodo = $this->cliente->contrato->plan->nodo;
            
            $estadisticas = (new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            ))->obtenerEstadisticasCliente(
                $this->cliente->ip,
                $this->cliente->id
            );

            $this->agregarPuntoDatos($estadisticas);
            $this->dispatch('actualizarGraficas')->self();
            $this->dispatch('programarActualizacion', intervalo: 1000);

        } catch (\Exception $e) {
            $this->error = $e->getMessage();
        } finally {
            $this->isLoading = false;
        }
    }

    protected function agregarPuntoDatos($estadisticas)
    {
        $this->datosConsumo[] = [
            'timestamp' => now()->format('H:i:s'),
            'subida' => $estadisticas['subida'],
            'bajada' => $estadisticas['bajada'],
            'raw_rate' => $estadisticas['raw_rate']
        ];

        // Mantener máximo 30 puntos de datos
        if (count($this->datosConsumo) > 30) {
            array_shift($this->datosConsumo);
        }
    }

    public function resetearGraficas()
    {
        $this->mostrarGraficas = false;
        $this->datosConsumo = [];
    }

    public function render()
    {
        return view('livewire.clientes.graficas-consumo-cliente');
    }
}
