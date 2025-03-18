<?php

namespace App\Livewire;

use App\Models\Nodo;
use App\Services\MikroTikService;
use Livewire\Component;
use RouterOS\Exceptions\ConnectException;
use RouterOS\Exceptions\BadCredentialsException;

class NodoMonitoreo extends Component
{
    public $nodos; // Lista de nodos
    public $interfaces = []; // Interfaces del nodo seleccionado
    public $selectedNodoId; // ID del nodo seleccionado
    public $errorMessage = ''; // Mensaje de error
    public $nodoNombre = ''; // Nombre del nodo seleccionado
    public $nodo; // Objeto del nodo seleccionado
    public $interfaceStats = []; // Estadísticas de tráfico de las interfaces
    public function mount()
    {
        // Obtener todos los nodos al cargar el componente
        $this->nodos = Nodo::all();
    }

    public function selectNodo($nodoId)
    {
        // Obtener el nodo seleccionado
        $this->nodo = Nodo::find($nodoId);

        if (!$this->nodo) {
            $this->errorMessage = 'El nodo seleccionado no existe.';
            return;
        }

        $this->nodoNombre = $this->nodo->nombre;
        $this->selectedNodoId = $nodoId;
        $this->interfaces = []; // Limpiar las interfaces al seleccionar un nuevo nodo
        $this->errorMessage = ''; // Limpiar mensajes de error
    }

    public function loadInterfaces()
    {
        // Validar que se haya seleccionado un nodo
        if (!$this->selectedNodoId) {
            return;
        }

        // Obtener el nodo seleccionado
        $nodo = Nodo::find($this->selectedNodoId);

        if (!$nodo) {
            $this->errorMessage = 'El nodo seleccionado no existe.';
            return;
        }

        // Crear una instancia del servicio MikroTik
        $mikroTikService = new MikroTikService(
            $nodo->ip,
            $nodo->user,
            $nodo->pass,
            $nodo->puerto_api ?? 8728
        );

        // Verificar si el nodo es alcanzable
        if (!$mikroTikService->isReachable()) {
            $this->errorMessage = 'No se pudo conectar al nodo. Verifica la IP, las credenciales y que el nodo esté en línea.';
            return;
        }

        // Obtener las interfaces y las estadísticas de tráfico
        try {
            $this->interfaces = $mikroTikService->getInterfaces();
            $this->interfaceStats = $mikroTikService->getInterfaceStatistics(); // Obtener estadísticas
            $this->errorMessage = ''; // Limpiar mensajes de error
        } catch (\Exception $e) {
            $this->errorMessage = 'Error al obtener las interfaces: ' . $e->getMessage();
            $this->interfaces = [];
            $this->interfaceStats = [];
        }
    }
    public function render()
    {
        return view('livewire.nodo-monitoreo');
    }

}