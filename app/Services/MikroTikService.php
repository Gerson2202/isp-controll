<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use RouterOS\Exceptions\BadCredentialsException;
use RouterOS\Exceptions\ConnectException;
use Illuminate\Support\Facades\Log; // Importar la clase Log


class MikroTikService
{
    protected $client;

    public function __construct($host, $user, $pass, $port = 8728)
    {
        // Validar parámetros
        if (empty($host) || empty($user) || empty($pass)) {
            throw new \InvalidArgumentException('Host, user y pass son requeridos.');
        }

        // Crear el cliente
        $this->client = new Client([
            'host' => $host,
            'user' => $user,
            'pass' => $pass,
            'port' => $port,
        ]);
    }

    /**
     * Verifica si el nodo es alcanzable.
     *
     * @return bool
     */
    public function isReachable()
    {
        try {
            // Intentar realizar una operación simple (por ejemplo, obtener la identidad del router)
            $query = new Query('/system/identity/print');
            $this->client->query($query)->read();
            return true;
        } catch (ConnectException $e) {
            Log::error('Error de conexión: ' . $e->getMessage());
            return false;
        } catch (BadCredentialsException $e) {
            Log::error('Credenciales incorrectas: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Log::error('Error al verificar conectividad: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene las interfaces del MikroTik.
     *
     * @return array
     */
    public function getInterfaces()
    {
        try {
            $query = new Query('/interface/print');
            return $this->client->query($query)->read();
        } catch (ConnectException $e) {
            Log::error('Error de conexión al obtener interfaces: ' . $e->getMessage());
            return [];
        } catch (BadCredentialsException $e) {
            Log::error('Credenciales incorrectas al obtener interfaces: ' . $e->getMessage());
            return [];
        } catch (\Exception $e) {
            Log::error('Error al obtener interfaces: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene las estadísticas de tráfico de las interfaces.
     *
     * @return array
     */
    public function getInterfaceStatistics()
    {
        try {
            // Obtener la lista de interfaces
            $query = new Query('/interface/print');
            $interfaces = $this->client->query($query)->read();
    
            // Depurar la lista de interfaces
            // dd('Interfaces disponibles:', $interfaces); // Detener la ejecución y mostrar la lista de interfaces
    
            // Si no hay interfaces, devolver un array vacío
            if (empty($interfaces)) {
                return [];
            }
    
            // Obtener las estadísticas de tráfico para cada interfaz
            $stats = [];
            foreach ($interfaces as $interface) {
                $interfaceName = $interface['name'];
    
                $query = new Query('/interface/monitor-traffic');
                $query->equal('interface', $interfaceName); // Monitorear la interfaz específica
                $query->equal('once'); // Obtener datos una sola vez
    
                $interfaceStats = $this->client->query($query)->read();
    
                // Depurar las estadísticas de la interfaz
                // dd("Estadísticas de {$interfaceName}:", $interfaceStats); // Detener la ejecución y mostrar las estadísticas
    
                // Procesar las estadísticas
                if (!empty($interfaceStats)) {
                    $stats[] = [
                        'name' => $interfaceName,
                        'rx' => $this->bitsToKbps($interfaceStats[0]['rx-bits-per-second'] ?? 0), // Convertir a Kbps
                        'tx' => $this->bitsToKbps($interfaceStats[0]['tx-bits-per-second'] ?? 0), // Convertir a Kbps
                    ];
                } else {
                    // Si no hay estadísticas, asignar valores predeterminados
                    $stats[] = [
                        'name' => $interfaceName,
                        'rx' => 0, // Valor predeterminado si no hay estadísticas
                        'tx' => 0, // Valor predeterminado si no hay estadísticas
                    ];
                }
            }
    
            return $stats;
        } catch (ConnectException $e) {
            dd('Error de conexión:', $e->getMessage()); // Depurar el error
        } catch (BadCredentialsException $e) {
            dd('Credenciales incorrectas:', $e->getMessage()); // Depurar el error
        } catch (\Exception $e) {
            dd('Error al obtener estadísticas:', $e->getMessage()); // Depurar el error
        }
    }
    /**
     * Convierte bits por segundo a kilobits por segundo (Kbps).
     *
     * @param string $bits Valor en bits por segundo.
     * @return float
     */
    private function bitsToKbps($bits)
    {
        return (float)$bits / 1000000; // 1 Mbps = 1000000 bps
       
    }

    // Consultar infromacion de systeam 
    public function getSystemResources()
    {
        $query = new Query('/system/resource/print');
        $response = $this->client->query($query)->read();
    
        return $response[0] ?? [];
    }
    
    //consultar voltaje y tempertura
    public function getSystemHealth()
    {
        // Realizamos la consulta a /system/health/print
        $query = new Query('/system/health/print');
        $response = $this->client->query($query)->read();

        // Inicializamos las variables para almacenar los valores de temperatura y voltaje
        $healthData = [
            'temperature' => 'N/A',
            'voltage' => 'N/A'
        ];

        // Iteramos sobre los resultados para encontrar los valores de temperatura y voltaje
        foreach ($response as $item) {
            if ($item['name'] == 'voltage') {
                $healthData['voltage'] = $item['value'] . ' ' . $item['type'];
            }
            if ($item['name'] == 'temperature') {
                $healthData['temperature'] = $item['value'] . ' ' . $item['type'];
            }
        }

        return $healthData;
    }

// -----------------------------------------------------------------
    // FUNCIONES PARA CREAR COLAS PADRES
       

    public function crearColaPadre($nombrePlan, $subidaMbps, $bajadaMbps)
    {
        try {
            $nombreCola =$nombrePlan;
            // Verificar si la cola ya existe
            $query = (new Query('/queue/simple/print'))
                ->where('name', $nombreCola);

            $existe = $this->client->query($query)->read();

            if (!empty($existe)) {
                throw new \Exception("La cola padre '{$nombreCola}' ya existe en este nodo");
            }

            // Crear la cola padre
            $query = (new Query('/queue/simple/add'))
                ->equal('name', $nombreCola)
                ->equal('target', '10.100.0.1')
                ->equal('max-limit', "{$subidaMbps}M/{$bajadaMbps}M")
                ->equal('priority', '1/1')
                ->equal('disabled', 'no');

            $resultado = $this->client->query($query)->read();

            if (empty($resultado)) {
                throw new \Exception("No se recibió respuesta al crear la cola");
            }

            return [
                'success' => true,
                'message' => "Cola padre '{$nombreCola}' creada exitosamente",
                'data' => $resultado
            ];

        } catch (ConnectException $e) {
            Log::error("Error de conexión al crear cola: " . $e->getMessage());
            throw new \Exception("No se pudo conectar al MikroTik: " . $e->getMessage());
        } catch (BadCredentialsException $e) {
            Log::error("Credenciales incorrectas al crear cola: " . $e->getMessage());
            throw new \Exception("Credenciales incorrectas para el MikroTik");
        } catch (\Exception $e) {
            Log::error("Error al crear cola padre: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verifica si una cola específica existe
     *
     * @param string $nombreCola
     * @return bool
     */
    public function verificarColaExistente($nombreCola)
    {
        try {
            $query = (new Query('/queue/simple/print'))
                ->where('name', $nombreCola);

            $resultado = $this->client->query($query)->read();

            return !empty($resultado);
        } catch (\Exception $e) {
            Log::error("Error al verificar cola: " . $e->getMessage());
            return false;
        }
    }
    // FIN -FUNCIONES PARA CREAR COLAS PADRES

    
    // Crear cola hija
    public function crearColaHija($cliente_id,$ipCliente, $nombrePlanPadre, $subidaMbps, $bajadaMbps, $rehuso = '1:1')
    {
        try {
            $nombreColaHija = "CLIENTE-" . str_replace('.', '-', $cliente_id);
            
            // 1. Calcular limit-at según rehúso (ahora con 1:2)
            $factorDivision = 1;
            if ($rehuso === '1:2') {
                $factorDivision = 2;
            } elseif ($rehuso === '1:4') {
                $factorDivision = 4;
            } elseif ($rehuso === '1:6') {
                $factorDivision = 6;
            }
            
            $subidaLimitAt = ceil($subidaMbps / $factorDivision);
            $bajadaLimitAt = ceil($bajadaMbps / $factorDivision);
    
            // 2. Actualizar target y max-limit en cola padre
            $this->actualizarTargetColaPadre($nombrePlanPadre, $ipCliente, $subidaLimitAt, $bajadaLimitAt);
            
            // 3. Crear cola hija
            $query = (new Query('/queue/simple/add'))
                ->equal('name', $nombreColaHija)
                ->equal('target', $ipCliente.'/32')
                ->equal('parent', $nombrePlanPadre)
                ->equal('max-limit', $subidaMbps.'M/'.$bajadaMbps.'M')
                ->equal('limit-at', $subidaLimitAt.'M/'.$bajadaLimitAt.'M')
                ->equal('disabled', 'no');
    
            return $this->client->query($query)->read();
    
        } catch (\Exception $e) {
            throw new \Exception("Error al crear cola: " . $e->getMessage());
        }
    }
    
    private function actualizarTargetColaPadre($nombrePadre, $ipHija, $subidaLimitAt, $bajadaLimitAt)
    {
        // 1. Obtener información actual de la cola padre
        $queryPadre = (new Query('/queue/simple/print'))
            ->where('name', $nombrePadre);
        $colaPadre = $this->client->query($queryPadre)->read()[0];
    
        // 2. Convertir de bits a Mbps
        $maxLimitActual = explode('/', $colaPadre['max-limit']);
        $maxSubidaActual = (float)$maxLimitActual[0] / 1000000;
        $maxBajadaActual = (float)$maxLimitActual[1] / 1000000;
    
        // 3. Actualizar target
        $ipConMascara = $ipHija.'/32';
        $targetActual = $colaPadre['target'] ?? '';
        
        if (strpos($targetActual, $ipConMascara) === false) {
            $nuevoTarget = $targetActual ? $targetActual.','.$ipConMascara : $ipConMascara;
            
            // 4. Calcular suma total de limit-ats
            $totalHijos = $nuevoTarget ? count(explode(',', $nuevoTarget)) : 0;
            $totalSubidaNecesaria = $subidaLimitAt * $totalHijos;
            $totalBajadaNecesaria = $bajadaLimitAt * $totalHijos;
    
            // 5. Actualizar max-limit solo si es necesario
            $nuevoMaxSubida = $maxSubidaActual;
            $nuevoMaxBajada = $maxBajadaActual;
    
            if ($totalSubidaNecesaria > $maxSubidaActual) {
                $nuevoMaxSubida = $totalSubidaNecesaria;
            }
    
            if ($totalBajadaNecesaria > $maxBajadaActual) {
                $nuevoMaxBajada = $totalBajadaNecesaria;
            }
    
            // 6. Aplicar cambios
            $this->client->query(
                (new Query('/queue/simple/set'))
                ->equal('.id', $colaPadre['.id'])
                ->equal('max-limit', ($nuevoMaxSubida * 1000000).'/'.($nuevoMaxBajada * 1000000))
                ->equal('target', $nuevoTarget)
            )->read();
        }
    }
   // FIN --Crear cola hija
    

//    --------------
    // FUNCIONES PARA ACTUALIZAR PLAN DE CLIENTE
    public function actualizarPlanMikroTik($clienteId, $ipCliente, $planAnterior, $planNuevo, $subidaMbps, $bajadaMbps, $rehuso = '1:1')
    {
        try {
            // 1. Calcular limit-at para el NUEVO plan
            $factorDivision = 1;
            if ($rehuso === '1:2') $factorDivision = 2;
            elseif ($rehuso === '1:4') $factorDivision = 4;
            elseif ($rehuso === '1:6') $factorDivision = 6;
            
            $subidaLimitAt = ceil($subidaMbps / $factorDivision);
            $bajadaLimitAt = ceil($bajadaMbps / $factorDivision);

            // 2. Actualizar cola anterior (SOLO remover IP, SIN cambiar max-limit)
            $this->removerTargetDeColaPadre($planAnterior, $ipCliente);
            $this->eliminarColaHija($clienteId, $planAnterior);
            
            // 3. Actualizar NUEVA cola (agregar IP Y actualizar límites)
            $this->agregarTargetAColaPadre($planNuevo, $ipCliente);
            $this->actualizarMaxLimitColaPadre($planNuevo, $subidaLimitAt, $bajadaLimitAt);
            
            // 4. Crear nueva cola hija
            $nombreColaHija = "CLIENTE-" . str_replace('.', '-', $clienteId);
            $query = (new Query('/queue/simple/add'))
                ->equal('name', $nombreColaHija)
                ->equal('target', $ipCliente.'/32')
                ->equal('parent', $planNuevo)
                ->equal('max-limit', $subidaMbps.'M/'.$bajadaMbps.'M')
                ->equal('limit-at', $subidaLimitAt.'M/'.$bajadaLimitAt.'M')
                ->equal('disabled', 'no');
            
            $this->client->query($query)->read();

        } catch (\Exception $e) {
            Log::error("Error al actualizar plan en MikroTik", [
                'cliente_id' => $clienteId,
                'ip' => $ipCliente,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Error al actualizar plan en MikroTik: " . $e->getMessage());
        }
    }

    /**
     * Actualiza los límites máximos de la cola padre (SOLO para nueva cola)
     */
    private function actualizarMaxLimitColaPadre($nombrePlan, $subidaLimitAt, $bajadaLimitAt)
    {
        // 1. Obtener información actual de la cola padre
        $queryPadre = (new Query('/queue/simple/print'))
            ->where('name', $nombrePlan);
        $colaPadre = $this->client->query($queryPadre)->read()[0];

        // 2. Convertir de bits a Mbps (igual que en tu función)
        $maxLimitActual = explode('/', $colaPadre['max-limit']);
        $maxSubidaActual = (float)$maxLimitActual[0] / 1000000;
        $maxBajadaActual = (float)$maxLimitActual[1] / 1000000;

        // 3. Contar TODOS los targets (incluyendo IP base)
        $targetActual = $colaPadre['target'] ?? '';
        $totalTargets = $targetActual ? count(explode(',', $targetActual)) : 0;

        // 4. Calcular necesidades totales (igual que en tu función)
        $totalSubidaNecesaria = $subidaLimitAt * $totalTargets;
        $totalBajadaNecesaria = $bajadaLimitAt * $totalTargets;

        // 5. Determinar nuevos máximos (igual que en tu función)
        $nuevoMaxSubida = $maxSubidaActual;
        $nuevoMaxBajada = $maxBajadaActual;

        if ($totalSubidaNecesaria > $maxSubidaActual) {
            $nuevoMaxSubida = $totalSubidaNecesaria;
        }

        if ($totalBajadaNecesaria > $maxBajadaActual) {
            $nuevoMaxBajada = $totalBajadaNecesaria;
        }

        // 6. Aplicar cambios SOLO si es necesario (igual que en tu función)
        if ($nuevoMaxSubida > $maxSubidaActual || $nuevoMaxBajada > $maxBajadaActual) {
            $this->client->query(
                (new Query('/queue/simple/set'))
                    ->equal('.id', $colaPadre['.id'])
                    ->equal('max-limit', ($nuevoMaxSubida * 1000000).'/'.($nuevoMaxBajada * 1000000))
            )->read();

            Log::info("Límites actualizados", [
                'plan' => $nombrePlan,
                'targets' => $totalTargets,
                'previous_max' => "$maxSubidaActual/$maxBajadaActual",
                'new_max' => "$nuevoMaxSubida/$nuevoMaxBajada",
                'reason' => "{$totalTargets} targets x {$subidaLimitAt}M/{$bajadaLimitAt}M"
            ]);
        }

        return true;
    }

    private function removerTargetDeColaPadre($nombrePlan, $ipCliente)
    {
        $ipConMascara = $ipCliente.'/32';
        $colaPadre = $this->obtenerColaPadre($nombrePlan);
        
        if ($colaPadre && isset($colaPadre['target'])) {
            $targets = explode(',', $colaPadre['target']);
            $nuevosTargets = array_filter($targets, function($target) use ($ipConMascara) {
                return trim($target) !== $ipConMascara;
            });
            
            $this->client->query(
                (new Query('/queue/simple/set'))
                    ->equal('.id', $colaPadre['.id'])
                    ->equal('target', implode(',', $nuevosTargets))
            )->read();
        }
    }

    private function agregarTargetAColaPadre($nombrePlan, $ipCliente)
    {
        $ipConMascara = $ipCliente.'/32';
        $colaPadre = $this->obtenerColaPadre($nombrePlan);
        
        if ($colaPadre) {
            $targets = isset($colaPadre['target']) ? explode(',', $colaPadre['target']) : [];
            
            if (!in_array($ipConMascara, $targets)) {
                $targets[] = $ipConMascara;
                $this->client->query(
                    (new Query('/queue/simple/set'))
                        ->equal('.id', $colaPadre['.id'])
                        ->equal('target', implode(',', $targets))
                )->read();
            }
        }
    }

    private function obtenerColaPadre($nombrePlan)
    {
        $query = (new Query('/queue/simple/print'))
            ->where('name', $nombrePlan);
        $result = $this->client->query($query)->read();
        return $result[0] ?? null;
    }
    
    private function eliminarColaHija($clienteId, $planPadre)
    {
        $nombreCola = "CLIENTE-".str_replace('.', '-', $clienteId);
        
        $query = (new Query('/queue/simple/print'))
            ->where('name', $nombreCola)
            ->where('parent', $planPadre);
        
        $colas = $this->client->query($query)->read();
        
        if (!empty($colas)) {
            $this->client->query(
                (new Query('/queue/simple/remove'))
                    ->equal('.id', $colas[0]['.id'])
            )->read();
        }
    }

    // FUNCIONES PARA CAMBIO DE NODO 
    /**
 * Elimina todas las colas asociadas a un cliente (hijas y referencia en padre)
 * 
 * @param string $ipCliente La IP del cliente
 * @param string $nombrePlan El nombre del plan padre
 * @param int $clienteId El ID del cliente para identificar la cola hija
 * @return bool
 * @throws \Exception
 */
    public function eliminarCola($ipCliente, $nombrePlan, $clienteId)
    {
        try {
            // 1. Eliminar la cola hija del cliente
            $this->eliminarColaHija($clienteId, $nombrePlan);
            
            // 2. Remover la IP del target de la cola padre
            $this->removerTargetDeColaPadre($nombrePlan, $ipCliente);
            $this->cortarCliente($ipCliente);
            return true;
        } catch (\Exception $e) {
            Log::error("Error al eliminar colas para cliente $clienteId: " . $e->getMessage());
            throw new \Exception("Error al eliminar colas en MikroTik: " . $e->getMessage());
        }
    }


    // FUNCIONES  CORTAR O SUSPENDER CLIENTE

    public function manejarEstadoCliente($ipCliente, $estado)
    {
        try {
            switch ($estado) {
                case 'activo':
                    $this->activarCliente($ipCliente);
                    break;
                case 'cortado':
                    $this->cortarCliente($ipCliente);
                    break;
                // case 'suspendido':
                //     $this->suspenderCliente($ipCliente);
                //     break;
            }
            return true;
        } catch (\Exception $e) {
            Log::error("Error al actualizar estado en MikroTik: " . $e->getMessage());
            throw new \Exception("Error al actualizar estado en MikroTik: " . $e->getMessage());
        }
    }

    /**
     * Activa un cliente moviendo su IP a la lista "activado"
     */
      private function activarCliente($ipCliente)
    {
        
        // Agregar a la lista "activado"
        $this->agregarAAddressList($ipCliente, 'activado');
        
        // Primero eliminar de la lista "cortado" si existe
        $this->eliminarDeAddressList($ipCliente, 'cortado');
        
        
    }

    /**
     * Corta un cliente moviendo su IP a la lista "cortado"
     */
    private function cortarCliente($ipCliente)
    {
        // Primero eliminar de la lista "activado" si existe
        $this->eliminarDeAddressList($ipCliente, 'activado');
        
        // Agregar a la lista "cortado"
        $this->agregarAAddressList($ipCliente, 'cortado');
    }
    /**
     * Agrega una IP a una address list específica
     */
    private function agregarAAddressList($ip, $listName)
    {
        try {
            // Verificar si ya existe en la lista
            $query = (new Query('/ip/firewall/address-list/print'))
                ->where('address', $ip)
                ->where('list', $listName);

            $existing = $this->client->query($query)->read();

            if (empty($existing)) {
                $query = (new Query('/ip/firewall/address-list/add'))
                    ->equal('address', $ip)
                    ->equal('list', $listName);

                $this->client->query($query)->read();
                Log::info("IP $ip agregada a la lista $listName");
            }
        } catch (\Exception $e) {
            Log::error("Error al agregar IP a address list: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Elimina una IP de una address list específica
     */
    private function eliminarDeAddressList($ip, $listName)
    {
        try {
            $query = (new Query('/ip/firewall/address-list/print'))
                ->where('address', $ip)
                ->where('list', $listName);

            $entries = $this->client->query($query)->read();

            foreach ($entries as $entry) {
                $query = (new Query('/ip/firewall/address-list/remove'))
                    ->equal('.id', $entry['.id']);
                $this->client->query($query)->read();
                Log::info("IP $ip eliminada de la lista $listName");
            }
        } catch (\Exception $e) {
            Log::error("Error al eliminar IP de address list: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Obtiene todas las IPs de una lista específica
     */
    public function obtenerIPsDeLista($listName)
    {
        try {
            $query = (new Query('/ip/firewall/address-list/print'))
                ->where('list', $listName);

            $ips = $this->client->query($query)->read();
            return array_map(function($item) {
                return $item['address'];
            }, $ips);
        } catch (\Exception $e) {
            Log::error("Error al obtener IPs de la lista: " . $e->getMessage());
            throw $e;
        }
    }

    // Graficas de consumo

    public function obtenerEstadisticasCliente($ipCliente, $clienteId)
    {
        try {
            $query = (new Query('/queue/simple/print'))
            ->where('target', $ipCliente.'/32');
            
            $colas = $this->client->query($query)->read();
            
            if (empty($colas)) {
                throw new \Exception("No se encontró la cola del cliente");
            }

            $cola = $colas[0];
            $rates = explode('/', $cola['rate'] ?? '0/0');
            
            return [
                'bajada' => round($rates[1] / 1000000, 2), // Upload en Mbps
                'subida' => round($rates[0] / 1000000, 2),  // Download en Mbps
                'raw_rate' => $cola['rate']
            ];

        } catch (\Exception $e) {
            Log::error("Error obteniendo estadísticas", [
                'ip' => $ipCliente,
                'error' => $e->getMessage()
            ]);
            throw new \Exception("Error al obtener datos: " . $e->getMessage());
        }
    }

    // funcionaes para actualizar plan ;  elimnar cola padre con sus hijas
    
   /**
 * Elimina una cola padre y todas sus colas hijas en MikroTik
 * 
 * @param string $nombreColaPadre Nombre exacto de la cola padre
 * @return bool
 * @throws \Exception Si ocurre algún error
 */
    public function eliminarColaPadreYHijas(string $nombreColaPadre): bool
    {
        try {
            // 1. Primero eliminamos todas las colas hijas (targets)
            $queryHijas = new Query('/queue/simple/print');
            $queryHijas->where('parent', $nombreColaPadre);
            $hijas = $this->client->query($queryHijas)->read();

            foreach ($hijas as $hija) {
                $removeQuery = new Query('/queue/simple/remove');
                $removeQuery->equal('.id', $hija['.id']);
                $this->client->query($removeQuery)->read();
            }

            // 2. Luego eliminamos la cola padre
            $queryPadre = new Query('/queue/simple/print');
            $queryPadre->where('name', $nombreColaPadre);
            $padre = $this->client->query($queryPadre)->read();

            if (!empty($padre)) {
                $removePadre = new Query('/queue/simple/remove');
                $removePadre->equal('.id', $padre[0]['.id']);
                $this->client->query($removePadre)->read();
            }

            return true;

        } catch (\RouterOS\Exceptions\ConnectException $e) {
            throw new \Exception("Error de conexión al MikroTik: " . $e->getMessage());
            
        } catch (\RouterOS\Exceptions\BadCredentialsException $e) {
            throw new \Exception("Credenciales incorrectas para el MikroTik: " . $e->getMessage());
            
        } catch (\Exception $e) {
            throw new \Exception("Error al eliminar colas: " . $e->getMessage());
        }
    }
}