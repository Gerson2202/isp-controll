<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Services\MikroTikService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection; // Importar Collection

class NodoMonitoreo extends Component
{
    public $nodos; // Lista de nodos
    public $interfaces = []; // Interfaces del nodo seleccionado (estructura fija)
    public $interfaceStats = []; // Estadísticas de tráfico de las interfaces (actualizadas por polling)
    public $systemResources = []; // Recursos del sistema (cargados al seleccionar/recargar)
    public $systemHealth = []; // Salud del sistema (cargados al seleccionar/recargar)
    public $selectedNodoId; // ID del nodo seleccionado
    public $errorMessage = ''; // Mensaje de error
    public $nodoNombre = ''; // Nombre del nodo seleccionado
    public $nodo; // Objeto del nodo seleccionado
    public $isLoading = false; // Indicador de carga
    public $shouldCancel = false; // Propiedad para detener polling si es necesario (no usada en esta optimización, pero útil)

    // Propiedad computada para combinar interfaces y estadísticas para la vista
    public function getInterfacesWithStatsProperty()
    {
        // Combinar interfaces y estadísticas para facilitar la visualización
        // Usamos collect() para trabajar con los arrays de manera más conveniente
        return collect($this->interfaces)->map(function ($interface) {
            $stats = collect($this->interfaceStats)->firstWhere('name', $interface['name']);
            // Añadir las estadísticas (rx, tx) al array de la interfaz
            $interface['rx'] = $stats['rx'] ?? '0.00';
            $interface['tx'] = $stats['tx'] ?? '0.00';
            return $interface;
        });
    }

    public function mount()
    {
        // Obtener todos los nodos al cargar el componente
        // Considerar paginación si el número de nodos es muy grande
        $this->nodos = Nodo::all();
    }

    public function selectNodo($nodoId)
    {
        $this->isLoading = true;
        // Restablecer estados al seleccionar un nuevo nodo
        $this->reset(['interfaces', 'interfaceStats', 'systemResources', 'systemHealth', 'errorMessage']);

        // Obtener el nodo seleccionado
        $this->nodo = Nodo::find($nodoId);

        if (!$this->nodo) {
            $this->errorMessage = 'El nodo seleccionado no existe.';
            $this->selectedNodoId = null; // Asegurarse de que no haya un nodo seleccionado inválido
            return;
        }

        $this->nodoNombre = $this->nodo->nombre;
        $this->selectedNodoId = $nodoId;

        // Cargar los datos iniciales (interfaces, recursos, salud) al seleccionar el nodo
        $this->loadInitialData();
    }

