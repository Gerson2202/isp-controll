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
}