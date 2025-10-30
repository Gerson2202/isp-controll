<?php

namespace App\Livewire\Tecnico;

use App\Models\ConsumibleMovimiento;
use Livewire\Component;
use App\Models\Inventario;
use App\Models\ConsumibleStock;
use App\Models\MovimientoInventario;

class Bodega extends Component
{
    public $buscarEquipo = '';
    public $buscarConsumible = '';
    public $searchMovimientos = '';
    public $activeTab = 'equipos';

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $usuario = auth()->user();

        // === EQUIPOS ===
        $equipos = Inventario::with('modelo')
            ->where('user_id', $usuario->id)
            ->where(function ($query) {
                $query->where('mac', 'like', "%{$this->buscarEquipo}%")
                    ->orWhere('serial', 'like', "%{$this->buscarEquipo}%")
                    ->orWhereHas('modelo', function ($q) {
                        $q->where('nombre', 'like', "%{$this->buscarEquipo}%");
                    });
            })
            ->paginate(5, ['*'], 'equipos_page');

        // === CONSUMIBLES ===
        $consumibles = ConsumibleStock::with('consumible')
            ->where('usuario_id', $usuario->id)
            ->whereHas('consumible', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscarConsumible}%");
            })
            ->paginate(5, ['*'], 'consumibles_page');

        // === MOVIMIENTOS DE CONSUMIBLES ===
        $movimientosConsumibles = ConsumibleMovimiento::with(['consumible', 'usuario'])
            ->where(function ($q) use ($usuario) {
                $q->where(function ($q2) use ($usuario) {
                    $q2->where('origen_tipo', 'usuario')
                        ->where('origen_id', $usuario->id);
                })->orWhere(function ($q3) use ($usuario) {
                    $q3->where('destino_tipo', 'usuario')
                        ->where('destino_id', $usuario->id);
                });
            })
            ->whereHas('consumible', function ($q) {
                $q->where('nombre', 'like', "%{$this->searchMovimientos}%")
                    ->orWhere('descripcion', 'like', "%{$this->searchMovimientos}%")
                    ->orWhere('unidad', 'like', "%{$this->searchMovimientos}%");
            })
            ->latest()
            ->paginate(5, ['*'], 'movconsumibles_page');

        // === MOVIMIENTOS DE INVENTARIO ===
        $movimientosInventario = MovimientoInventario::with([
            'inventario.modelo',
            'usuarioAccion',
            'userAnterior', 'bodegaAnterior', 'clienteAnterior', 'nodoAnterior', 'visitaAnterior',
            'userNuevo', 'bodegaNueva', 'clienteNuevo', 'nodoNuevo', 'visitaNueva',
        ])
            ->where(function ($q) use ($usuario) {
                $q->where('user_anterior_id', $usuario->id)
                    ->orWhere('user_nuevo_id', $usuario->id);
            })
            ->whereHas('inventario', function ($q) {
                $q->where('mac', 'like', "%{$this->searchMovimientos}%")
                    ->orWhere('serial', 'like', "%{$this->searchMovimientos}%")
                    ->orWhereHas('modelo', function ($q2) {
                        $q2->where('nombre', 'like', "%{$this->searchMovimientos}%");
                    });
            })
            ->latest()
            ->paginate(5, ['*'], 'movinventario_page');

        return view('livewire.tecnico.bodega', compact(
            'equipos',
            'consumibles',
            'movimientosConsumibles',
            'movimientosInventario'
        ));
    }
}