    public function loadInitialData()
    {
        $this->isLoading = true;
        $this->errorMessage = ''; // Limpiar mensajes de error

        // Validar selección de nodo
        if (!$this->selectedNodoId) {
             $this->errorMessage = 'Por favor selecciona un nodo primero.';
             $this->isLoading = false;
             return;
        }

        // Obtener nodo de la base de datos (ya debería estar cargado por selectNodo, pero se verifica)
        $nodo = $this->nodo ?? Nodo::find($this->selectedNodoId);
        if (!$nodo) {
            $this->errorMessage = 'El nodo seleccionado no existe en la base de datos.';
            $this->isLoading = false;
            return;
        }

        try {
            // Configurar conexión MikroTik
            $mikroTikService = $this->getMikroTikService($nodo);

            // Verificar conexión
            if (!$mikroTikService->isReachable()) {
                 throw new \Exception(
                     'No se pudo conectar al nodo. Verifica: '.
                     '1. Que el nodo esté en línea. '.
                     '2. Que la IP y credenciales sean correctas. '.
                     '3. Que el puerto API esté accesible.'
                 );
            }

            // Obtener datos iniciales con reintentos
            $maxRetries = 2;

            // Cargar interfaces (estructura, no estadísticas)
            $this->interfaces = $this->fetchWithRetry($mikroTikService, 'getInterfaces', $maxRetries);

            // Cargar recursos del sistema
            $this->systemResources = $this->fetchWithRetry($mikroTikService, 'getSystemResources', $maxRetries);
            if (isset($this->systemResources['uptime'])) {
                $this->systemResources['formatted_uptime'] = $this->formatUptime($this->systemResources['uptime']);
            }

            // Cargar salud del sistema
            $this->systemHealth = $this->fetchWithRetry($mikroTikService, 'getSystemHealth', $maxRetries);

            // Iniciar la carga de estadísticas de interfaz para el polling
            $this->pollInterfaceStats();

        } catch (\Exception $e) {
            $this->errorMessage = $this->parseErrorMessage($e->getMessage());
            Log::error("Error en loadInitialData: ".$e->getMessage(), [
                'nodo_id' => $this->selectedNodoId ?? null,
                'exception' => $e
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    // Nuevo método para cargar solo estadísticas de interfaz (para polling)
    public function pollInterfaceStats()
    {
         // No mostrar indicador de carga para el polling, ya que es una actualización en segundo plano
         $this->errorMessage = ''; // Limpiar mensajes de error de polling

         // Validar selección de nodo
         if (!$this->selectedNodoId) {
              // No hay nodo seleccionado, no hacer polling
              return;
         }

         // Obtener nodo de la base de datos (puede ser necesario si el componente se recarga)
         $nodo = $this->nodo ?? Nodo::find($this->selectedNodoId);
         if (!$nodo) {
             // Nodo no encontrado, detener polling implícitamente
             Log::warning("Intento de polling para nodo inexistente: ".$this->selectedNodoId);
             return;
         }

         try {
             // Configurar conexión MikroTik
             $mikroTikService = $this->getMikroTikService($nodo);

             // Verificar conexión (opcional para polling, puede omitirse para no añadir latencia extra si el error se maneja después)
             // if (!$mikroTikService->isReachable()) {
             //      throw new \Exception('No se pudo conectar al nodo para obtener estadísticas.');
             // }

             // Obtener solo estadísticas de interfaz con reintentos
             $maxRetries = 1; // Menos reintentos para polling para no bloquear
             $this->interfaceStats = $this->fetchWithRetry($mikroTikService, 'getInterfaceStatistics', $maxRetries);

         } catch (\Exception $e) {
             // Registrar error pero no mostrar mensaje persistente al usuario para no ser intrusivo durante el polling
             Log::error("Error en pollInterfaceStats: ".$e->getMessage(), [
                 'nodo_id' => $this->selectedNodoId ?? null,
                 'exception' => $e
             ]);
             // Opcional: Podrías establecer una propiedad de error temporal para mostrar un aviso sutil
             // $this->pollingErrorMessage = 'Error al actualizar estadísticas: ' . $this->parseErrorMessage($e->getMessage());
         }
    }

    protected function fetchWithRetry($service, $method, $maxRetries = 2)
    {
        $attempts = 0;
        $lastError = null;

        while ($attempts < $maxRetries) {
            try {
                // Asegurarse de que el método existe en el servicio
                if (!method_exists($service, $method)) {
                     throw new \BadMethodCallException("Método '{$method}' no existe en MikroTikService.");
                }
                return $service->$method();
            } catch (\Exception $e) {
                $lastError = $e;
                $attempts++;
                if ($attempts < $maxRetries) {
                    sleep(1); // Espera 1 segundo entre intentos
                }
            }
        }

        // Si fallan todos los reintentos, lanzar la última excepción
        throw new \Exception("Error al obtener {$method} después de {$maxRetries} intentos: ".$lastError->getMessage(), 0, $lastError);
    }

    protected function parseErrorMessage($message)
    {
        $errors = [
            'Connection timed out' => 'El nodo no respondió a tiempo. Verifica su conectividad.',
            'Authentication failed' => 'Credenciales incorrectas. Verifica usuario y contraseña.',
            'Unable to connect' => 'No se pudo conectar al puerto API. Verifica el firewall.',
            'Socket operation failed' => 'Error de conexión de socket. Verifica la IP y el puerto.',
            // Añadir más errores comunes de conexión si se identifican
        ];

        foreach ($errors as $key => $value) {
            if (strpos($message, $key) !== false) {
                return $value;
            }
        }

        // Si no coincide ningún error conocido, devolver un mensaje genérico con el error original
        return 'Error desconocido al obtener datos del nodo: '.$message;
    }

    protected function getMikroTikService(Nodo $nodo): MikroTikService
    {
         // Crear y devolver una nueva instancia del servicio MikroTik
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

        // Si ya está en formato de días, devolverlo tal cual (ej: 1d 5h 30m)
        if (preg_match('/^\d+d\s/', $uptime)) {
            return $uptime;
        }

        // Intentar parsear formatos como 1w, 5d, 1h30m, 30m15s, 10s, etc.
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

        // Calcular componentes de tiempo
        $days = floor($seconds / (24 * 60 * 60));
        $remaining = $seconds % (24 * 60 * 60);
        $hours = floor($remaining / (60 * 60));
        $remaining %= (60 * 60);
        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;

        // Formatear según la duración
        $parts = [];
        if ($days > 0) $parts[] = $days . 'd';
        if ($hours > 0 || ($days > 0 && ($minutes > 0 || $seconds > 0))) $parts[] = sprintf("%02dh", $hours); // Mostrar horas si hay días o si hay horas, minutos o segundos
        if ($minutes > 0 || (($days > 0 || $hours > 0) && $seconds > 0)) $parts[] = sprintf("%02dm", $minutes); // Mostrar minutos si hay días/horas o si hay minutos/segundos
        if ($seconds > 0 || empty($parts)) $parts[] = sprintf("%02ds", $seconds); // Mostrar segundos si hay segundos o si no hay otras partes

        return implode(' ', $parts);
    }


    public function render()
    {
        return view('livewire.nodo-monitoreo', [
            // Pasar la propiedad computada a la vista
            'interfacesWithStats' => $this->interfacesWithStats,
        ]);
    }
}
