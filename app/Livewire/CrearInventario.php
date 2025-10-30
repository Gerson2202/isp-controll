<?php

namespace App\Livewire;

use App\Models\Inventario;
use App\Models\Modelo;
use App\Models\Bodega;
use App\Models\User;
use App\Models\Nodo;
use App\Models\Cliente;
use App\Models\MovimientoInventario;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;

use Livewire\Component;

class CrearInventario extends Component
{
    public $mac;
    public $descripcion;
    public $serial;
    public $modelo_id;
    public $fecha;

    // Campos para asignación
    public $tipo_asignacion = '';
    public $bodega_id = null;
    public $user_id = null;
    public $nodo_id = null;
    public $cliente_id = null;

    // Búsquedas
    public $searchBodega = '';
    public $searchUsuario = '';
    public $searchNodo = '';
    public $searchCliente = '';

    public $bodegas = [];
    public $usuarios = [];
    public $nodos = [];
    public $clientes = [];

    public $modelos = [];

    protected $rules = [
        'mac' => '|unique:inventarios,mac',
        'descripcion' => 'required',
        'serial' => 'nullable',
        'modelo_id' => 'required|exists:modelos,id',
        'fecha' => 'nullable|date',
        'tipo_asignacion' => 'required|in:bodega,usuario,nodo,cliente',
        'bodega_id' => 'required_if:tipo_asignacion,bodega',
        'user_id' => 'required_if:tipo_asignacion,usuario',
        'nodo_id' => 'required_if:tipo_asignacion,nodo',
        'cliente_id' => 'required_if:tipo_asignacion,cliente',
    ];

    public function mount()
    {
        $this->modelos = Modelo::all();
        $this->fecha = now()->format('Y-m-d');
    }

    public function render()
    {
        // Buscar según el tipo de asignación - ahora se actualiza automáticamente con wire:model.live
        if ($this->searchBodega && $this->tipo_asignacion === 'bodega') {
            $this->bodegas = Bodega::where('nombre', 'like', '%' . $this->searchBodega . '%')->get();
        } else {
            $this->bodegas = [];
        }

        if ($this->searchUsuario && $this->tipo_asignacion === 'usuario') {
            $this->usuarios = User::where('name', 'like', '%' . $this->searchUsuario . '%')->get();
        } else {
            $this->usuarios = [];
        }

        if ($this->searchNodo && $this->tipo_asignacion === 'nodo') {
            $this->nodos = Nodo::where('nombre', 'like', '%' . $this->searchNodo . '%')->get();
        } else {
            $this->nodos = [];
        }

        if ($this->searchCliente && $this->tipo_asignacion === 'cliente') {
            $this->clientes = Cliente::where('nombre', 'like', '%' . $this->searchCliente . '%')->get();
        } else {
            $this->clientes = [];
        }

        return view('livewire.crear-inventario');
    }

    public function updatedTipoAsignacion()
    {
        // Limpiar búsquedas anteriores cuando cambia el tipo
        $this->reset([
            'bodega_id',
            'user_id',
            'nodo_id',
            'cliente_id',
            'searchBodega',
            'searchUsuario',
            'searchNodo',
            'searchCliente'
        ]);
    }

    public function save()
    {
        $this->validate();

        try {
            // Crear el inventario
            $inventario = Inventario::create([
                'mac' => $this->mac,
                'descripcion' => $this->descripcion,
                'serial' => $this->serial,
                'modelo_id' => $this->modelo_id,
                'fecha' => $this->fecha,
                'bodega_id' => $this->tipo_asignacion === 'bodega' ? $this->bodega_id : null,
                'user_id' => $this->tipo_asignacion === 'usuario' ? $this->user_id : null,
                'nodo_id' => $this->tipo_asignacion === 'nodo' ? $this->nodo_id : null,
                'cliente_id' => $this->tipo_asignacion === 'cliente' ? $this->cliente_id : null,
            ]);

            // Registrar movimiento de creación
            $this->registrarMovimiento($inventario, 'entrada', 'Equipo registrado en el sistema');

            $this->dispatch('hide-modals');
            $this->dispatch(
                'notify',
                type: 'success',
                message: '¡Equipo registrado con exito!'
            );

            // En lugar de $this->reset(), resetea solo los campos del formulario
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error al crear el inventario: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        // Resetear solo los campos del formulario, no los datos cargados
        $this->mac = '';
        $this->descripcion = '';
        $this->serial = '';
        $this->modelo_id = '';
        $this->fecha = now()->format('Y-m-d');
        $this->tipo_asignacion = '';
        $this->bodega_id = null;
        $this->user_id = null;
        $this->nodo_id = null;
        $this->cliente_id = null;
        $this->searchBodega = '';
        $this->searchUsuario = '';
        $this->searchNodo = '';
        $this->searchCliente = '';
        $this->bodegas = [];
        $this->usuarios = [];
        $this->nodos = [];
        $this->clientes = [];

    }
    private function registrarMovimiento($inventario, $tipo, $descripcion)
    {
        MovimientoInventario::create([
            'inventario_id' => $inventario->id,
            'tipo_movimiento' => $tipo,
            'descripcion' => $descripcion,
            'bodega_nueva_id' => $inventario->bodega_id,
            'user_nuevo_id' => $inventario->user_id,
            'nodo_nuevo_id' => $inventario->nodo_id,
            'cliente_nuevo_id' => $inventario->cliente_id,
            'user_id' => Auth::id(),
        ]);
    }
}
