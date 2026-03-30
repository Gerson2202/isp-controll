<?php

namespace App\Livewire\Contratos;

use Carbon\Carbon;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\Nodo;
use App\Models\Plan;
use App\Models\Ticket;

class ContratosList extends Component
{
    use WithPagination;

    // 🔍 Busqueda
    public $search = '';

    // 📄 Paginación
    public $perPage = 10;

    // 🔃 Orden
    public $sortField = 'fecha_inicio';
    public $sortDirection = 'desc';

    // 🎯 FILTROS
    public $filterEstado = '';
    public $filterTecnologia = '';
    public $filterNodo = '';
    public $filterEstadoCliente = '';

    // 📦 Datos cacheados (mejora rendimiento)
    public $clientesList;
    public $planesList;

    protected $queryString = [
        'search',
        'perPage',
        'sortField',
        'sortDirection',
        'filterEstado',
        'filterTecnologia',
        'filterNodo',
        'filterEstadoCliente'
    ];

    // ✏️ Variables edición
    public $showModal = false;
    public $contratoId;
    public $cliente_id;
    public $plan_id;
    public $tecnologia;
    public $fecha_inicio;
    public $fecha_fin;
    public $estado;
    public $precio;

    protected $rules = [
        'cliente_id' => 'required|exists:clientes,id',
        'plan_id' => 'required|exists:plans,id',
        'tecnologia' => 'required|string|max:50',
        'fecha_inicio' => 'required|date_format:Y-m-d',
        'fecha_fin' => 'nullable|date_format:Y-m-d|after:fecha_inicio',
        'estado' => 'required|in:activo,cancelado,suspendido',
        'precio' => 'required|regex:/^[\d.,]+$/',
    ];

    // 🚀 Carga inicial optimizada
    public function mount()
    {
        $this->clientesList = Cliente::orderBy('nombre')->get();
        $this->planesList = Plan::orderBy('nombre')->get();
    }

    // 🔄 Reset paginación
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingPerPage()
    {
        $this->resetPage();
    }
    public function updatingFilterEstado()
    {
        $this->resetPage();
    }
    public function updatingFilterTecnologia()
    {
        $this->resetPage();
    }
    public function updatingFilterNodo()
    {
        $this->resetPage();
    }
    public function updatingFilterEstadoCliente()
    {
        $this->resetPage();
    }

    // 🔃 Ordenar
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    // 🧼 Limpiar filtros
    public function resetFilters()
    {
        $this->reset([
            'search',
            'filterEstado',
            'filterTecnologia',
            'filterNodo',
            'filterEstadoCliente'
        ]);

        $this->resetPage();
    }

    // ✏️ Editar contrato
    public function openEditModal($contratoId)
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->dispatch('abrir-modal');

        $contrato = Contrato::findOrFail($contratoId);

        $this->contratoId = $contrato->id;
        $this->cliente_id = $contrato->cliente_id;
        $this->plan_id = $contrato->plan_id;
        $this->tecnologia = $contrato->tecnologia;
        $this->estado = $contrato->estado;


        $this->fecha_inicio = $contrato->fecha_inicio
            ? Carbon::parse($contrato->fecha_inicio)->format('Y-m-d')
            : null;

        $this->fecha_fin = $contrato->fecha_fin
            ? Carbon::parse($contrato->fecha_fin)->format('Y-m-d')
            : null;

