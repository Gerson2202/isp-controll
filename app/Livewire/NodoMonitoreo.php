<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\Log; // Importación necesaria

class NodoMonitoreo extends Component
{
    public $nodos; // Lista de nodos
    public $interfaces = []; // Interfaces del nodo seleccionado
    public $selectedNodoId; // ID del nodo seleccionado
    public $errorMessage = ''; // Mensaje de error
    public $nodoNombre = ''; // Nombre del nodo seleccionado
    public $nodo; // Objeto del nodo seleccionado
    public $interfaceStats = []; // Estadísticas de tráfico de las interfaces
    public $systemResources = [];
    public $systemHealth = [];
    public $isLoading = false;
    public $shouldCancel = false;
    
    public function mount()
    {
        // Obtener todos los nodos al cargar el componente
        $this->nodos = Nodo::all();
    }

    public function selectNodo($nodoId)
    {
        // Obtener el nodo seleccionado
        $this->nodo = Nodo::find($nodoId);

        if (!$this->nodo) {
            $this->errorMessage = 'El nodo seleccionado no existe.';
            return;
        }

        $this->nodoNombre = $this->nodo->nombre;
        $this->selectedNodoId = $nodoId;
        $this->interfaces = []; // Limpiar las interfaces al seleccionar un nuevo nodo
        $this->systemResources = []; // Limpiar las interfaces al seleccionar un nuevo nodo
        $this->systemHealth = []; // Limpiar las interfaces al seleccionar un nuevo nodo
        $this->errorMessage = ''; // Limpiar mensajes de error
    }

    public function loadInterfaces()
    {
        try {
            // Resetear estado anterior
            $this->errorMessage = '';
            $this->interfaces = [];
            $this->interfaceStats = [];
            $this->systemResources = [];
            $this->systemHealth = [];

            // 1. Validar selección de nodo
            if (!$this->selectedNodoId) {
                throw new \Exception('Por favor selecciona un nodo primero.');
            }

            // 2. Obtener nodo de la base de datos
            $nodo = Nodo::find($this->selectedNodoId);
            if (!$nodo) {
                throw new \Exception('El nodo seleccionado no existe en la base de datos.');
            }

            // 3. Configurar conexión MikroTik
            $mikroTikService = new MikroTikService(
                $nodo->ip,
                $nodo->user,
                $nodo->pass,
                $nodo->puerto_api ?? 8728
            );

            // 4. Verificar conexión
            if (!$mikroTikService->isReachable()) {
                throw new \Exception(
                    'No se pudo conectar al nodo. Verifica: '.
                    '1. Que el nodo esté en línea. '.
                    '2. Que la IP y credenciales sean correctas. '.
                    '3. Que el puerto API esté accesible.'
                );
            }

            // 5. Obtener datos con reintentos
            $maxRetries = 2;
            
            $this->interfaces = $this->fetchWithRetry($mikroTikService, 'getInterfaces', $maxRetries);
            $this->interfaceStats = $this->fetchWithRetry($mikroTikService, 'getInterfaceStatistics', $maxRetries);
            $this->systemResources = $this->fetchWithRetry($mikroTikService, 'getSystemResources', $maxRetries);
                if (isset($this->systemResources['uptime'])) {
                    $this->systemResources['formatted_uptime'] = $this->formatUptime($this->systemResources['uptime']);
                    }           
            $this->systemHealth = $this->fetchWithRetry($mikroTikService, 'getSystemHealth', $maxRetries);

        } catch (\Exception $e) {
            $this->errorMessage = $this->parseErrorMessage($e->getMessage());
            Log::error("Error en loadInterfaces: ".$e->getMessage(), [
                'nodo_id' => $this->selectedNodoId ?? null,
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
                return $service->$method();
            } catch (\Exception $e) {
                $lastError = $e;
                $attempts++;
                if ($attempts < $maxRetries) {
                    sleep(1); // Espera 1 segundo entre intentos
                }
            }
        }
        
        throw new \Exception("Error al obtener $method después de $maxRetries intentos: ".$lastError->getMessage());
    }

    protected function parseErrorMessage($message)
    {
        $errors = [
            'Connection timed out' => 'El nodo no respondió a tiempo. Verifica su conectividad.',
            'Authentication failed' => 'Credenciales incorrectas. Verifica usuario y contraseña.',
            'Unable to connect' => 'No se pudo conectar al puerto API. Verifica el firewall.',
        ];
        
        foreach ($errors as $key => $value) {
            if (strpos($message, $key) !== false) {
                return $value;
            }
        }
        
        return 'Error al obtener datos del nodo: '.$message;
    }
    public function render()
    {
        return view('livewire.nodo-monitoreo');
    }
    // Funcion para formatear el uptime de resource ya que viene de diferentes formatos si tiene dias o horas
    protected function formatUptime($uptime)
    {
        if (empty($uptime)) return 'N/A';
        
        // Si ya está en formato de días, devolverlo tal cual
        if (str_contains($uptime, 'd ')) {
            return $uptime;
        }

        // Convertir a segundos
        $seconds = 0;
        preg_match_all('/(\d+)([wdhms])/', $uptime, $matches);
        
        foreach ($matches[0] as $key => $value) {
            $num = (int)$matches[1][$key];
            $unit = $matches[2][$key];
            
            switch ($unit) {
                case 'w': $seconds += $num * 7 * 24 * 60 * 60; break;
                case 'd': $seconds += $num * 24 * 60 * 60; break;
                case 'h': $seconds += $num * 60 * 60; break;
                case 'm': $seconds += $num * 60; break;
                case 's': $seconds += $num; break;
            }
        }

        // Calcular componentes de tiempo
        $days = floor($seconds / (24 * 60 * 60));
        $remaining = $seconds % (24 * 60 * 60);
        $hours = floor($remaining / (60 * 60));
        $remaining %= (60 * 60);
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;

        // Formatear según la duración
        if ($days > 0) {
            return sprintf("%dd %02d:%02d:%02d", $days, $hours, $minutes, $seconds);
        } elseif ($hours > 0) {
            return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
        } else {
            return sprintf("%02d:%02d", $minutes, $seconds);
        }
    }
}