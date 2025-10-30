<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Nodo;
use App\Models\User;
use App\Models\ConsumibleStock;
use App\Models\Inventario;

class DetalleInventario extends Component
{
    use WithPagination;

    public $tipo;
    public $id;
    public $entidad;
    // ELIMINA estas propiedades
    // public $searchConsumible = '';
    // public $searchEquipo = '';

    public function mount($tipo = null, $id = null)
    {
        $this->tipo = $tipo;
        $this->id = $id;
        $this->cargarEntidad();
    }

    protected function cargarEntidad()
    {
        if (!$this->tipo || !$this->id) {
            return;
        }

        $this->entidad = match($this->tipo) {
            'bodega' => Bodega::find($this->id),
            'cliente' => Cliente::find($this->id),
            'nodo' => Nodo::find($this->id),
            'usuario' => User::find($this->id),
            default => null
        };

        if (!$this->entidad) {
            abort(404, ucfirst($this->tipo) . " no encontrado");
        }
    }

    public function render()
    {   
        $this->cargarEntidad();

        // Consulta para los stocks de consumibles (SIN filtro)
        $stocks = ConsumibleStock::with('consumible')
            ->where(function($q) {
                match($this->tipo) {
                    'bodega' => $q->where('bodega_id', $this->id),
                    'cliente' => $q->where('cliente_id', $this->id),
                    'nodo' => $q->where('nodo_id', $this->id),
                    'usuario' => $q->where('usuario_id', $this->id),
                    default => $q
                };
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Consulta para equipos (SIN filtro)
        $equipos = Inventario::with(['modelo', 'movimientos' => function($q) {
                $q->orderBy('created_at', 'desc')->take(1);
            }])
            ->where(function($q) {
                match($this->tipo) {
                    'bodega' => $q->where('bodega_id', $this->id),
                    'cliente' => $q->where('cliente_id', $this->id),
                    'nodo' => $q->where('nodo_id', $this->id),
                    'usuario' => $q->where('user_id', $this->id),
                    default => $q
                };
            })
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Título y totales (igual que antes)
        $titulo = $this->entidad ? match($this->tipo) {
            'bodega' => 'Bodega: ' . $this->entidad->nombre,
            'cliente' => 'Cliente: ' . $this->entidad->nombre,
            'nodo' => 'Nodo: ' . $this->entidad->nombre,
            'usuario' => 'Usuario: ' . $this->entidad->name,
            default => 'Inventario'
        } : 'Cargando...';

        $totalItems = $this->tipo && $this->id ? ConsumibleStock::where(function($q) {
            match($this->tipo) {
                'bodega' => $q->where('bodega_id', $this->id),
                'cliente' => $q->where('cliente_id', $this->id),
                'nodo' => $q->where('nodo_id', $this->id),
                'usuario' => $q->where('usuario_id', $this->id),
                default => $q
            };
        })->sum('cantidad') : 0;

        $totalProductos = $stocks->total();
        $totalEquipos = $equipos->total();

        return view('livewire.inventario.detalle-inventario', [
            'stocks' => $stocks,
            'equipos' => $equipos,
            'titulo' => $titulo,
            'totalItems' => $totalItems,
            'totalProductos' => $totalProductos,
            'totalEquipos' => $totalEquipos,
        ]);
    }
}