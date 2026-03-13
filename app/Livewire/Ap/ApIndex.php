<?php

namespace App\Livewire\Ap;

use Livewire\Component;
use App\Models\Ap;
use App\Models\Nodo;
use App\Models\Inventario;

class ApIndex extends Component
{
    public $nodos;
    public $inventarios;

    public $filtroNodo = '';
    public $searchAp = '';
    public $nombre;
    public $ip_lan;
    public $ip_wan;
    public $puerto_lan;
    public $puerto_wan;
    public $ssid;
    public $clave;
    public $user_login;
    public $clave_login;
    public $clientes_max;
    public $frecuencia;
    public $ancho_canal;
    public $estado = 'activo';
    public $inventario_id;
    public $clientesAp = [];
    public $apSeleccionado = null;
    public $ap_id;
    public $detalleAp;
    public $detalleInventario;
    // Buscador de clientes en editar
    public $searchCliente = ''; // Para el buscador
    public $selectedClientes = []; // IDs de clientes seleccionados
    // buscador de inventario
    public $searchInventario = ''; // Texto del buscador
    public $inventario_nombre = ''; // Para mostrar el equipo seleccionado en el input

    protected $rules = [
        'nombre' => 'required',
        'inventario_id' => 'required',
        'ip_lan' => 'required',
        'puerto_lan' => 'required',
        'ancho_canal' => 'required',
        'ssid' => 'required',
        'clientes_max' => 'required',


    ];
    // 1. Traduce el nombre del campo que aparece en el error
    protected $messages = [
        'nombre.required' => 'El nombre del equipo es obligatorio.',
        'inventario_id.required' => 'Debes seleccionar un ítem del inventario.',
        'ip_lan.required' => 'La dirección IP LAN no puede estar vacía.',
        'puerto_lan.required' => 'El puerto LAN es necesario para la conexión.',
        'ancho_canal.required' => 'Debes especificar el ancho de canal.',
        'ssid.required' => 'El nombre de la red (SSID) es obligatorio.',
        'clientes_max.required' => 'Indica el número máximo de clientes permitidos.',
    ];

    public function mount()
    {
        $this->nodos = Nodo::all();
        $this->inventarios = Inventario::doesntHave('ap')->get();
    }
    public function seleccionarEquipo($id, $nombre, $mac)
    {
        $this->inventario_id = $id;
        $this->inventario_nombre = $nombre . " - " . $mac;
        $this->searchInventario = ''; // Limpiamos la búsqueda tras seleccionar
    }