        $this->precio = $contrato->precio;
    }

    public function hide()
    {
        $this->showModal = false;
    }

    // 💾 Actualizar contrato
    public function updateContrato()
    {
        if (!auth()->user()->can('editar contrato')) {
            abort(403);
        }

        $this->validate();

        $contrato = Contrato::findOrFail($this->contratoId);

        // 🔴 CAPTURAR ANTES DEL UPDATE
        $original = $contrato->getOriginal();

        // 🔥 Limpieza correcta del precio
        $precioFormateado = (int) preg_replace('/\D/', '', $this->precio);

        // 🧠 DETECTAR CAMBIOS ANTES
        $changes = [];

        // Estado
        if ($original['estado'] != $this->estado) {
            $changes[] = "Estado: {$original['estado']} -> {$this->estado}";
        }

        // Precio
        if ($original['precio'] != $precioFormateado) {
            $changes[] = "Precio: {$original['precio']} -> {$precioFormateado}";
        }

        // Tecnología
        if ($original['tecnologia'] != $this->tecnologia) {
            $changes[] = "Tecnología: {$original['tecnologia']} -> {$this->tecnologia}";
        }

        // Fecha inicio
        $fechaInicioOriginal = $original['fecha_inicio']
            ? \Carbon\Carbon::parse($original['fecha_inicio'])->format('Y-m-d')
            : null;

        if ($fechaInicioOriginal != $this->fecha_inicio) {
            $changes[] = "Fecha inicio: {$fechaInicioOriginal} -> {$this->fecha_inicio}";
        }

        // Fecha fin
        $fechaFinOriginal = $original['fecha_fin']
            ? \Carbon\Carbon::parse($original['fecha_fin'])->format('Y-m-d')
            : null;

        if ($fechaFinOriginal != $this->fecha_fin) {
            $changes[] = "Fecha fin: {$fechaFinOriginal} -> {$this->fecha_fin}";
        }

        // ✅ UPDATE (SOLO UNA VEZ)
        $contrato->update([
            'cliente_id' => $this->cliente_id,
            'plan_id' => $this->plan_id,
            'tecnologia' => $this->tecnologia,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
            'precio' => $precioFormateado,
        ]);

        // 📝 MENSAJE FINAL
        $detalle = empty($changes)
            ? 'Actualizacion sin cambios relevantes'
            : implode(', ', $changes);

        // 🎫 CREAR TICKET
        Ticket::create([
            'tipo_reporte' => 'Actualizacion de contrato',
            'situacion' => $detalle,
            'fecha_cierre' => now(),
            'solucion' => 'Actualizacion realizada por ' . auth()->user()->name,
            'estado' => 'cerrado',
            'cliente_id' => $this->cliente_id,
            'user_id' => auth()->id(),
        ]);

        $this->dispatch('cerrar-modal');

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Contrato actualizado exitosamente!'
        );
    }

    // 📊 Estadísticas
    protected function getEstadisticasNodos()
    {
        return Nodo::select('nodos.id', 'nodos.nombre as nodo_nombre')
            ->selectRaw('COUNT(DISTINCT clientes.id) as total_clientes')
            ->selectRaw('SUM(CASE WHEN contratos.estado = "activo" THEN 1 ELSE 0 END) as total_activos')
            ->leftJoin('plans', 'plans.nodo_id', '=', 'nodos.id')
            ->leftJoin('contratos', 'contratos.plan_id', '=', 'plans.id')
            ->leftJoin('clientes', 'clientes.id', '=', 'contratos.cliente_id')
            ->groupBy('nodos.id', 'nodos.nombre')
            ->orderBy('nodos.nombre')
            ->get();
    }

    public function render()
    {
        $query = Contrato::select('contratos.*')
            ->with(['cliente', 'plan.nodo'])
            ->join('clientes', 'clientes.id', '=', 'contratos.cliente_id')
            ->join('plans', 'plans.id', '=', 'contratos.plan_id')
            ->join('nodos', 'nodos.id', '=', 'plans.nodo_id');

        // 🔍 BUSCADOR
        $query->when($this->search, function ($q) {
            $q->where(function ($q2) {
                $q2->where('clientes.nombre', 'like', "%{$this->search}%")
                    ->orWhere('clientes.ip', 'like', "%{$this->search}%")
                    ->orWhere('contratos.tecnologia', 'like', "%{$this->search}%")
                    ->orWhere('contratos.estado', 'like', "%{$this->search}%")
                    ->orWhere('nodos.nombre', 'like', "%{$this->search}%")
                    ->orWhere('clientes.estado', 'like', "%{$this->search}%");
            });
        });

        // 🎯 FILTROS
        $query->when(
            $this->filterEstado,
            fn($q) =>
            $q->where('contratos.estado', $this->filterEstado)
        );

        $query->when(
            $this->filterTecnologia,
            fn($q) =>
            $q->where('contratos.tecnologia', $this->filterTecnologia)
        );

        $query->when(
            $this->filterNodo,
            fn($q) =>
            $q->where('nodos.id', $this->filterNodo)
        );

        $query->when(
            $this->filterEstadoCliente,
            fn($q) =>
            $q->where('clientes.estado', $this->filterEstadoCliente)
        );

        // 📊 TOTAL FILTRADOS
        $totalFiltrados = $query->count();

        // 🔃 ORDENAMIENTO LIMPIO
        $sortMap = [
            'ip' => 'clientes.ip',
            'cliente_id' => 'clientes.nombre',
            'nodo' => 'nodos.nombre',
            'estado_cliente' => 'clientes.estado',
        ];

        $orderField = $sortMap[$this->sortField] ?? 'contratos.' . $this->sortField;

        $contratos = $query
            ->orderBy($orderField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.contratos.contratos-list', [
            'contratos' => $contratos,
            'totalFiltrados' => $totalFiltrados,
            'clientes' => $this->clientesList,
            'planes' => $this->planesList,
            'estadisticasNodos' => $this->getEstadisticasNodos()
        ]);
    }
}
