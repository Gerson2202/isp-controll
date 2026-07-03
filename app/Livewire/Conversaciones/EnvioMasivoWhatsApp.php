<?php

namespace App\Livewire\Conversaciones;

use Livewire\Component;
use App\Models\Factura;
use App\Models\EnvioMasivoLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class EnvioMasivoWhatsApp extends Component
{
    public $clientesPendientes = [];
    public $totalClientes = 0;
    public $clientesEnviados = 0;
    public $procesando = false;
    public $progreso = 0;
    public $limiteDiario = 250;
    public $enviadosHoy = 0;
    public $pendientesPorEnviar = 0;
    public $fechaUltimoEnvio = null;
    public $mensajesEnviadosEsteMes = 0;
    public $puedeEnviar = false; 


    public $webhookUrl = 'https://automatizacion-isprotik1-n8n.ijnhto.easypanel.host/webhook/mesaje-aviso';

    public function mount()
    {
        $this->contarClientesPendientes();
        $this->verificarEnvioDiario();
        $this->verificarPermisoEnvio(); // ← Dato para que se habilite el boton de enviar 

    }

    /**
     * Verifica si el usuario puede enviar mensajes (solo día 4 del mes)
     */
    public function verificarPermisoEnvio()
    {
        $hoy = Carbon::now();
        $this->puedeEnviar = $hoy->day == 4;
    }
    /**
     * Obtiene los clientes con facturas pendientes que NO han recibido recordatorio este mes
     */
    public function contarClientesPendientes()
    {
        $sub = Factura::selectRaw('MAX(id) as max_id')
            ->groupBy('contrato_id');

        $inicioMes = Carbon::now()->startOfMonth();

        $this->clientesPendientes = Factura::with(['contrato.cliente'])
            ->joinSub($sub, 'ultimas', function ($join) {
                $join->on('facturas.id', '=', 'ultimas.max_id');
            })
            ->where('facturas.estado', 'pendiente')
            ->get()
            ->filter(function ($factura) use ($inicioMes) {
                $clienteId = $factura->contrato->cliente->id ?? null;

                // Si no tiene cliente_id, lo incluimos (por si acaso)
                if (!$clienteId) {
                    return true;
                }

                // 🔥 Verificar si ya recibió RECORDATORIO este mes (NO comprobante)
                $yaRecibioRecordatorio = EnvioMasivoLog::where('cliente_id', $clienteId)
                    ->where('estado', 'enviado')
                    ->where('plantilla_usada', 'recordatorio_pago') // ← SOLO recordatorios
                    ->where('fecha_envio', '>=', $inicioMes)
                    ->exists();

                // Solo incluimos si NO ha recibido recordatorio este mes
                return !$yaRecibioRecordatorio;
            })
            ->map(function ($factura) {
                return [
                    'cliente_id' => $factura->contrato->cliente->id ?? null,
                    'nombre' => $factura->contrato->cliente->nombre ?? 'Sin nombre',
                    'telefono' => $factura->contrato->cliente->telefono ?? 'Sin teléfono',
                    'monto' => '$' . number_format($factura->monto_total, 0, ',', '.'),
                    'monto_raw' => $factura->monto_total,
                ];
            })
            ->values() // Reindexar el array
            ->toArray();

        $this->totalClientes = count($this->clientesPendientes);

        // Contar mensajes enviados este mes
        $this->contarMensajesEsteMes();
    }

    /**
     * Cuenta cuántos mensajes se han enviado en el mes actual (TODOS los tipos)
     */
    public function contarMensajesEsteMes()
    {
        $inicioMes = Carbon::now()->startOfMonth();

        if (Schema::hasTable('envio_masivo_logs')) {
            // 🔥🔥🔥 Cuenta TODOS los mensajes del mes (recordatorios + comprobantes)
            $this->mensajesEnviadosEsteMes = EnvioMasivoLog::where('estado', 'enviado')
                ->where('fecha_envio', '>=', $inicioMes)
                ->count();
        } else {
            $this->mensajesEnviadosEsteMes = 0;
        }
    }

    /**
     * Verifica si un cliente ya recibió RECORDATORIO en el mes actual
     */
    public function clienteYaRecibioMensajeEsteMes($clienteId)
    {
        $inicioMes = Carbon::now()->startOfMonth();

        // 🔥 SOLO verificar RECORDATORIOS (NO comprobantes)
        return EnvioMasivoLog::where('cliente_id', $clienteId)
            ->where('estado', 'enviado')
            ->where('plantilla_usada', 'recordatorio_pago') // ← SOLO recordatorios
            ->where('fecha_envio', '>=', $inicioMes)
            ->exists();
    }

    /**
     * Cuenta cuántos recordatorios envió este mes a un cliente específico
     */
    public function contarMensajesClienteEsteMes($clienteId)
    {
        $inicioMes = Carbon::now()->startOfMonth();

        return EnvioMasivoLog::where('cliente_id', $clienteId)
            ->where('estado', 'enviado')
            ->where('plantilla_usada', 'recordatorio_pago')
            ->where('fecha_envio', '>=', $inicioMes)
            ->count();
    }

    /**
     * Verifica cuántos clientes únicos han recibido mensaje HOY (TODOS los tipos)
     */
    public function verificarEnvioDiario()
    {
        $hoy = Carbon::today();

        if (Schema::hasTable('envio_masivo_logs')) {
            // 🔥🔥🔥 Cuenta TODOS los mensajes enviados hoy (recordatorios + comprobantes)
            $this->enviadosHoy = EnvioMasivoLog::whereDate('fecha_envio', $hoy)
                ->where('estado', 'enviado')
                ->count(); // ← Simple count, sin distinct ni filtros

            $ultimoEnvio = EnvioMasivoLog::where('estado', 'enviado')
                ->latest()
                ->first();

            $this->fechaUltimoEnvio = $ultimoEnvio ? $ultimoEnvio->created_at : null;
        } else {
            $this->enviadosHoy = 0;
            $this->fechaUltimoEnvio = null;
        }

        $this->pendientesPorEnviar = max(0, $this->totalClientes - $this->enviadosHoy);
    }

    /**
     * Envía los clientes a n8n para procesar los mensajes
     */
    public function enviarMensajesMasivos()
    {
        $this->procesando = true;
        $this->progreso = 0;

        try {
            // Recargar clientes pendientes (con filtro mensual)
            $this->contarClientesPendientes();
            $this->verificarEnvioDiario();

            // Verificar si ya se alcanzó el límite diario
            if ($this->enviadosHoy >= $this->limiteDiario) {
                $this->dispatch(
                    'notify',
                    type: 'warning',
                    message: "⚠️ Límite diario de {$this->limiteDiario} mensajes alcanzado. Intenta mañana."
                );
                $this->procesando = false;
                return;
            }

            // Calcular cuántos clientes podemos enviar hoy
            $limiteRestante = $this->limiteDiario - $this->enviadosHoy;

            $clientesDisponibles = array_slice(
                $this->clientesPendientes,
                0, // Siempre empezamos desde 0 porque ya filtramos los que recibieron este mes
                $limiteRestante // Solo los que caben en el límite
            );

            $totalAEnviar = count($clientesDisponibles);

            if ($totalAEnviar === 0) {
                $this->dispatch(
                    'notify',
                    type: 'info',
                    message: 'No hay clientes pendientes por notificar este mes.'
                );
                $this->procesando = false;
                return;
            }

            // 📦 DATOS OPTIMIZADOS - SOLO LO NECESARIO PARA N8N
            $datosParaN8N = [
                'clientes' => array_map(function ($cliente) {
                    return [
                        'nombre' => $cliente['nombre'],
                        'telefono' => $cliente['telefono'],
                        'monto' => $cliente['monto'],
                    ];
                }, $clientesDisponibles),
                'plantilla' => 'recordatorio_pago',
                'total' => $totalAEnviar,
            ];

            Log::info('Enviando clientes a n8n', [
                'total' => $totalAEnviar,
                'enviados_hoy' => $this->enviadosHoy,
                'limite_restante' => $limiteRestante,
                'enviados_mes' => $this->mensajesEnviadosEsteMes,
            ]);

            $response = Http::timeout(60)
                ->retry(3, 100)
                ->post($this->webhookUrl, $datosParaN8N);

            if ($response->successful()) {
                // Guardar log SOLO con cliente_id
                if (Schema::hasTable('envio_masivo_logs')) {
                    foreach ($clientesDisponibles as $cliente) {
                        if (isset($cliente['cliente_id']) && $cliente['cliente_id']) {
                            EnvioMasivoLog::create([
                                'cliente_id' => $cliente['cliente_id'],
                                'estado' => 'enviado',
                                'plantilla_usada' => 'recordatorio_pago',
                                'fecha_envio' => now(),
                                'ip_cliente' => request()->ip(),
                            ]);
                        }
                    }
                }

                $this->clientesEnviados = $totalAEnviar;
                $this->progreso = 100;

                $this->dispatch(
                    'notify',
                    type: 'success',
                    message: "✅ Datos enviados a n8n. Total: {$totalAEnviar} clientes."
                );

                // Recargar contadores
                $this->contarClientesPendientes();
                $this->verificarEnvioDiario();
            } else {
                throw new \Exception("Error en n8n: " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("Error en envío masivo: " . $e->getMessage());

            $this->dispatch(
                'notify',
                type: 'error',
                message: "❌ Error: " . $e->getMessage()
            );
        } finally {
            $this->procesando = false;
        }
    }

    /**
     * Envía prueba de un cliente específico a n8n
     */
    public function enviarPrueba($index)
    {
        $this->procesando = true;

        try {
            $cliente = $this->clientesPendientes[$index] ?? null;

            if (!$cliente) {
                throw new \Exception("Cliente no encontrado en la lista");
            }

            // Verificar si ya recibió recordatorio este mes
            if (isset($cliente['cliente_id']) && $cliente['cliente_id']) {
                if ($this->clienteYaRecibioMensajeEsteMes($cliente['cliente_id'])) {
                    $this->dispatch(
                        'notify',
                        type: 'warning',
                        message: "⚠️ Este cliente ya recibió recordatorio este mes."
                    );
                    $this->procesando = false;
                    return;
                }
            }

            // 📦 Enviar SOLO este cliente al webhook principal
            $datosPrueba = [
                'clientes' => [
                    [
                        'nombre' => $cliente['nombre'],
                        'telefono' => $cliente['telefono'],
                        'monto' => $cliente['monto'],
                    ]
                ],
                'plantilla' => 'recordatorio_pago',
                'total' => 1,
                'test' => true,
            ];

            Log::info('Enviando prueba a n8n', [
                'cliente' => $cliente['nombre'],
                'telefono' => $cliente['telefono'],
            ]);

            $response = Http::timeout(30)
                ->post($this->webhookUrl, $datosPrueba);

            if (!$response->successful()) {
                throw new \Exception("Error HTTP: " . $response->status() . " - " . $response->body());
            }

            // Guardar log de prueba
            if (Schema::hasTable('envio_masivo_logs') && isset($cliente['cliente_id']) && $cliente['cliente_id']) {
                EnvioMasivoLog::create([
                    'cliente_id' => $cliente['cliente_id'],
                    'estado' => 'enviado',
                    'plantilla_usada' => 'recordatorio_pago',
                    'fecha_envio' => now(),
                    'ip_cliente' => request()->ip(),
                    'response_data' => ['test' => true],
                ]);
            }

            $this->dispatch(
                'notify',
                type: 'success',
                message: "✅ Prueba enviada para: {$cliente['nombre']}"
            );

            // Recargar contadores
            $this->contarClientesPendientes();
            $this->verificarEnvioDiario();
        } catch (\Exception $e) {
            Log::error("Error en prueba: " . $e->getMessage());
            $this->dispatch(
                'notify',
                type: 'error',
                message: "❌ Error en prueba: " . $e->getMessage()
            );
        } finally {
            $this->procesando = false;
        }
    }

    /**
     * Resetea el contador mensual (solo para administradores)
     */
    public function resetearContadorMensual()
    {
        // Verificar si es administrador
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            $this->dispatch('notify', type: 'error', message: 'No tienes permiso para esta acción.');
            return;
        }

        $inicioMes = Carbon::now()->startOfMonth();

        $eliminados = EnvioMasivoLog::where('estado', 'enviado')
            ->where('fecha_envio', '>=', $inicioMes)
            ->delete();

        $this->dispatch(
            'notify',
            type: 'success',
            message: "✅ Contador mensual reseteado. {$eliminados} registros eliminados."
        );

        $this->contarClientesPendientes();
        $this->verificarEnvioDiario();
    }

    /**
     * Obtiene estadísticas del mes actual
     */
    public function getEstadisticasMesProperty()
    {
        $inicioMes = Carbon::now()->startOfMonth();

        if (!Schema::hasTable('envio_masivo_logs')) {
            return [
                'total' => 0,
                'clientes_unicos' => 0,
                'recordatorios' => 0,
                'comprobantes' => 0,
            ];
        }

        return [
            'total' => EnvioMasivoLog::where('estado', 'enviado')
                ->where('fecha_envio', '>=', $inicioMes)
                ->count(),
            'clientes_unicos' => EnvioMasivoLog::where('estado', 'enviado')
                ->where('fecha_envio', '>=', $inicioMes)
                ->distinct('cliente_id')
                ->count('cliente_id'),
            'recordatorios' => EnvioMasivoLog::where('estado', 'enviado')
                ->where('plantilla_usada', 'recordatorio_pago')
                ->where('fecha_envio', '>=', $inicioMes)
                ->count(),
            'comprobantes' => EnvioMasivoLog::where('estado', 'enviado')
                ->where('plantilla_usada', 'comprobante_pago')
                ->where('fecha_envio', '>=', $inicioMes)
                ->count(),
        ];
    }

    public function render()
    {
        $estadisticasMes = $this->getEstadisticasMesProperty();

        return view('livewire.conversaciones.envio-masivo-whats-app', [
            'clientes' => $this->clientesPendientes,
            'total' => $this->totalClientes,
            'enviadosHoy' => $this->enviadosHoy,
            'pendientes' => $this->pendientesPorEnviar,
            'fechaUltimoEnvio' => $this->fechaUltimoEnvio,
            'limiteDiario' => $this->limiteDiario,
            'estadisticasMes' => $estadisticasMes,
            'mensajesEnviadosEsteMes' => $this->mensajesEnviadosEsteMes,
        ]);
    }
}
