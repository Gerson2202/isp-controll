<?php

namespace App\Livewire\Clientes;

use Livewire\Component;
use App\Services\MikroTikService; // Asegúrate de importar tu servicio
use Illuminate\Support\Facades\Log;
use App\Models\Cliente; // Asumo que tienes un modelo Cliente y lo pasas al componente

class GraficasConsumoCliente extends Component
{
    public Cliente $cliente;
    public $labels = [];
    public $subidaData = [];
    public $bajadaData = [];
    public $isLoading = false;
    public $error = null;
    protected $maxDataPoints = 10;

    public function mount(Cliente $cliente)
    {
        $this->cliente = $cliente; // Asigna el cliente que se pasa al componente

        // No instanciamos MikroTikService aquí, ya que no puede ser serializado.
        // Se instanciará en cada llamada al método obtenerDatosConsumo.

        if ($this->cliente) {
            // Se llama iniciarMonitoreo para la primera carga y para configurar el intervalo.
            $this->iniciarMonitoreo();
        }
    }

    public function iniciarMonitoreo()
    {
        // Se llama para hacer la primera carga de datos inmediatamente.
        $this->obtenerDatosConsumo();
        // Luego, se dispara el evento para que JavaScript inicie el intervalo.
        $this->dispatch('iniciar-monitoreo');
    }

    public function obtenerDatosConsumo()
    {
        $this->isLoading = true;
        $this->error = null; // Limpia el error anterior

        try {
            $nodo = $this->cliente->contrato->plan->nodo;

            // Instancia el MikroTikService aquí, justo antes de usarlo.
            // Esto asegura que se obtiene una nueva conexión fresca para cada petición Livewire.
            // Es la forma más compatible con Livewire dada la naturaleza de la clase Client de RouterOS.
            $mikrotikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            // Obtener estadísticas del cliente
            $estadisticas = $mikrotikService->obtenerEstadisticasCliente(
                $this->cliente->ip,
                $this->cliente->id
            );

            // Agregar nuevos datos
            $timestamp = now()->format('H:i:s');
            $this->labels[] = $timestamp;
            $this->subidaData[] = $estadisticas['subida'];
            $this->bajadaData[] = $estadisticas['bajada'];

            // Limitar la cantidad de puntos a 5
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
                'cliente_id' => $this->cliente->id, // Añadido para mejor seguimiento
            ]);

        } catch (\InvalidArgumentException $e) {
            // Captura errores si los datos del nodo son inválidos
            $this->error = "Error de configuración: " . $e->getMessage();
            Log::error("Error de configuración de MikroTik para cliente ID: " . $this->cliente->id . " - " . $e->getMessage());
        } catch (\RouterOS\Exceptions\ConnectException $e) {
            // Captura errores específicos de conexión a MikroTik
            $this->error = "No se pudo conectar al MikroTik: " . $e->getMessage();
            Log::error("Error de conexión a MikroTik para cliente ID: " . $this->cliente->id . " - " . $e->getMessage());
        } catch (\RouterOS\Exceptions\BadCredentialsException $e) {
            // Captura errores de credenciales incorrectas
            $this->error = "Credenciales de MikroTik incorrectas: " . $e->getMessage();
            Log::error("Credenciales incorrectas de MikroTik para cliente ID: " . $this->cliente->id . " - " . $e->getMessage());
        } catch (\Exception $e) {
            // Captura cualquier otra excepción inesperada
            $this->error = "Error inesperado: " . $e->getMessage();
            Log::error("Error inesperado en GraficasConsumoCliente para cliente ID: " . $this->cliente->id . " - " . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.clientes.graficas-consumo-cliente');
    }
}