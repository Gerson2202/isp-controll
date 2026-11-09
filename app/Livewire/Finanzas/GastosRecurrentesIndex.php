<?php

namespace App\Livewire\Finanzas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CategoriaGasto;
use App\Models\GastoRecurrente;
use App\Models\Gasto;
use Carbon\Carbon;

class GastosRecurrentesIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Formulario - Gasto Recurrente Base
    public $buscar = '';
    public $registro_id;
    public $categoria_gasto_id;
    public $concepto;
    public $valor;
    public $valor_formateado;
    public $frecuencia = 'mensual';
    public $dia_ejecucion = 1;
    public $tipo = 'fijo';
    public $activo = true;
    public $descripcion;

    // Modal de pago
    public $mostrarModalPago = false;
    public $gasto_recurrente_id_para_pagar;
    public $metodo_pago_seleccionado = 'efectivo';
    public $fecha_pago_modal;

    // Filtros
    public $mesSeleccionado;
    public $anoSeleccionado;
    public $filtroEstado = 'todos';

    protected function rules()
    {
        return [
            'categoria_gasto_id' => 'required|exists:categorias_gastos,id',
            'concepto' => 'required|string|max:255|unique:gastos_recurrentes,concepto,' . $this->registro_id,
            'valor' => 'required|numeric|min:0',
            'frecuencia' => 'required|in:mensual,quincenal,semanal',
            'dia_ejecucion' => 'required|integer|min:1|max:31',
            'tipo' => 'required|in:fijo,variable',
            'descripcion' => 'nullable|string|max:500'
        ];
    }

    protected $messages = [
        'categoria_gasto_id.required' => 'La categoría es obligatoria',
        'categoria_gasto_id.exists' => 'La categoría seleccionada no existe',
        'concepto.required' => 'El concepto es obligatorio',
        'concepto.max' => 'El concepto no puede tener más de 255 caracteres',
        'concepto.unique' => 'Este concepto ya está registrado',
        'valor.required' => 'El valor es obligatorio',
        'valor.numeric' => 'El valor debe ser un número',
        'valor.min' => 'El valor debe ser mayor a 0',
        'dia_ejecucion.required' => 'El día de ejecución es obligatorio',
        'dia_ejecucion.min' => 'El día debe ser entre 1 y 31',
        'dia_ejecucion.max' => 'El día debe ser entre 1 y 31',
        'tipo.required' => 'El tipo es obligatorio',
        'descripcion.max' => 'La descripción no puede tener más de 500 caracteres',
    ];

    public function mount()
    {
        $this->mesSeleccionado = Carbon::now()->month;
        $this->anoSeleccionado = Carbon::now()->year;
        $this->fecha_pago_modal = Carbon::now()->toDateString();
    }

    public function updatedValor($value)
    {
        $limpio = preg_replace('/[^0-9]/', '', $value);

        if ($limpio) {
            $this->valor = (int) $limpio;
            $this->valor_formateado = number_format($this->valor, 0, ',', '.');
        } else {
            $this->valor = null;
            $this->valor_formateado = '';
        }
    }

    public function guardar()
    {
        if ($this->valor_formateado) {
            $this->valor = (int) str_replace('.', '', $this->valor_formateado);
        }

        $this->validate();

        try {
            $data = [
                'categorias_gasto_id' => $this->categoria_gasto_id,
                'concepto' => $this->concepto,
                'valor' => $this->valor,
                'frecuencia' => $this->frecuencia,
                'dia_ejecucion' => $this->dia_ejecucion,
                'tipo' => $this->tipo,
                'activo' => $this->activo,
                'descripcion' => $this->descripcion,
            ];

            if ($this->registro_id) {
                $gasto = GastoRecurrente::find($this->registro_id);
                if ($gasto) {
                    $gasto->update($data);
                    $this->dispatch('notify', type: 'success', message: 'Gasto recurrente actualizado correctamente');
                } else {
                    $this->dispatch('notify', type: 'error', message: 'Registro no encontrado');
                }
            } else {
                GastoRecurrente::create($data);
                $this->dispatch('notify', type: 'success', message: 'Gasto recurrente creado correctamente');
            }

            $this->limpiar();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function editar($id)
    {
        $item = GastoRecurrente::findOrFail($id);

        $this->registro_id = $item->id;
        $this->categoria_gasto_id = $item->categorias_gasto_id;
        $this->concepto = $item->concepto;
        $this->valor = $item->valor;
        $this->valor_formateado = number_format($item->valor, 0, ',', '.');
        $this->frecuencia = $item->frecuencia;
        $this->dia_ejecucion = $item->dia_ejecucion;
        $this->tipo = $item->tipo;
        $this->activo = $item->activo;
        $this->descripcion = $item->descripcion;

        $this->dispatch('notify', type: 'info', message: 'Editando: ' . $item->concepto);
    }

    public function cambiarEstado($id)
    {
        try {
            $item = GastoRecurrente::findOrFail($id);
            $nuevoEstado = !$item->activo;
            $item->update(['activo' => $nuevoEstado]);

            $this->dispatch('notify', type: 'success', message: 'Gasto ' . ($nuevoEstado ? 'activado' : 'desactivado') . ' correctamente');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error al cambiar el estado');
        }
    }

    /**
     * Abrir modal para registrar pago
     */
    public function abrirModalPago($gastoRecurrenteId)
    {
        $this->gasto_recurrente_id_para_pagar = $gastoRecurrenteId;
        $this->metodo_pago_seleccionado = 'efectivo';
        $this->fecha_pago_modal = Carbon::now()->toDateString();
        $this->mostrarModalPago = true;
    }

    public function cerrarModalPago()
    {
        $this->mostrarModalPago = false;
        $this->gasto_recurrente_id_para_pagar = null;
        $this->metodo_pago_seleccionado = 'efectivo';
        $this->fecha_pago_modal = null;
    }

    /**
     * Guardar el pago en la tabla gastos
     */
    public function guardarPago()
    {
        try {
            $gastoBase = GastoRecurrente::findOrFail($this->gasto_recurrente_id_para_pagar);

            // Validar que la frecuencia permita más pagos
            $pagosEnMes = $this->contarPagosDelGastoEnMes($gastoBase->concepto);
            $limitePagos = $this->obtenerLimitePagosPorFrecuencia($gastoBase->frecuencia);

            if ($pagosEnMes >= $limitePagos) {
                $this->dispatch('notify', type: 'warning', message: 'Ya ha alcanzado el límite de pagos para este mes según la frecuencia.');
                return;
            }

            // Crear registro en tabla gastos
            Gasto::create([
                'categorias_gasto_id' => $gastoBase->categorias_gasto_id,
                'concepto' => $gastoBase->concepto,
                'valor' => $gastoBase->valor,
                'fecha_gasto' => $this->fecha_pago_modal,
                'tipo' => $gastoBase->tipo,
                'estado' => 'pagado',
                'metodo_pago' => $this->metodo_pago_seleccionado,
                'descripcion' => $gastoBase->descripcion,
                'numero_documento' => null,
                'beneficiario' => null,
                // 🔄 Relación con el gasto recurrente
                'gasto_recurrente_id' => $gastoBase->id,
                'user_id' => auth()->id(),
            ]);

            $this->dispatch('notify', type: 'success', message: 'Pago registrado correctamente');
            $this->cerrarModalPago();
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error al guardar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Contar cuántos pagos tiene un concepto en el mes actual
     * Se busca por concepto porque los pagos se guardan en tabla gastos
     */
    private function contarPagosDelGastoEnMes($concepto)
    {
        return Gasto::where('concepto', $concepto)
            ->whereYear('fecha_gasto', $this->anoSeleccionado)
            ->whereMonth('fecha_gasto', $this->mesSeleccionado)
            ->count();
    }

    /**
     * Obtener el límite de pagos según la frecuencia
     */
    private function obtenerLimitePagosPorFrecuencia($frecuencia)
    {
        return match ($frecuencia) {
            'mensual' => 1,
            'quincenal' => 2,
            'semanal' => 4,
            default => 1,
        };
    }

    /**
     * Cambiar el mes/año del filtro
     */
    public function cambiarMes($direccion)
    {
        $fecha = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1);

        if ($direccion === 'anterior') {
            $fecha->subMonth();
        } elseif ($direccion === 'siguiente') {
            $fecha->addMonth();
        }

        $this->mesSeleccionado = $fecha->month;
        $this->anoSeleccionado = $fecha->year;
    }

    public function eliminar($id)
    {
        try {
            $item = GastoRecurrente::findOrFail($id);

            // No permitir eliminar si tiene pagos registrados
            $tienePagos = Gasto::where('concepto', $item->concepto)->exists();

            if ($tienePagos) {
                $this->dispatch('notify', type: 'error', message: 'No se puede eliminar este gasto porque tiene pagos registrados');
                return;
            }

            $concepto = $item->concepto;
            $item->delete();

            $this->dispatch('notify', type: 'success', message: 'Gasto recurrente "' . $concepto . '" eliminado correctamente');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Error al eliminar el gasto');
        }
    }

    public function limpiar()
    {
        $this->reset([
            'registro_id',
            'categoria_gasto_id',
            'concepto',
            'valor',
            'valor_formateado',
            'descripcion'
        ]);

        $this->frecuencia = 'mensual';
        $this->dia_ejecucion = 1;
        $this->tipo = 'fijo';
        $this->activo = true;

        $this->resetValidation();

        $this->dispatch('notify', type: 'info', message: 'Formulario limpiado');
    }

    /**
     * Calcular total de pagos pendientes según frecuencia
     */
    private function calcularTotalPendiente()
    {
        $gastosActivos = GastoRecurrente::where('activo', true)->get();
        $totalPendiente = 0;

        foreach ($gastosActivos as $gasto) {
            $pagosEnMes = $this->contarPagosDelGastoEnMes($gasto->concepto);
            $limitePagos = $this->obtenerLimitePagosPorFrecuencia($gasto->frecuencia);
            $pagosPendientes = max(0, $limitePagos - $pagosEnMes);

            // Suma el valor de los pagos pendientes
            $totalPendiente += $gasto->valor * $pagosPendientes;
        }

        return $totalPendiente;
    }

    public function render()
    {
        $categorias = CategoriaGasto::where('activo', 1)
            ->orderBy('nombre')
            ->get();

        // Obtener todos los gastos recurrentes activos
        $gastosBase = GastoRecurrente::orderBy('concepto')->get();

        // Filtrar por búsqueda
        if ($this->buscar) {
            $gastosBase = $gastosBase->filter(function ($item) {
                return stripos($item->concepto, $this->buscar) !== false ||
                    stripos($item->descripcion ?? '', $this->buscar) !== false ||
                    ($item->categoria && stripos($item->categoria->nombre, $this->buscar) !== false);
            });
        }

        // Calcular estado de pagos en el mes actual para cada gasto
        $gastosBase = $gastosBase->map(function ($item) {
            $pagosEnMes = Gasto::where('concepto', $item->concepto)
                ->whereYear('fecha_gasto', $this->anoSeleccionado)
                ->whereMonth('fecha_gasto', $this->mesSeleccionado)
                ->count();

            $limitePagos = $this->obtenerLimitePagosPorFrecuencia($item->frecuencia);

            $item->pagos_realizados = $pagosEnMes;
            $item->pagos_pendientes = max(0, $limitePagos - $pagosEnMes);
            $item->limite_pagos = $limitePagos;
            $item->puede_pagar = $item->pagos_pendientes > 0;

            return $item;
        });

        // Filtrar por estado
        if ($this->filtroEstado === 'pagados') {
            $gastosBase = $gastosBase->filter(function ($item) {
                return $item->pagos_realizados == $item->limite_pagos;
            });
        } elseif ($this->filtroEstado === 'pendientes') {
            $gastosBase = $gastosBase->filter(function ($item) {
                return $item->pagos_pendientes > 0;
            });
        }

        // Crear paginación manual
        $perPage = 10;
        $currentPage = $this->page ?? 1;
        $total = $gastosBase->count();
        $items = $gastosBase->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $gastosPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        // Totales
        $totalGastosBase = GastoRecurrente::where('activo', true)->sum('valor');

        $totalPagadosMes = Gasto::whereYear('fecha_gasto', $this->anoSeleccionado)
            ->whereMonth('fecha_gasto', $this->mesSeleccionado)
            ->sum('valor');

        // 🔥 Total pendiente calculado correctamente según frecuencia
        $totalPendiente = $this->calcularTotalPendiente();

        $nombreMes = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->translatedFormat('F Y');

        return view('livewire.finanzas.gastos-recurrentes-index', compact(
            'categorias',
            'gastosPaginated',
            'totalGastosBase',
            'totalPagadosMes',
            'totalPendiente',
            'nombreMes'
        ));
    }
}