    public function quitarEquipo()
    {
        $this->inventario_id = null;
        $this->inventario_nombre = '';
    }
    public function getEquiposInventarioProperty()
    {
        if (strlen($this->searchInventario) < 2) return [];

        return \App\Models\Inventario::where(function ($query) {
            $query->whereHas('modelo', function ($q) {
                $q->where('nombre', 'like', '%' . $this->searchInventario . '%');
            })
                ->orWhere('mac', 'like', '%' . $this->searchInventario . '%');
        })
            ->where(function ($query) {
                $query->whereDoesntHave('ap') // Equipos libres
                    ->orWhere('id', $this->inventario_id); // O el equipo que ya tiene este AP
            })
            ->limit(5)
            ->get();
    }
    public function guardar()
    {
        $this->validate();

        Ap::create([
            'nombre'        => $this->nombre,
            'ip_lan'        => $this->ip_lan,
            'ip_wan'        => $this->ip_wan ?: null,
            'puerto_lan'    => $this->puerto_lan ?: null,
            'puerto_wan'    => $this->puerto_wan ?: null,
            'ssid'          => $this->ssid,
            'clave'         => $this->clave,
            'user_login'    => $this->user_login ?: null,
            'clave_login'   => $this->clave_login ?: null,
            'clientes_max'  => $this->clientes_max,
            'frecuencia'    => $this->frecuencia ?: null,
            'ancho_canal'   => $this->ancho_canal ?: null,
            'estado'        => $this->estado,
            'inventario_id' => $this->inventario_id,
        ]);

        $this->limpiar();

        $this->dispatch('closeModal');

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Ap Creado con exito'
        );
    }
    public function limpiar()
    {
        $this->reset([
            'nombre',
            'ip_lan',
            'ip_wan',
            'puerto_lan',
            'puerto_wan',
            'ssid',
            'clave',
            'user_login',
            'clave_login',
            'clientes_max',
            'frecuencia',
            'ancho_canal',
            'inventario_id',
            'inventario_nombre',
            'searchInventario'
        ]);
        $this->resetValidation();
    }
    public function editarAp($id)
    {
        $this->resetValidation();
        // Añadimos 'inventario_nombre' al reset para mayor seguridad
        $this->reset(['searchCliente', 'selectedClientes', 'searchInventario', 'inventario_nombre']);

        $ap = \App\Models\Ap::with(['clientes', 'inventario.modelo'])->find($id);

        $this->ap_id = $ap->id;
        $this->nombre = $ap->nombre;
        $this->ip_lan = $ap->ip_lan;
        $this->ip_wan = $ap->ip_wan;
        $this->puerto_lan = $ap->puerto_lan;
        $this->puerto_wan = $ap->puerto_wan;
        $this->ssid = $ap->ssid;
        $this->clave = $ap->clave;
        $this->user_login = $ap->user_login;
        $this->clave_login = $ap->clave_login;
        $this->clientes_max = $ap->clientes_max;
        $this->frecuencia = $ap->frecuencia;
        $this->ancho_canal = $ap->ancho_canal;
        $this->estado = $ap->estado;
        $this->inventario_id = $ap->inventario_id;

        // --- EL CAMBIO ESTÁ AQUÍ ---
        // Si el AP tiene un equipo asociado, cargamos su nombre y MAC
        if ($ap->inventario) {
            $this->inventario_nombre = $ap->inventario->modelo->nombre . " - " . $ap->inventario->mac;
        } else {
            $this->inventario_nombre = '';
        }

        $this->selectedClientes = $ap->clientes->pluck('id')->map(fn($id) => (string)$id)->toArray();

        $this->dispatch('abrirModalEditar');
    }

    /**
     * Guarda los cambios del AP y sincroniza la lista de clientes
     */
    public function actualizarAp()
    {
        $this->validate();
        $ap = \App\Models\Ap::find($this->ap_id);

        $ap->update([
            'nombre'        => $this->nombre,
            'ip_lan'        => $this->ip_lan,
            'ip_wan'        => $this->ip_wan ?: null,
            'puerto_lan'    => $this->puerto_lan ?: null,
            'puerto_wan'    => $this->puerto_wan ?: null,
            'ssid'          => $this->ssid,
            'clave'         => $this->clave,
            'user_login'    => $this->user_login,
            'clave_login'   => $this->clave_login,
            'clientes_max'  => $this->clientes_max,
            'frecuencia'    => $this->frecuencia ?: null,
            'ancho_canal'   => $this->ancho_canal,
            'estado'        => $this->estado,
            'inventario_id' => $this->inventario_id,
        ]);

        // LÓGICA DE SINCRONIZACIÓN DE CLIENTES
        // 1. Desvincular: Clientes que estaban en este AP pero ya no están marcados
        \App\Models\Cliente::where('ap_id', $ap->id)
            ->whereNotIn('id', $this->selectedClientes)
            ->update(['ap_id' => null]);

        // 2. Vincular: Clientes marcados (nuevos o viejos) se aseguran a este AP
        if (!empty($this->selectedClientes)) {
            \App\Models\Cliente::whereIn('id', $this->selectedClientes)
                ->update(['ap_id' => $ap->id]);
        }

        $this->reset(['searchCliente', 'selectedClientes']);
        $this->dispatch('cerrarModalEditar');
        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Ap Actualizado con exito'
        );
    }

    /**
     * Propiedad computada para el buscador del modal
     */
    public function getClientesProperty()
    {
        // Si no hay búsqueda, mostramos los clientes que ya pertenecen a este AP
        if (empty($this->searchCliente)) {
            return \App\Models\Cliente::where('ap_id', $this->ap_id)->get();
        }

        // Si hay búsqueda, mostramos coincidencias (libres o de este AP)
        return \App\Models\Cliente::where('nombre', 'like', '%' . $this->searchCliente . '%')
            ->where(function ($query) {
                $query->whereNull('ap_id')          // Clientes libres
                    ->orWhere('ap_id', $this->ap_id); // O que ya sean de este AP
            })
            ->limit(10)
            ->get();
    }
    // para el filtro del listado de cleintes en aps
    public function getClientesFiltradosProperty()
    {
        if (!$this->searchCliente) {
            return collect($this->clientesAp);
        }

        return collect($this->clientesAp)
            ->filter(function ($cliente) {
                return str_contains(
                    strtolower($cliente->nombre),
                    strtolower($this->searchCliente)
                );
            });
    }
    public function verDetalle($id)
    {
        // Cargamos inventario, el nodo del inventario y la bodega del inventario
        $ap = Ap::with(['inventario.nodo', 'inventario.bodega'])->find($id);

        if ($ap) {
            $this->detalleAp = $ap;
            $this->detalleInventario = $ap->inventario;
            // La bodega ya estará disponible dentro de $this->detalleInventario->bodega

            $this->dispatch('abrirModalDetalle');
        }
    }
    public function verClientes($apId)
    {
        $ap = Ap::with('clientes')->find($apId);

        $this->clientesAp = $ap->clientes;
        $this->apSeleccionado = $ap->nombre;

        $this->dispatch('abrirModalClientes');
    }
    public function render()
    {
        $aps = Ap::with(['inventario.nodo'])
            ->withCount('clientes')
            // Filtro por Nodo
            ->when($this->filtroNodo, function ($query) {
                $query->whereHas('inventario', function ($q) {
                    $q->where('nodo_id', $this->filtroNodo);
                });
            })
            // Filtro por Nombre o MAC (Buscador)
            ->when($this->searchAp, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->searchAp . '%')
                        ->orWhereHas('inventario', function ($inv) {
                            $inv->where('mac', 'like', '%' . $this->searchAp . '%');
                        });
                });
            })
            ->get();

        return view('livewire.ap.ap-index', compact('aps'));
    }
}
