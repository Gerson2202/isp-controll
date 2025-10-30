<?php

namespace App\Livewire\Tecnico\Bodega;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ConsumibleMovimiento;
use App\Models\MovimientoInventario;

class Historial extends Component
{
    use WithPagination;

    public $buscarEquipos = '';
    public $buscarConsumibles = '';

    protected $paginationTheme = 'bootstrap';

    // Nombres de las páginas para que no se mezclen
    public string $equiposPageName = 'equiposPage';
    public string $consumiblesPageName = 'consumiblesPage';

    // Resetear paginación al cambiar búsqueda
    public function updatingBuscarEquipos()
    {
        $this->resetPage($this->equiposPageName);
    }

    public function updatingBuscarConsumibles()
    {
        $this->resetPage($this->consumiblesPageName);
    }

    public function render()
    {
        $usuario = auth()->user();

        // ================== EQUIPOS ==================
        $movimientosInventario = MovimientoInventario::with([
            'inventario.modelo',
            'usuarioAccion',
            'userAnterior',
            'bodegaAnterior',
            'clienteAnterior',
            'nodoAnterior',
            'visitaAnterior',
            'userNuevo',
            'bodegaNueva',
            'clienteNuevo',
            'nodoNuevo',
            'visitaNueva',
        ])
            ->where(function ($q) use ($usuario) {
                $q->where('user_anterior_id', $usuario->id)
                  ->orWhere('user_nuevo_id', $usuario->id);
            })
            ->where(function ($q) {
                $q->whereHas('inventario', function ($qi) {
                    $qi->where(function ($sub) {
                        $sub->where('mac', 'like', "%{$this->buscarEquipos}%")
                            ->orWhere('serial', 'like', "%{$this->buscarEquipos}%")
                            ->orWhereHas('modelo', function ($qm) {
                                $qm->where('nombre', 'like', "%{$this->buscarEquipos}%");
                            });
                    });
                });
            })
            ->latest()
            ->paginate(5, ['*'], $this->equiposPageName);

        // ================== CONSUMIBLES ==================
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
            ->whereHas('consumible', function ($qc) {
                $qc->where('nombre', 'like', "%{$this->buscarConsumibles}%")
                    ->orWhere('descripcion', 'like', "%{$this->buscarConsumibles}%")
                    ->orWhere('unidad', 'like', "%{$this->buscarConsumibles}%");
            })
            ->latest()
            ->paginate(5, ['*'], $this->consumiblesPageName);

        return view('livewire.tecnico.bodega.historial', [
            'movimientosInventario' => $movimientosInventario,
            'movimientosConsumibles' => $movimientosConsumibles,
        ]);
    }
}
