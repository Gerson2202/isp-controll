<?php

namespace App\Livewire\Inventario;

use App\Models\Inventario;
use App\Models\MovimientoInventario;
use App\Models\Bodega;
use App\Models\User;
use App\Models\Nodo;
use App\Models\Cliente;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Component;

class MovimientoEquipo extends Component
{
    public $inventario_id;
    public $tipo_movimiento = 'traslado';
    public $descripcion;

    public $nuevo_tipo_asignacion = '';
    public $nueva_bodega_id = null;
    public $nuevo_user_id = null;
    public $nuevo_nodo_id = null;
    public $nuevo_cliente_id = null;

    public $searchInventario = '';
    public $searchBodega = '';
    public $searchUsuario = '';
    public $searchNodo = '';
    public $searchCliente = '';

    public $inventarios = [];
    public $bodegas = [];
    public $usuarios = [];
    public $nodos = [];
    public $clientes = [];

    protected $rules = [
        'inventario_id' => 'required|exists:inventarios,id',
        'tipo_movimiento' => 'required|in:entrada,salida,traslado,asignacion',
        'descripcion' => 'required|min:5',
        'nuevo_tipo_asignacion' => 'required|in:bodega,usuario,nodo,cliente',
        'nueva_bodega_id' => 'required_if:nuevo_tipo_asignacion,bodega',
        'nuevo_user_id' => 'required_if:nuevo_tipo_asignacion,usuario',
        'nuevo_nodo_id' => 'required_if:nuevo_tipo_asignacion,nodo',
        'nuevo_cliente_id' => 'required_if:nuevo_tipo_asignacion,cliente',
    ];

    protected $messages = [
        'inventario_id.required' => 'Debe seleccionar un equipo.',
        'descripcion.required' => 'La descripción es obligatoria.',
        'nuevo_tipo_asignacion.required' => 'Debe seleccionar el tipo de asignación.',
    ];

    public function render()
    {
        // Búsqueda de inventarios
        if (!empty($this->searchInventario)) {
            $this->inventarios = Inventario::where(function ($query) {
                $query->where('mac', 'LIKE', '%' . $this->searchInventario . '%')
                    ->orWhere('serial', 'LIKE', '%' . $this->searchInventario . '%')
                    ->orWhere('descripcion', 'LIKE', '%' . $this->searchInventario . '%');
            })->limit(10)->get();
        } else {
            $this->inventarios = [];
        }

        // Búsquedas según el tipo de asignación
        switch ($this->nuevo_tipo_asignacion) {
            case 'bodega':
                if (!empty($this->searchBodega)) {
                    $this->bodegas = Bodega::where('nombre', 'LIKE', '%' . $this->searchBodega . '%')->get();
                } else {
                    $this->bodegas = [];
                }
                break;

            case 'usuario':
                if (!empty($this->searchUsuario)) {
                    $this->usuarios = User::where('name', 'LIKE', '%' . $this->searchUsuario . '%')->get();
                } else {
                    $this->usuarios = [];
                }
                break;

            case 'nodo':
                if (!empty($this->searchNodo)) {
                    $this->nodos = Nodo::where('nombre', 'LIKE', '%' . $this->searchNodo . '%')->get();
                } else {
                    $this->nodos = [];
                }
                break;

            case 'cliente':
                if (!empty($this->searchCliente)) {
                    $this->clientes = Cliente::where('nombre', 'LIKE', '%' . $this->searchCliente . '%')->get();
                } else {
                    $this->clientes = [];
                }
                break;
        }

        return view('livewire.inventario.movimiento-equipo');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function updatedNuevoTipoAsignacion()
    {
        // Resetear todas las selecciones cuando cambia el tipo
        $this->reset([
            'nueva_bodega_id',
            'nuevo_user_id',
            'nuevo_nodo_id',
            'nuevo_cliente_id',
            'searchBodega',
            'searchUsuario',
            'searchNodo',
            'searchCliente',
            'bodegas',
            'usuarios',
            'nodos',
            'clientes'
        ]);
    }

    public function save()
    {
        $this->validate();

        try {
            // Encontrar el inventario
            $inventario = Inventario::findOrFail($this->inventario_id);

            // Guardar ubicaciones anteriores
            $datosAnteriores = [
                'bodega_anterior_id' => $inventario->bodega_id,
                'user_anterior_id' => $inventario->user_id,
                'nodo_anterior_id' => $inventario->nodo_id,
                'cliente_anterior_id' => $inventario->cliente_id,
            ];

            // Preparar datos para actualización
            $datosActualizacion = [
                'bodega_id' => null,
                'user_id' => null,
                'nodo_id' => null,
                'cliente_id' => null,
            ];

            // Asignar según el tipo seleccionado
            switch ($this->nuevo_tipo_asignacion) {
                case 'bodega':
                    $datosActualizacion['bodega_id'] = $this->nueva_bodega_id;
                    break;
                case 'usuario':
                    $datosActualizacion['user_id'] = $this->nuevo_user_id;
                    break;
                case 'nodo':
                    $datosActualizacion['nodo_id'] = $this->nuevo_nodo_id;
                    break;
                case 'cliente':
                    $datosActualizacion['cliente_id'] = $this->nuevo_cliente_id;
                    break;
            }

            // Actualizar inventario
            $inventario->update($datosActualizacion);

            // Registrar movimiento
            MovimientoInventario::create(array_merge([
                'inventario_id' => $inventario->id,
                'tipo_movimiento' => $this->tipo_movimiento,
                'descripcion' => $this->descripcion,
                'bodega_nueva_id' => $inventario->bodega_id,
                'user_nuevo_id' => $inventario->user_id,
                'nodo_nuevo_id' => $inventario->nodo_id,
                'cliente_nuevo_id' => $inventario->cliente_id,
                'user_id' => Auth::id(),
            ], $datosAnteriores));

            $this->dispatch(
                'notify',
                type: 'success',
                message: '¡Moviemiento realizado con exito!'
            );

            // Resetear el formulario
            $this->reset([
                'inventario_id',
                'tipo_movimiento',
                'descripcion',
                'nuevo_tipo_asignacion',
                'nueva_bodega_id',
                'nuevo_user_id',
                'nuevo_nodo_id',
                'nuevo_cliente_id',
                'searchInventario',
                'searchBodega',
                'searchUsuario',
                'searchNodo',
                'searchCliente',
                'inventarios',
                'bodegas',
                'usuarios',
                'nodos',
                'clientes'
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al registrar el movimiento: ' . $e->getMessage());
        }
    }
}
