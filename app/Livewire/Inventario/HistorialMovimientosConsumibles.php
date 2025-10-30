<?php

namespace App\Livewire\Inventario;
use Livewire\WithPagination;
use App\Models\ConsumibleMovimiento;
use Livewire\Component;

class HistorialMovimientosConsumibles extends Component
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
        $movimientos = ConsumibleMovimiento::with([
                'consumible',
                'usuario'
            ])
            ->when($this->search, function($query) {
                $query->whereHas('consumible', function($q) {
                    $q->where('nombre', 'LIKE', '%' . $this->search . '%')
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
            ->paginate(15);

        return view('livewire.inventario.historial-movimientos-consumibles', [
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
            default => 'secondary'
        };
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'fecha_inicio', 'fecha_fin', 'tipo_movimiento']);
        $this->resetPage();
    }   
}
