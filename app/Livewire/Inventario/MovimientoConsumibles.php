<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Nodo;
use App\Models\Bodega;
use App\Models\Consumible;
use App\Models\ConsumibleStock;
use App\Models\ConsumibleMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MovimientoConsumibles extends Component
{
    // Propiedades principales
    public $origen_tipo;
    public $origen_id;
    public $destino_tipo;
    public $destino_id;
    public $consumible_id;
    public $cantidad;

    // Propiedades para listas y búsqueda
    public $origenes = [];
    public $destinos = [];
    public $searchConsumible = '';
    public $searchClienteOrigen = '';
    public $searchClienteDestino = '';
    public $mostrarResultadosConsumible = false;
    public $mostrarResultadosClienteOrigen = false;
    public $mostrarResultadosClienteDestino = false;
    public $clientesOrigen = [];
    public $clientesDestino = [];

    // Constantes para tipos de origen/destino
    protected $tiposPermitidos = ['usuario', 'cliente', 'bodega', 'nodo'];

    /**
     * Actualiza la lista de orígenes cuando cambia el tipo
     */
    public function updatedOrigenTipo()
    {
        $this->origen_id = null;
        $this->searchClienteOrigen = '';
        $this->mostrarResultadosClienteOrigen = false;

        if ($this->origen_tipo !== 'cliente') {
            $this->origenes = $this->getOpciones($this->origen_tipo);
        }
    }

    /**
     * Actualiza la lista de destinos cuando cambia el tipo
     */
    public function updatedDestinoTipo()
    {
        $this->destino_id = null;
        $this->searchClienteDestino = '';
        $this->mostrarResultadosClienteDestino = false;

        if ($this->destino_tipo !== 'cliente') {
            $this->destinos = $this->getOpciones($this->destino_tipo);
        }
    }

    /**
     * Busca clientes para el origen
     */
    public function updatedSearchClienteOrigen()
    {
        $this->mostrarResultadosClienteOrigen = !empty($this->searchClienteOrigen);
        $this->origen_id = null;

        if ($this->searchClienteOrigen) {
            $this->clientesOrigen = Cliente::where('nombre', 'like', "%{$this->searchClienteOrigen}%")
                ->orWhere('id', 'like', "%{$this->searchClienteOrigen}%")
                ->orderBy('nombre')
                ->take(10)
                ->get();
        } else {
            $this->clientesOrigen = [];
        }
    }

    /**
     * Busca clientes para el destino
     */
    public function updatedSearchClienteDestino()
    {
        $this->mostrarResultadosClienteDestino = !empty($this->searchClienteDestino);
        $this->destino_id = null;

        if ($this->searchClienteDestino) {
            $this->clientesDestino = Cliente::where('nombre', 'like', "%{$this->searchClienteDestino}%")
                ->orWhere('id', 'like', "%{$this->searchClienteDestino}%")
                ->orderBy('nombre')
                ->take(10)
                ->get();
        } else {
            $this->clientesDestino = [];
        }
    }

    /**
     * Obtiene las opciones según el tipo especificado
     */
    protected function getOpciones($tipo)
    {
        if (!in_array($tipo, $this->tiposPermitidos)) {
            return collect();
        }

        return match ($tipo) {
            'usuario' => User::select('id', 'name')->orderBy('name')->get(),
            'cliente' => Cliente::select('id', 'nombre')->orderBy('nombre')->get(),
            'bodega'  => Bodega::select('id', 'nombre')->orderBy('nombre')->get(),
            'nodo'    => Nodo::select('id', 'nombre')->orderBy('nombre')->get(),
            default   => collect(),
        };
    }

    /**
     * Realiza el movimiento de inventario con transacción
     */
    public function realizarMovimiento()
    {
        $this->validate();

        // Usar transacción para asegurar la consistencia de datos
        DB::transaction(function () {
            $this->procesarMovimiento();
        });

        $this->mostrarMensajeExito();
        $this->resetForm();
    }

    /**
     * Valida los datos del formulario
     */
    protected function rules()
    {
        return [
            'consumible_id' => 'required',
            'cantidad' => 'required|numeric|min:1',
            'origen_tipo' => 'required',
            'origen_id' => 'required',
            'destino_tipo' => 'required',
            'destino_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($this->destino_tipo === $this->origen_tipo && $this->destino_id === $this->origen_id) {
                        $fail('El destino debe ser diferente al origen.');
                    }
                },
            ],
        ];
    }


    /**
     * Mensajes de validación personalizados
     */
    protected function messages()
    {
        return [
            'consumible_id.required' => 'Seleccione un consumible',
            'cantidad.required' => 'Ingrese la cantidad',
            'origen_tipo.required' => 'Seleccione el tipo de origen',
            'origen_id.required' => 'Seleccione el origen',
            'destino_tipo.required' => 'Seleccione el tipo de destino',
            'destino_id.required' => 'Seleccione el destino',
        ];
    }

    /**
     * Procesa el movimiento de inventario
     */
    protected function procesarMovimiento()
    {
        $this->validarStockDisponible();

        // Actualizar stock del origen
        $stockOrigen = ConsumibleStock::where('consumible_id', $this->consumible_id)
            ->where($this->origen_tipo . '_id', $this->origen_id)
            ->firstOrFail();

        $stockOrigen->decrement('cantidad', $this->cantidad);

        // Actualizar o crear stock del destino
        $stockDestino = ConsumibleStock::firstOrCreate(
            [
                'consumible_id' => $this->consumible_id,
                $this->destino_tipo . '_id' => $this->destino_id,
            ],
            ['cantidad' => 0]
        );

        $stockDestino->increment('cantidad', $this->cantidad);

        // Registrar el movimiento
        $this->registrarMovimiento();
    }

    /**
     * Valida que haya stock suficiente en el origen
     */
    protected function validarStockDisponible()
    {
        $stockOrigen = ConsumibleStock::where('consumible_id', $this->consumible_id)
            ->where($this->origen_tipo . '_id', $this->origen_id)
            ->first();

        if (!$stockOrigen || $stockOrigen->cantidad < $this->cantidad) {
            throw ValidationException::withMessages([
                'cantidad' => 'Stock insuficiente. Disponible: ' . ($stockOrigen->cantidad ?? 0),
            ]);
        }
    }

    /**
     * Registra el movimiento en el historial
     */
    protected function registrarMovimiento()
    {
        ConsumibleMovimiento::create([
            'consumible_id' => $this->consumible_id,
            'cantidad' => $this->cantidad,
            'tipo_movimiento' => 'traslado',
            'origen_tipo' => $this->origen_tipo,
            'origen_id' => $this->origen_id,
            'destino_tipo' => $this->destino_tipo,
            'destino_id' => $this->destino_id,
            'descripcion' => 'Movimiento manual registrado por sistema',
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Selecciona un consumible del buscador
     */
    public function selectConsumible(int $id, string $nombre)
    {
        $this->consumible_id = $id;
        $this->searchConsumible = $nombre;
        $this->mostrarResultadosConsumible = false;
    }

    /**
     * Selecciona un cliente para el origen
     */
    public function selectClienteOrigen(int $id, string $nombre)
    {
        $this->origen_id = $id;
        $this->searchClienteOrigen = $nombre;
        $this->mostrarResultadosClienteOrigen = false;
    }

    /**
     * Selecciona un cliente para el destino
     */
    public function selectClienteDestino(int $id, string $nombre)
    {
        $this->destino_id = $id;
        $this->searchClienteDestino = $nombre;
        $this->mostrarResultadosClienteDestino = false;
    }

    /**
     * Muestra resultados de búsqueda cuando se escribe
     */
    public function updatedSearchConsumible()
    {
        $this->mostrarResultadosConsumible = !empty($this->searchConsumible);
        $this->consumible_id = null;
    }

    /**
     * Oculta los resultados cuando se hace clic fuera
     */
    public function cerrarResultados()
    {
        $this->mostrarResultadosConsumible = false;
        $this->mostrarResultadosClienteOrigen = false;
        $this->mostrarResultadosClienteDestino = false;
    }

    /**
     * Muestra mensaje de éxito
     */
    protected function mostrarMensajeExito()
    {
        $this->dispatch(
            'notify',
            type: 'success',
            message: '¡Moviemiento realizado con exito!'
        );
    }

    /**
     * Resetea el formulario
     */
    protected function resetForm()
    {
        $this->reset([
            'consumible_id',
            'cantidad',
            'origen_tipo',
            'origen_id',
            'destino_tipo',
            'destino_id',
            'searchConsumible',
            'searchClienteOrigen',
            'searchClienteDestino'
        ]);

        $this->origenes = [];
        $this->destinos = [];
        $this->clientesOrigen = [];
        $this->clientesDestino = [];
        $this->mostrarResultadosConsumible = false;
        $this->mostrarResultadosClienteOrigen = false;
        $this->mostrarResultadosClienteDestino = false;
    }

    /**
     * Renderiza la vista
     */
    public function render()
    {
        $consumibles = $this->getConsumibles();
        return view('livewire.inventario.movimiento-consumibles', compact('consumibles'));
    }

    /**
     * Obtiene la lista de consumibles filtrados
     */
    protected function getConsumibles()
    {
        if (empty($this->searchConsumible)) {
            return collect();
        }

        return Consumible::where('nombre', 'like', "%{$this->searchConsumible}%")
            ->orderBy('nombre')
            ->take(10)
            ->get();
    }
}
