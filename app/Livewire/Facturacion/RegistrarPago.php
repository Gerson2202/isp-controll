<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use Livewire\WithPagination; // Agregar este trait
use App\Models\Factura;
use App\Models\Pago;
use Illuminate\Support\Facades\DB; // Importación necesaria
use Exception;

class RegistrarPago extends Component
{
    use WithPagination; // Usar el trait de paginación
    
    protected $paginationTheme = 'bootstrap'; // Opcional: si usas Bootstrap
    
    public $search = '';
    public $facturaSeleccionada;
    public $monto;
    public $metodo_pago = 'efectivo';
    public $fecha_pago;

    public function seleccionarFactura($facturaId)
    {
        $this->facturaSeleccionada = Factura::with('contrato.cliente')->find($facturaId);
        $this->monto = $this->facturaSeleccionada->saldo_pendiente;
        $this->fecha_pago = now()->format('Y-m-d');
    }

    public function cerrarModal()
    {
        $this->facturaSeleccionada = null;
        $this->reset(['monto', 'metodo_pago', 'fecha_pago']);
    }

    public function registrarPago()
    {
        try {
            // Validación adicional
            if (!$this->facturaSeleccionada) {
                throw new Exception('No se ha seleccionado ninguna factura');
            }

            // Validación de campos
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

            // Transacción de base de datos
            DB::transaction(function () {
                // Registrar el pago
                $pago = Pago::create([
                    'factura_id' => $this->facturaSeleccionada->id,
                    'monto' => $this->monto,
                    'metodo_pago' => $this->metodo_pago,
                    'fecha_pago' => $this->fecha_pago
                ]);

                // Actualizar factura
                $this->facturaSeleccionada->saldo_pendiente -= $this->monto;
                
                if ($this->facturaSeleccionada->saldo_pendiente <= 0) {
                    $this->facturaSeleccionada->estado = 'pagada';
                }
                
                $this->facturaSeleccionada->save();
            });

            // Reset y notificación
            $this->reset(['facturaSeleccionada', 'monto', 'metodo_pago', 'fecha_pago']);
            $this->resetPage();
            
            $this->dispatch('notify', 
                type: 'success', 
                message: 'Pago registrado correctamente'
            );

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Las validaciones de Laravel ya manejan esto
            throw $e;
            
        } catch (Exception $e) {
            $this->dispatch('notify', 
                type: 'error', 
                message: 'Error: ' . $e->getMessage()
            );
        }
    }


    public function updatingSearch()
    {
        $this->resetPage(); // Resetear la paginación cuando se cambia la búsqueda
    }

    public function getFacturasProperty()
    {
        return Factura::where('estado', 'pendiente') // Este filtro siempre se aplica
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