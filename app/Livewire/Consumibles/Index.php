<?php

namespace App\Livewire\Consumibles;

use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Consumible;
use App\Models\ConsumibleMovimiento;
use App\Models\ConsumibleStock;
use App\Models\Nodo;
use App\Models\User;
use Livewire\Component;

class Index extends Component
{
    public $consumibles, $nombre, $unidad, $descripcion;
    public $modalConsumible = false;
    public $search = '';

    public $cantidad, $ubicacion_tipo, $ubicacion_id;
    public $modalStock = false;
    public $ubicaciones = [];

    // Para clientes
    public $search_cliente = '';
    public $clientes_filtrados = [];
    public $cliente_seleccionado = null;

    public $searchConsumible = '';
    public $consumible_id = null;
    public $consumibles_filtrados = [];

    public function updatedSearchConsumible()
    {
        if (!empty($this->searchConsumible)) {
            $this->consumibles_filtrados = \App\Models\Consumible::where('nombre', 'like', '%' . $this->searchConsumible . '%')
                ->limit(10)
                ->get();
        } else {
            $this->consumibles_filtrados = [];
        }
    }

    public function seleccionarConsumible($consumibleId)
    {
        $this->consumible_id = $consumibleId;
        $consumible = \App\Models\Consumible::find($consumibleId);
        $this->searchConsumible = $consumible->nombre;
        $this->consumibles_filtrados = [];
    }

    public function clearConsumible()
    {
        $this->reset(['consumible_id', 'searchConsumible', 'consumibles_filtrados']);
    }
    // ---

    public function updatedUbicacionTipo($value)
    {
        $this->ubicacion_id = null;

        // Reset clientes si cambia tipo
        if ($value != 'cliente') {
            $this->search_cliente = '';
            $this->clientes_filtrados = [];
            $this->cliente_seleccionado = null;
        }

        switch ($value) {
            case 'bodega':
                $this->ubicaciones = Bodega::all();
                break;
            case 'nodo':
                $this->ubicaciones = Nodo::all();
                break;
            case 'usuario':
                $this->ubicaciones = User::all();
                break;
            default:
                $this->ubicaciones = [];
        }
    }

    // Buscador en vivo para clientes
    public function updatedSearchCliente($value)
    {
        if ($this->ubicacion_tipo == 'cliente') {
            $this->clientes_filtrados = Cliente::where('nombre', 'like', "%{$value}%")->get();
        }
    }

    public function seleccionarCliente($id)
    {
        $this->cliente_seleccionado = Cliente::find($id);
        $this->search_cliente = $this->cliente_seleccionado->nombre;
        $this->ubicacion_id = $this->cliente_seleccionado->id;
        $this->clientes_filtrados = [];
    }

    public function render()
    {
        $query = Consumible::with('stocks');

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        $this->consumibles = $query->get();

        return view('livewire.consumibles.index', [
            'bodegas' => Bodega::all(),
            'nodos' => Nodo::all(),
            'usuarios' => User::all()
        ]);
    }

    // ---------------- Consumible ----------------
    public function openModalConsumible()
    {
        $this->modalConsumible = true;
    }
    public function closeModalConsumible()
    {
        $this->reset(['nombre', 'unidad', 'descripcion', 'consumible_id']);
        $this->modalConsumible = false;
    }

    public function saveConsumible()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'unidad' => 'required|string|max:50'
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'unidad.required' => 'La unidad es obligatoria'
        ]);

        if ($this->consumible_id) {
            $consumible = Consumible::find($this->consumible_id);
            $consumible->update([
                'nombre' => $this->nombre,
                'unidad' => $this->unidad,
                'descripcion' => $this->descripcion
            ]);
        } else {
            Consumible::create([
                'nombre' => $this->nombre,
                'unidad' => $this->unidad,
                'descripcion' => $this->descripcion
            ]);
        }

        $this->dispatch('hide-modals');
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Consumible agregado con extio!'
        );
        $this->closeModalConsumible();
    }

    public function editConsumible($id)
    {
        $consumible = Consumible::find($id);
        $this->consumible_id = $consumible->id;
        $this->nombre = $consumible->nombre;
        $this->unidad = $consumible->unidad;
        $this->descripcion = $consumible->descripcion;
        $this->openModalConsumible();
    }

    public function deleteConsumible($id)
    {
        Consumible::find($id)->delete();
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Consumible actualizado con extio!'
        );
    }

    // ---------------- Stock ----------------
    public function openModalStock()
    {
        $this->modalStock = true;
    }
    public function closeModalStock()
    {
        $this->reset(['consumible_id', 'cantidad', 'ubicacion_tipo', 'ubicacion_id', 'search_cliente']);
        $this->clientes_filtrados = [];
        $this->modalStock = false;
    }

    public function saveStock()
    {
        $this->validate([
            'consumible_id' => 'required|exists:consumibles,id',
            'cantidad' => 'required|integer|min:1',
            'ubicacion_tipo' => 'required|in:bodega,cliente,nodo,usuario',
            'ubicacion_id' => 'required'
        ], [
            'consumible_id.required' => 'Seleccione un consumible',
            'cantidad.required' => 'Ingrese la cantidad',
            'cantidad.min'           => 'La cantidad debe ser mayor o igual a 1',
            'ubicacion_tipo.required' => 'Seleccione el tipo de ubicación',
            'ubicacion_id.required' => 'Seleccione la ubicación'
        ]);

        // 🔹 1. Registrar o actualizar el stock actual
        $stock = ConsumibleStock::firstOrCreate(
            [
                'consumible_id' => $this->consumible_id,
                $this->ubicacion_tipo . '_id' => $this->ubicacion_id,
            ],
            ['cantidad' => 0]
        );

        $stock->increment('cantidad', $this->cantidad);

        // 🔹 2. Registrar el movimiento en el historial
        ConsumibleMovimiento::create([
            'consumible_id' => $this->consumible_id,
            'cantidad' => $this->cantidad,
            'tipo_movimiento' => 'entrada',
            'destino_tipo' => $this->ubicacion_tipo,
            'destino_id' => $this->ubicacion_id,
            'descripcion' => 'Ingreso manual de stock inicial o adicional',
            'user_id' => auth()->id(),

        ]);

        // 🔹 3. Feedback al usuario
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Stock agregado y movimiento registrado correctamente!'
        );

        $this->closeModalStock();
        $this->reset(['searchConsumible', 'consumible_id', 'consumibles_filtrados']);
    }
}
