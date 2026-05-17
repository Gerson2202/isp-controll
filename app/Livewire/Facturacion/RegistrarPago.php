<?php

namespace App\Livewire\Facturacion;

use App\Models\Empresa;
use Illuminate\Support\Facades\Http;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
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
    public $empresa; // ✅ Del segundo archivo

    public function mount() // ✅ Del segundo archivo
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

            $this->dispatch(
                'notify',
                type: 'success',
                message: $mensaje
            );
            $this->mostrarComprobante = true;

            // Crear carpeta si no existe
            $rutaRelativa = 'comprobantes/pago-' . $this->pagoRegistrado->id . '.png';
            $rutaCompleta = public_path($rutaRelativa);
            $directorio = dirname($rutaCompleta);

            if (!file_exists($directorio)) {
                mkdir($directorio, 0777, true);
            }

            // Generar la imagen con altura suficiente
            Browsershot::html(
                view('comprobantes.pago', [
                    'facturaSeleccionada' => $this->facturaSeleccionada,
                    'pagoRegistrado' => $this->pagoRegistrado,
                    'usuario' => auth()->user()->name,
                    'paraImagen' => true,
                    'empresa' => $this->empresa
                ])->render()
            )
                ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
                ->windowSize(550, 1000)  // Ancho fijo, altura grande
                ->fullPage()              // Captura todo el scroll
                ->waitUntilNetworkIdle()  // Espera a que cargue todo
                ->save($rutaCompleta);

            // ✅ Verificar que la imagen se creó antes de leerla
            if (!file_exists($rutaCompleta)) {
                throw new Exception('No se pudo generar la imagen del comprobante');
            }

            $imagenData = file_get_contents($rutaCompleta);
            $imagenBase64 = 'data:image/png;base64,' . base64_encode($imagenData);

            // Enviar a n8n
            $response = Http::post('http://localhost:5678/webhook-test/pago-factura', [
                'cliente' => $this->facturaSeleccionada->contrato->cliente->nombre ?? '',
                'telefono' => $this->facturaSeleccionada->contrato->cliente->telefono ?? '',
                'factura' => $this->facturaSeleccionada->numero_factura,
                'monto' => $this->pagoRegistrado->monto,
                'metodo_pago' => $this->pagoRegistrado->metodo_pago,
                'fecha_pago' => $this->pagoRegistrado->fecha_pago,
                'usuario' => auth()->user()->name,
                'imagen' => $imagenBase64
            ]);

            // ✅ Opcional: Log para depurar
            \Log::info('Respuesta de n8n:', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

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

    public function render()
    {
        return view('livewire.facturacion.registrar-pago', [
            'facturas' => $this->facturas
        ]);
    }
}
