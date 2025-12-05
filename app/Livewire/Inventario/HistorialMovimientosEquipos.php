<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MovimientoInventario;

class HistorialMovimientosEquipos extends Component
{
     use WithPagination;

    public $search = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $tipo_movimiento = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'fecha_inicio' => ['except' => ''],
        'fecha_fin' => ['except' => ''],
        'tipo_movimiento' => ['except' => '']
    ];

    public function render()
    {
        $movimientos = MovimientoInventario::with([
                'inventario.modelo',
                'usuario',
                'bodegaAnterior',
                'bodegaNueva',
                'userAnterior',
                'userNuevo',
                'nodoAnterior',
                'nodoNuevo',
                'clienteAnterior',
                'clienteNuevo'
            ])
            ->when($this->search, function($query) {
                $query->whereHas('inventario', function($q) {
                    $q->where('mac', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('serial', 'LIKE', '%' . $this->search . '%')
                      ->orWhere('descripcion', 'LIKE', '%' . $this->search . '%');
                });
            })
            ->when($this->tipo_movimiento, function($query) {
                $query->where('tipo_movimiento', $this->tipo_movimiento);
            })
            ->when($this->fecha_inicio, function($query) {
                $query->whereDate('created_at', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function($query) {
                $query->whereDate('created_at', '<=', $this->fecha_fin);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.inventario.historial-movimientos-equipos', [
            'movimientos' => $movimientos
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFechaInicio()
    {
        $this->resetPage();
    }

    public function updatingFechaFin()
    {
        $this->resetPage();
    }

    public function updatingTipoMovimiento()
    {
        $this->resetPage();
    }

    public function getBadgeColor($tipo)
    {
        return match($tipo) {
            'entrada' => 'success',
            'salida' => 'danger',
            'traslado' => 'warning',
            'asignacion' => 'info',
            default => 'secondary'
        };
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'fecha_inicio', 'fecha_fin', 'tipo_movimiento']);
        $this->resetPage();
    }
}
