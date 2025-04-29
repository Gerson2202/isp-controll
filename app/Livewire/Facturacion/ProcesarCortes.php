<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use App\Models\Cliente;
use App\Models\Factura;
use App\Models\HistorialCorte;

class ProcesarCortes extends Component
{
    public $diasVencimiento = 30;
    public $enProceso = false;
    public $resultado = [];
    public $fechaCorte;
    public $montoReconexion = 5.00;
    
    public function mount()
    {
        $this->fechaCorte = now()->format('Y-m-d');
    }
    
    public function procesarCortes()
    {
        $this->validate([
            'diasVencimiento' => 'required|numeric|min:1',
            'fechaCorte' => 'required|date',
        ]);
        
        $this->enProceso = true;
        $this->resultado = [];
        
        $fechaLimite = now()->subDays($this->diasVencimiento);
        
        $clientes = Cliente::whereHas('facturas', function($query) use ($fechaLimite) {
            $query->where('estado', 'vencida')
                  ->where('fecha_vencimiento', '<=', $fechaLimite);
        })->get();
        
        foreach ($clientes as $cliente) {
            try {
                // Registrar en historial de cortes
                $corte = HistorialCorte::create([
                    'cliente_id' => $cliente->id,
                    'fecha_corte' => $this->fechaCorte,
                    'notas' => 'Corte automático por factura vencida',
                ]);
                
                // Cambiar estado del cliente
                $cliente->update(['estado' => 'suspendido']);
                
                $this->resultado[] = [
                    'cliente' => $cliente->nombre,
                    'estado' => 'exito',
                    'mensaje' => 'Servicio suspendido'
                ];
                
            } catch (\Exception $e) {
                $this->resultado[] = [
                    'cliente' => $cliente->nombre,
                    'estado' => 'error',
                    'mensaje' => 'Error: ' . $e->getMessage()
                ];
            }
        }
        
        $this->enProceso = false;
    }
   
    public function render()
    {
        $cantidadClientes = Cliente::whereHas('facturas', function($query) {
            $query->where('estado', 'vencida')
                  ->where('fecha_vencimiento', '<=', now()->subDays($this->diasVencimiento));
        })->count();
        
        return view('livewire.facturacion.procesar-cortes');
    }
}
