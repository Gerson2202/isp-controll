<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\Ticket;
use App\Services\MikroTikService;
use Illuminate\Support\Facades\DB;
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
                            $message = 'El monto no puede ser mayor al saldo pendiente ($'.number_format($this->facturaSeleccionada->saldo_pendiente, 2).')';
                            $this->dispatch('notify', 
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

            $mensaje = 'Pago registrado exitosamente'; // Mensaje por defecto

            DB::transaction(function () use (&$mensaje) {
                $this->pagoRegistrado = Pago::create([
                    'factura_id' => $this->facturaSeleccionada->id,
                    'monto' => $this->monto,
                    'metodo_pago' => $this->metodo_pago,
                    'fecha_pago' => $this->fecha_pago,
                    'notas' => 'Pago registrado por: ' . auth()->user()->name
                ]);

                $this->facturaSeleccionada->saldo_pendiente -= $this->monto;

                if ($this->facturaSeleccionada->saldo_pendiente <= 0) {
                    $this->facturaSeleccionada->estado = 'pagada';
                    $this->facturaSeleccionada->save();

                    // Cambiar estado del cliente en MikroTik si corresponde
                    $contrato = $this->facturaSeleccionada->contrato;
                    $cliente = $contrato->cliente ?? null;

                    if ($cliente && !empty($cliente->ip)) {
                        $estadoAnterior = $cliente->estado;

                        // Si el cliente estaba cortado, lo activamos y notificamos
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
                            // Cliente ya estaba activo
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

            $this->dispatch('notify', 
                type: 'success', 
                message: $mensaje
            );
            $this->mostrarComprobante = true;
            $this->reset(['monto', 'metodo_pago', 'fecha_pago']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', 
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
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('numero_factura', 'like', '%'.$this->search.'%')
                    ->orWhereHas('contrato.cliente', function($subQuery) {
                        $subQuery->where('nombre', 'like', '%'.$this->search.'%');
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