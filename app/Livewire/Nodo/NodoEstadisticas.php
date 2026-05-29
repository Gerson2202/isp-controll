<?php

namespace App\Livewire\Nodo;

use App\Models\Nodo;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class NodoEstadisticas extends Component
{
    public $nodoId;
    public $nodo;
    public $interfaces = [];
    public $interfaceStats = [];
    public $systemResources = [];
    public $systemHealth = [];
    public $errorMessage = '';
    public $isLoading = false;
    public $dataLoaded = false;

    // Propiedad computada
    public function getInterfacesWithStatsProperty()
    {
        return collect($this->interfaces)->map(function ($interface) {
            $stats = collect($this->interfaceStats)->firstWhere('name', $interface['name']);
            $interface['rx'] = $stats['rx'] ?? '0.00';
            $interface['tx'] = $stats['tx'] ?? '0.00';
            return $interface;
        });
    }

    public function mount($nodoId)
    {
        $this->nodoId = $nodoId;
        $this->nodo = Nodo::find($nodoId);

        if (!$this->nodo) {
            $this->errorMessage = 'El nodo no existe.';
            return;
        }

        // $this->loadInitialData();
    }

    public function loadInitialData()
    {
        $this->isLoading = true;
        $this->errorMessage = '';
        $this->dataLoaded = true;
        try {
            $mikroTikService = $this->getMikroTikService($this->nodo);

            if (!$mikroTikService->isReachable()) {
                throw new \Exception(
                    'No se pudo conectar al nodo. Verifica: ' .
                    '1. Que el nodo esté en línea. ' .
                    '2. Que la IP y credenciales sean correctas. ' .
                    '3. Que el puerto API esté accesible.'
                );
            }

            $maxRetries = 2;

            $this->interfaces = $this->fetchWithRetry($mikroTikService, 'getInterfaces', $maxRetries);
            $this->systemResources = $this->fetchWithRetry($mikroTikService, 'getSystemResources', $maxRetries);

            if (isset($this->systemResources['uptime'])) {
                $this->systemResources['formatted_uptime'] = $this->formatUptime($this->systemResources['uptime']);
            }

            $this->systemHealth = $this->fetchWithRetry($mikroTikService, 'getSystemHealth', $maxRetries);
            $this->pollInterfaceStats();

        } catch (\Exception $e) {
            $this->errorMessage = $this->parseErrorMessage($e->getMessage());
            Log::error("Error en loadInitialData: " . $e->getMessage(), [
                'nodo_id' => $this->nodoId,
                'exception' => $e
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function pollInterfaceStats()
    {
        $this->errorMessage = '';

        try {
            $mikroTikService = $this->getMikroTikService($this->nodo);
            $maxRetries = 1;
            $this->interfaceStats = $this->fetchWithRetry($mikroTikService, 'getInterfaceStatistics', $maxRetries);

        } catch (\Exception $e) {
            Log::error("Error en pollInterfaceStats: " . $e->getMessage(), [
                'nodo_id' => $this->nodoId,
                'exception' => $e
            ]);
        }
    }

    protected function fetchWithRetry($service, $method, $maxRetries = 2)
    {
        $attempts = 0;
        $lastError = null;

        while ($attempts < $maxRetries) {
            try {
                if (!method_exists($service, $method)) {
                    throw new \BadMethodCallException("Método '{$method}' no existe en MikroTikService.");
                }
                return $service->$method();
            } catch (\Exception $e) {
                $lastError = $e;
                $attempts++;
                if ($attempts < $maxRetries) {
                    sleep(1);
                }
            }
        }

        throw new \Exception("Error al obtener {$method} después de {$maxRetries} intentos: " . $lastError->getMessage(), 0, $lastError);
    }

    protected function parseErrorMessage($message)
    {
        $errors = [
            'Connection timed out' => 'El nodo no respondió a tiempo. Verifica su conectividad.',
            'Authentication failed' => 'Credenciales incorrectas. Verifica usuario y contraseña.',
            'Unable to connect' => 'No se pudo conectar al puerto API. Verifica el firewall.',
            'Socket operation failed' => 'Error de conexión de socket. Verifica la IP y el puerto.',
        ];

        foreach ($errors as $key => $value) {
            if (strpos($message, $key) !== false) {
                return $value;
            }
        }

        return 'Error desconocido al obtener datos del nodo: ' . $message;
    }

    protected function getMikroTikService(Nodo $nodo): MikroTikService
    {
        return new MikroTikService(
            $nodo->ip,
            $nodo->user,
            $nodo->pass,
            $nodo->puerto_api ?? 8728
        );
    }

    protected function formatUptime($uptime)
    {
        if (empty($uptime)) return 'N/A';

        if (preg_match('/^\d+d\s/', $uptime)) {
            return $uptime;
        }

        $seconds = 0;
        preg_match_all('/(\d+)([wdhms])/', $uptime, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $num = (int)$match[1];
            $unit = $match[2];

            switch ($unit) {
                case 'w': $seconds += $num * 7 * 24 * 60 * 60; break;
                case 'd': $seconds += $num * 24 * 60 * 60; break;
                case 'h': $seconds += $num * 60 * 60; break;
                case 'm': $seconds += $num * 60; break;
                case 's': $seconds += $num; break;
            }
        }

        $days = floor($seconds / (24 * 60 * 60));
        $remaining = $seconds % (24 * 60 * 60);
        $hours = floor($remaining / (60 * 60));
        $remaining %= (60 * 60);
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;

        $parts = [];
        if ($days > 0) $parts[] = $days . 'd';
        if ($hours > 0 || ($days > 0 && ($minutes > 0 || $seconds > 0))) $parts[] = sprintf("%02dh", $hours);
        if ($minutes > 0 || (($days > 0 || $hours > 0) && $seconds > 0)) $parts[] = sprintf("%02dm", $minutes);
        if ($seconds > 0 || empty($parts)) $parts[] = sprintf("%02ds", $seconds);

        return implode(' ', $parts);
    }

    public function render()
    {
        return view('livewire.nodo.nodo-estadisticas', [
            'interfacesWithStats' => $this->interfacesWithStats,
        ]);
    }
}