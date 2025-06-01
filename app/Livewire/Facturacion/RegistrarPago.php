<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Factura;
use App\Models\Pago;
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

            DB::transaction(function () {
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
                }
                
                $this->facturaSeleccionada->save();
            });
            $this->dispatch('notify', 
            type: 'success', 
            message: 'Pago regitrado exitosamente'
        );
            $this->mostrarComprobante = true;
            $this->reset(['monto', 'metodo_pago', 'fecha_pago']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
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