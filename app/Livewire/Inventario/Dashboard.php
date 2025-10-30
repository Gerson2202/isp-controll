<?php

namespace App\Livewire\Inventario;

use Livewire\Component;
use App\Models\Bodega;
use App\Models\Cliente;
use App\Models\Nodo;
use App\Models\User;

class Dashboard extends Component
{
    public $tipoSeleccionado = 'todos';
    public $search = '';
    public $resultados = [];

    // Opciones para el select
    public $opciones = [
        'todos' => 'Todos',
        'bodegas' => 'Bodegas',
        'clientes' => 'Clientes',
        'nodos' => 'Nodos',
        'usuarios' => 'Usuarios',
    ];

    /**
     * Actualiza los resultados cuando cambia el tipo o la búsqueda
     */
    public function updatedTipoSeleccionado()
    {
        $this->search = '';
        $this->resultados = [];
    }

    public function updatedSearch()
    {
        $this->cargarResultados();
    }

    /**
     * Carga los resultados según el tipo seleccionado
     */
    public function cargarResultados()
    {
        if (empty($this->search)) {
            $this->resultados = [];
            return;
        }

        $this->resultados = match($this->tipoSeleccionado) {
            'bodegas' => $this->buscarBodegas(),
            'clientes' => $this->buscarClientes(),
            'nodos' => $this->buscarNodos(),
            'usuarios' => $this->buscarUsuarios(),
            'todos' => $this->buscarTodos(),
            default => []
        };
    }

    /**
     * Búsqueda específica para bodegas
     */
    protected function buscarBodegas()
    {
        $bodegas = Bodega::where('nombre', 'like', "%{$this->search}%")
            ->orderBy('nombre')
            ->take(10)
            ->get();

        $resultados = [];
        foreach ($bodegas as $bodega) {
            $resultados[] = [
                'id' => $bodega->id,
                'nombre' => $bodega->nombre,
                'tipo' => 'bodega',
                'tipo_display' => 'Bodega',
                'ruta' => route('inventario.detalle', ['tipo' => 'bodega', 'id' => $bodega->id])
            ];
        }

        return $resultados;
    }

    /**
     * Búsqueda específica para clientes
     */
    protected function buscarClientes()
    {
        $clientes = Cliente::where('nombre', 'like', "%{$this->search}%")
            ->orderBy('nombre')
            ->take(10)
            ->get();

        $resultados = [];
        foreach ($clientes as $cliente) {
            $resultados[] = [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'tipo' => 'cliente',
                'tipo_display' => 'Cliente',
                'ruta' => route('inventario.detalle', ['tipo' => 'cliente', 'id' => $cliente->id])
            ];
        }

        return $resultados;
    }

    /**
     * Búsqueda específica para nodos
     */
    protected function buscarNodos()
    {
        $nodos = Nodo::where('nombre', 'like', "%{$this->search}%")
            ->orderBy('nombre')
            ->take(10)
            ->get();

        $resultados = [];
        foreach ($nodos as $nodo) {
            $resultados[] = [
                'id' => $nodo->id,
                'nombre' => $nodo->nombre,
                'tipo' => 'nodo',
                'tipo_display' => 'Nodo',
                'ruta' => route('inventario.detalle', ['tipo' => 'nodo', 'id' => $nodo->id])
            ];
        }

        return $resultados;
    }

    /**
     * Búsqueda específica para usuarios
     */
    protected function buscarUsuarios()
    {
        $usuarios = User::where('name', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->take(10)
            ->get();

        $resultados = [];
        foreach ($usuarios as $usuario) {
            $resultados[] = [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'tipo' => 'usuario',
                'tipo_display' => 'Usuario',
                'ruta' => route('inventario.detalle', ['tipo' => 'usuario', 'id' => $usuario->id])
            ];
        }

        return $resultados;
    }

    /**
     * Búsqueda en todos los tipos
     */
    protected function buscarTodos()
    {
        $resultados = [];

        // Bodegas
        $bodegas = Bodega::where('nombre', 'like', "%{$this->search}%")
            ->orderBy('nombre')
            ->take(3)
            ->get();

        foreach ($bodegas as $bodega) {
            $resultados[] = [
                'id' => $bodega->id,
                'nombre' => $bodega->nombre,
                'tipo' => 'bodega',
                'tipo_display' => 'Bodega',
                'ruta' => route('inventario.detalle', ['tipo' => 'bodega', 'id' => $bodega->id])
            ];
        }

        // Clientes
        $clientes = Cliente::where('nombre', 'like', "%{$this->search}%")
            ->orderBy('nombre')
            ->take(3)
            ->get();

        foreach ($clientes as $cliente) {
            $resultados[] = [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'tipo' => 'cliente',
                'tipo_display' => 'Cliente',
                'ruta' => route('inventario.detalle', ['tipo' => 'cliente', 'id' => $cliente->id])
            ];
        }

        // Nodos
        $nodos = Nodo::where('nombre', 'like', "%{$this->search}%")
            ->orderBy('nombre')
            ->take(3)
            ->get();

        foreach ($nodos as $nodo) {
            $resultados[] = [
                'id' => $nodo->id,
                'nombre' => $nodo->nombre,
                'tipo' => 'nodo',
                'tipo_display' => 'Nodo',
                'ruta' => route('inventario.detalle', ['tipo' => 'nodo', 'id' => $nodo->id])
            ];
        }

        // Usuarios
        $usuarios = User::where('name', 'like', "%{$this->search}%")
            ->orderBy('name')
            ->take(3)
            ->get();

        foreach ($usuarios as $usuario) {
            $resultados[] = [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'tipo' => 'usuario',
                'tipo_display' => 'Usuario',
                'ruta' => route('inventario.detalle', ['tipo' => 'usuario', 'id' => $usuario->id])
            ];
        }

        return $resultados;
    }

    /**
     * Obtiene estadísticas generales
     */
    public function getEstadisticasProperty()
    {
        return [
            'total_bodegas' => Bodega::count(),
            'total_clientes' => Cliente::count(),
            'total_nodos' => Nodo::count(),
            'total_usuarios' => User::count(),
        ];
    }

    /**
     * Renderiza la vista
     */
    public function render()
    {
        // Cargar resultados si hay búsqueda
        if (!empty($this->search)) {
            $this->cargarResultados();
        }

        return view('livewire.inventario.dashboard');
    }
}