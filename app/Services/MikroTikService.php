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
    public function crearColaHija($ipCliente, $nombrePlanPadre, $subidaMbps, $bajadaMbps, $rehuso = '1:1')
    {
        try {
            $nombreColaHija = "CLIENTE-" . str_replace('.', '-', $ipCliente);
            
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

}