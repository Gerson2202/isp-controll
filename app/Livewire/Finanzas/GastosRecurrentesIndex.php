<?php

namespace App\Livewire\Finanzas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CategoriaGasto;
use App\Models\GastoRecurrente;
use App\Models\SaldoAcumulado;
use Carbon\Carbon;

class GastosRecurrentesIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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

    // Filtros
    public $mesSeleccionado;
    public $anoSeleccionado;
    public $filtroEstado = 'todos';

    protected function rules()
    {
        return [
            'categoria_gasto_id' => 'required|exists:categorias_gastos,id',
            'concepto' => 'required|string|max:255|unique:gastos_recurrentes,concepto,' . $this->registro_id . ',id,ano,NULL,mes,NULL',
            'valor' => 'required|numeric|min:0',
            'frecuencia' => 'required|in:mensual,quincenal,anual',
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
            $this->valor = str_replace('.', '', $this->valor_formateado);
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
                'ano' => null,
                'mes' => null,
                'pagado' => false,
            ];

            if ($this->registro_id) {
                $gasto = GastoRecurrente::find($this->registro_id);
                if ($gasto) {
                    $gasto->update($data);
                    $this->dispatch(
                        'notify',
                        type: 'success',
                        message: 'Gasto recurrente actualizado correctamente'
                    );
                } else {
                    $this->dispatch(
                        'notify',
                        type: 'error',
                        message: 'Registro no encontrado'
                    );
                }
            } else {
                GastoRecurrente::create($data);
                $this->dispatch(
                    'notify',
                    type: 'success',
                    message: 'Gasto recurrente creado correctamente'
                );
            }

            $this->limpiar();

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al guardar: ' . $e->getMessage()
            );
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

        $this->dispatch(
            'notify',
            type: 'info',
            message: 'Editando: ' . $item->concepto
        );
    }

    public function cambiarEstado($id)
    {
        try {
            $item = GastoRecurrente::findOrFail($id);
            $nuevoEstado = !$item->activo;
            $item->update(['activo' => $nuevoEstado]);

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Gasto ' . ($nuevoEstado ? 'activado' : 'desactivado') . ' correctamente'
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al cambiar el estado'
            );
        }
    }

    /**
     * Marcar un gasto recurrente como pagado este mes
     */
    public function marcarComoPagado($id)
    {
        try {
            $gastoBase = GastoRecurrente::findOrFail($id);

            // Verificar que sea un gasto base (sin año y mes)
            if ($gastoBase->ano !== null || $gastoBase->mes !== null) {
                $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'Este no es un gasto base'
                );
                return;
            }

            // Verificar si ya está pagado este mes
            if (GastoRecurrente::yaPagadoEsteMes($gastoBase->concepto, $this->mesSeleccionado, $this->anoSeleccionado)) {
                $this->dispatch(
                    'notify',
                    type: 'warning',
                    message: 'Este gasto ya fue pagado en ' . Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->format('F Y')
                );
                return;
            }

            // Crear nuevo registro de pago
            $fecha = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, $gastoBase->dia_ejecucion);
            if ($fecha->isFuture()) {
                $fecha = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, min($gastoBase->dia_ejecucion, Carbon::now()->day));
            }

            GastoRecurrente::marcarComoPagado(
                $gastoBase->concepto,
                $gastoBase->valor,
                $gastoBase->categorias_gasto_id,
                $fecha,
                $gastoBase->dia_ejecucion
            );

            // Recalcular saldo acumulado
            SaldoAcumulado::recalcularMes($this->anoSeleccionado, $this->mesSeleccionado);

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Gasto "' . $gastoBase->concepto . '" marcado como pagado en ' . $fecha->format('F Y')
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al marcar como pagado: ' . $e->getMessage()
            );
        }
    }

    /**
     * Eliminar un registro de pago (anular el pago)
     */
    public function anularPago($id)
    {
        try {
            $registro = GastoRecurrente::findOrFail($id);

            // Verificar que sea un registro de pago (con año y mes)
            if ($registro->ano === null || $registro->mes === null) {
                $this->dispatch(
                    'notify',
                    type: 'error',
                    message: 'Este es un gasto base, no se puede anular'
                );
                return;
            }

            $concepto = $registro->concepto;
            $mes = $registro->mes;
            $ano = $registro->ano;

            $registro->delete();

            // Recalcular saldo acumulado
            SaldoAcumulado::recalcularMes($ano, $mes);

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Pago de "' . $concepto . '" anulado correctamente'
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al anular el pago: ' . $e->getMessage()
            );
        }
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
            
            // No permitir eliminar un gasto base que tiene pagos registrados
            if ($item->ano === null && $item->mes === null) {
                $tienePagos = GastoRecurrente::where('concepto', $item->concepto)
                    ->whereNotNull('ano')
                    ->whereNotNull('mes')
                    ->where('pagado', true)
                    ->exists();
                    
                if ($tienePagos) {
                    $this->dispatch(
                        'notify',
                        type: 'error',
                        message: 'No se puede eliminar este gasto porque tiene pagos registrados'
                    );
                    return;
                }
            }

            $concepto = $item->concepto;
            $item->delete();

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Gasto recurrente "' . $concepto . '" eliminado correctamente'
            );

        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al eliminar el gasto'
            );
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

        $this->dispatch(
            'notify',
            type: 'info',
            message: 'Formulario limpiado'
        );
    }

   public function render()
{
    $categorias = CategoriaGasto::where('activo', 1)
        ->orderBy('nombre')
        ->get();

    // 🔥 OBTENER GASTOS BASE CON SU ESTADO DE PAGO
    $gastosBase = GastoRecurrente::getGastosBaseConEstado(
        $this->mesSeleccionado,
        $this->anoSeleccionado
    );

    // Filtrar por búsqueda
    if ($this->buscar) {
        $gastosBase = $gastosBase->filter(function ($item) {
            return stripos($item->concepto, $this->buscar) !== false ||
                stripos($item->descripcion, $this->buscar) !== false ||
                ($item->categoria && stripos($item->categoria->nombre, $this->buscar) !== false);
        });
    }

    // Filtrar por estado
    if ($this->filtroEstado === 'pagados') {
        $gastosBase = $gastosBase->filter(function ($item) {
            return $item->pagado_este_mes === true;
        });
    } elseif ($this->filtroEstado === 'pendientes') {
        $gastosBase = $gastosBase->filter(function ($item) {
            return $item->pagado_este_mes === false;
        });
    }

    // 🔥 CREAR PAGINACIÓN MANUAL
    $perPage = 10;
    $currentPage = $this->page ?? 1;
    $total = $gastosBase->count();
    $items = $gastosBase->slice(($currentPage - 1) * $perPage, $perPage);

    // 🔥 CREAR EL PAGINADOR
    $gastosPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $items,
        $total,
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'pageName' => 'page']
    );

    // 🔥 OBTENER REGISTROS DE PAGO DEL MES
    $pagosDelMes = GastoRecurrente::getPagadosDelMes(
        $this->mesSeleccionado,
        $this->anoSeleccionado
    );

    // 🔥 TOTALES
    $totalGastosBase = GastoRecurrente::whereNull('ano')
        ->whereNull('mes')
        ->where('activo', true)
        ->sum('valor');

    $totalPagadosMes = GastoRecurrente::getTotalPagadosMes(
        $this->mesSeleccionado,
        $this->anoSeleccionado
    );

    $nombreMes = Carbon::create($this->anoSeleccionado, $this->mesSeleccionado, 1)->translatedFormat('F Y');

    return view('livewire.finanzas.gastos-recurrentes-index', compact(
        'categorias',
        'gastosPaginated',  // 🔥 CAMBIADO: ahora es paginador
        'pagosDelMes',
        'totalGastosBase',
        'totalPagadosMes',
        'nombreMes'
    ));
}
}