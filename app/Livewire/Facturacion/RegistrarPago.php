<?php

namespace App\Livewire\Facturacion;

use App\Services\ComprobanteImageGenerator;
use App\Models\Empresa;
use App\Models\EnvioMasivoLog; // 1. Agregar este modelo
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use App\Models\SaldoAcumulado;
use Illuminate\Support\Facades\Log; // 2. Agregar esta facade
use Carbon\Carbon;
use Exception;

class RegistrarPago extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $facturaSeleccionada;
    public $monto;
    public $metodo_pago = 'efectivo';
    public $fecha_pago;
    public $pagoRegistrado = null;
    public $mostrarComprobante = false;
    public $empresa;
    // Propiedades nuevas para controlar el modal de edición de precio
    public $mostrarModalEditar = false;
    public $nuevo_monto;

    public function mount()
    {
        $this->empresa = Empresa::first();
    }

    public function seleccionarFactura($facturaId)
    {
        $this->facturaSeleccionada = Factura::with('contrato.cliente')->find($facturaId);
        $this->monto = $this->facturaSeleccionada->saldo_pendiente;
        $this->fecha_pago = now()->format('Y-m-d');
        $this->mostrarComprobante = false;
        $this->pagoRegistrado = null;
    }

    public function cerrarModal()
    {
        $this->facturaSeleccionada = null;
        $this->reset(['monto', 'metodo_pago', 'fecha_pago']);
        $this->mostrarComprobante = false;
        $this->pagoRegistrado = null;
    }

    private function formatearTelefono($telefono)
    {
        $telefono = preg_replace('/\D+/', '', $telefono);

        if (str_starts_with($telefono, '0057')) {
            $telefono = substr($telefono, 4);
        }

        if (strlen($telefono) === 10) {
            $telefono = '57' . $telefono;
        }

        return substr($telefono, 0, 12);
    }

    public function registrarPago()
    {
        try {
            if (!$this->facturaSeleccionada) {
                throw new Exception('No se ha seleccionado ninguna factura');
            }

            $this->validate([
                'monto' => [
                    'required',
                    'numeric',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        if ($value > $this->facturaSeleccionada->saldo_pendiente) {
                            $message = 'El monto no puede ser mayor al saldo pendiente ($' . number_format($this->facturaSeleccionada->saldo_pendiente, 2) . ')';
                            $this->dispatch(
                                'notify',
                                type: 'error',
                                message: $message
                            );
                            $fail($message);
                        }
                    }
                ],
                'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta',
                'fecha_pago' => 'required|date|before_or_equal:today'
            ]);

            $mensaje = 'Pago registrado exitosamente';

            DB::transaction(function () use (&$mensaje) {
                $this->pagoRegistrado = Pago::create([
                    'factura_id' => $this->facturaSeleccionada->id,
                    'monto' => $this->monto,
                    'metodo_pago' => $this->metodo_pago,
                    'fecha_pago' => $this->fecha_pago,
                    'notas' => 'Pago registrado por: ' . auth()->user()->name,
                    'user_id' => auth()->id()
                ]);

                $this->facturaSeleccionada->saldo_pendiente -= $this->monto;

                if ($this->facturaSeleccionada->saldo_pendiente <= 0) {
                    $this->facturaSeleccionada->estado = 'pagada';
                    $this->facturaSeleccionada->save();

                    $contrato = $this->facturaSeleccionada->contrato;
                    $cliente = $contrato->cliente ?? null;

                    if ($cliente && !empty($cliente->ip)) {
                        $estadoAnterior = $cliente->estado;

                        if ($estadoAnterior == 'cortado') {
                            $nuevoEstado = 'activo';

                            $cliente->update(['estado' => $nuevoEstado]);

                            $situacionTexto = "Estado cambiado de {$estadoAnterior} a {$nuevoEstado}. Usuario: " . auth()->user()->name;

                            Ticket::create([
                                'tipo_reporte' => 'cambio de estado',
                                'situacion' => $situacionTexto,
                                'estado' => 'cerrado',
                                'fecha_cierre' => now(),
                                'cliente_id' => $cliente->id,
                                'solucion' => 'Estado actualizado tras pago de factura',
                                'user_id' => auth()->id()
                            ]);

                            $nodo = $contrato->plan->nodo;

                            $mikroTikService = new MikroTikService(
                                $nodo->ip,
                                $nodo->user,
                                $nodo->pass,
                                $nodo->puerto_api ?? 8728
                            );

                            if (!$mikroTikService->isReachable()) {
                                throw new \Exception("No se pudo conectar al MikroTik");
                            }

                            $mikroTikService->manejarEstadoCliente($cliente->ip, $nuevoEstado);

                            $mensaje = 'Pago realizado exitosamente y cliente activado en MikroTik';
                        } else {
                            $mensaje = 'Pago registrado exitosamente';
                        }
                    } else {
                        $mensaje = 'Pago registrado exitosamente (pero el cliente no tiene IP para activar en MikroTik)';
                    }
                } else {
                    $this->facturaSeleccionada->save();
                    $mensaje = 'Pago registrado exitosamente';
                }
            });

            // Actualizar saldo acumulado
            $fecha = Carbon::parse($this->fecha_pago);
            SaldoAcumulado::recalcularMes($fecha->year, $fecha->month);

            $this->dispatch(
                'notify',
                type: 'success',
                message: $mensaje
            );
            $this->mostrarComprobante = true;

            // Generar imagen con GD Library
            $rutaRelativa = 'comprobantes/pago-' . $this->pagoRegistrado->id . '.png';
            $rutaCompleta = public_path($rutaRelativa);
            $directorio = dirname($rutaCompleta);

            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            $generador = new ComprobanteImageGenerator();
            $generador->generate(
                $this->facturaSeleccionada,
                $this->pagoRegistrado,
                $this->empresa,
                $rutaCompleta
            );

            // Convertir a Base64 para enviar a n8n
            $imagenData = file_get_contents($rutaCompleta);
            $imagenBase64 = 'data:image/png;base64,' . base64_encode($imagenData);

            // 3. Enviar a n8n capturando la respuesta
            $response = Http::timeout(60)->post(
                'https://automatizacion-isprotik1-n8n.ijnhto.easypanel.host/webhook/pago-factura-fernet',
                [
                    'cliente' => $this->facturaSeleccionada->contrato->cliente->nombre ?? '',
                    'telefono' => $this->formatearTelefono(
                        $this->facturaSeleccionada->contrato->cliente->telefono ?? ''
                    ),
                    'factura' => $this->facturaSeleccionada->numero_factura,
                    'monto' => number_format($this->pagoRegistrado->monto, 0, ',', '.'),
                    'metodo_pago' => $this->pagoRegistrado->metodo_pago,
                    'fecha_pago' => $this->pagoRegistrado->fecha_pago,
                    'usuario' => auth()->user()->name,
                    'imagen' => $imagenBase64
                ]
            );

            // 4. Registrar en envio_masivo_logs y laravel.log
            $cliente = $this->facturaSeleccionada->contrato->cliente;

            if ($response->successful()) {
                EnvioMasivoLog::create([
                    'cliente_id' => $cliente->id,
                    'factura_id' => $this->facturaSeleccionada->id,
                    'estado' => 'enviado',
                    'plantilla_usada' => 'comprobante_pago',
                    'tipo_envio' => 'comprobante',
                    'fecha_envio' => now(),
                    'ip_cliente' => request()->ip(),
                    'response_data' => [
                        'factura_id' => $this->facturaSeleccionada->id,
                        'monto' => $this->pagoRegistrado->monto,
                        'metodo_pago' => $this->pagoRegistrado->metodo_pago,
                        'fecha_pago' => $this->pagoRegistrado->fecha_pago,
                        'n8n_response' => $response->json(),
                    ],
                ]);

                Log::info('Comprobante enviado y registrado en log', [
                    'cliente_id' => $cliente->id,
                    'factura_id' => $this->facturaSeleccionada->id,
                ]);
            } else {
                EnvioMasivoLog::create([
                    'cliente_id' => $cliente->id,
                    'factura_id' => $this->facturaSeleccionada->id,
                    'estado' => 'error',
                    'plantilla_usada' => 'comprobante_pago',
                    'tipo_envio' => 'comprobante',
                    'fecha_envio' => now(),
                    'ip_cliente' => request()->ip(),
                    'error_message' => 'Error al enviar comprobante: ' . $response->status(),
                    'response_data' => [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ],
                ]);

                Log::error('Error al enviar comprobante a n8n', [
                    'cliente_id' => $cliente->id,
                    'status' => $response->status(),
                ]);
            }

            $this->reset(['monto', 'metodo_pago', 'fecha_pago']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error: ' . $e->getMessage()
            );
        }
    }

    public function cerrarComprobante()
    {
        $this->mostrarComprobante = false;
        $this->facturaSeleccionada = null;
        $this->pagoRegistrado = null;
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getFacturasProperty()
    {
        return Factura::where('estado', 'pendiente')
            ->whereHas('contrato', function ($q) {
                $q->where('estado', 'activo')
                    ->whereHas('cliente', function ($subQ) {
                        $subQ->whereNull('deleted_at');
                    });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('numero_factura', 'like', '%' . $this->search . '%')
                        ->orWhereHas('contrato.cliente', function ($subQuery) {
                            $subQuery->where('nombre', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->with('contrato.cliente')
            ->paginate(10);
    }
    // ----funciones al agregar la opcion editar ---
    // Método para disparar el botón de la tabla
    public function editarFacturaDesdeTabla($facturaId)
    {
        $this->facturaSeleccionada = Factura::with('contrato.cliente')->find($facturaId);

        if (!$this->facturaSeleccionada) {
            $this->dispatch('notify', type: 'error', message: 'La factura no existe.');
            return;
        }

        $this->nuevo_monto = $this->facturaSeleccionada->monto_total;
        $this->mostrarModalEditar = true;
    }

    public function cerrarModalEditar()
    {
        $this->mostrarModalEditar = false;
        $this->facturaSeleccionada = null; // <-- Añade esta línea para limpiar la selección
        $this->reset(['nuevo_monto']);
    }

    public function guardarMontoEditado()
    {
        try {
            if (!$this->facturaSeleccionada) {
                throw new Exception('No hay ninguna factura seleccionada.');
            }

            // 1. Validar que la factura NO tenga pagos registrados
            if ($this->facturaSeleccionada->pagos()->exists()) {
                throw new Exception('No se puede editar el monto porque esta factura ya cuenta con pagos o abonos registrados.');
            }

            $this->validate([
                'nuevo_monto' => 'required|numeric|min:1'
            ]);

            $contrato = $this->facturaSeleccionada->contrato;

            if (!$contrato) {
                throw new Exception('La factura no tiene un contrato asociado.');
            }

            // 2. Validar que coincida con el precio oficial del contrato
            if ((float)$this->nuevo_monto !== (float)$contrato->precio) {
                throw new Exception("El valor ingresado no coincide con la tarifa del contrato (\$" . number_format($contrato->precio, 2) . "). Debe actualizar primero el contrato del cliente antes de editar la factura.");
            }

            $montoAnterior = $this->facturaSeleccionada->monto_total;

            DB::transaction(function () use ($montoAnterior) {
                $this->facturaSeleccionada->update([
                    'monto_total' => $this->nuevo_monto,
                    'saldo_pendiente' => $this->nuevo_monto
                ]);

                // Registrar auditoría en Tickets
                $cliente = $this->facturaSeleccionada->contrato->cliente ?? null;

                Ticket::create([
                    'tipo_reporte' => 'modificacion_precio',
                    'situacion' => "Monto de la Factura N° {$this->facturaSeleccionada->numero_factura} modificado de $" . number_format($montoAnterior, 2) . " a $" . number_format($this->nuevo_monto, 2) . " por usuario: " . auth()->user()->name,
                    'estado' => 'cerrado',
                    'fecha_cierre' => now(),
                    'cliente_id' => $cliente ? $cliente->id : null,
                    'solucion' => 'Precio actualizado tras verificación con el contrato',
                    'user_id' => auth()->id()
                ]);

                // Recalcular saldo acumulado del mes
                if ($this->facturaSeleccionada->fecha_emision) {
                    $fecha = Carbon::parse($this->facturaSeleccionada->fecha_emision);
                    SaldoAcumulado::recalcularMes($fecha->year, $fecha->month);
                }
            });

            $this->cerrarModalEditar();

            $this->dispatch('notify', type: 'success', message: 'Monto de la factura actualizado correctamente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.facturacion.registrar-pago', [
            'facturas' => $this->facturas
        ]);
    }
}
