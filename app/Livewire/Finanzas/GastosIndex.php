<?php

namespace App\Livewire\Finanzas;

use App\Models\Gasto;
use App\Models\CategoriaGasto;
use App\Models\SaldoAcumulado;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class GastosIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscar = '';
    public $mes = '';
    public $anio = '';

    public $gasto_id;
    public $categorias_gasto_id;
    public $concepto;
    public $valor;
    public $fecha_gasto;
    public $tipo = 'variable';
    public $estado = 'pagado';
    public $descripcion;
    public $metodo_pago = 'efectivo';

    public function updatingBuscar()
    {
        $this->resetPage();
    }

    public function updatingMes()
    {
        $this->resetPage();
    }

    public function updatingAnio()
    {
        $this->resetPage();
    }
    protected function rules()
    {
        return [
            'categorias_gasto_id' => 'required|exists:categorias_gastos,id',
            'concepto' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'tipo' => 'required|in:fijo,variable',
            'estado' => 'required|in:pagado,pendiente',
            'descripcion' => 'nullable|string|max:500',
        ];
    }

    protected $messages = [
        'categorias_gasto_id.required' => 'Debes seleccionar una categoría',
        'categorias_gasto_id.exists' => 'La categoría seleccionada no existe',
        'concepto.required' => 'El concepto es obligatorio',
        'concepto.max' => 'El concepto no puede tener más de 255 caracteres',
        'valor.required' => 'El valor es obligatorio',
        'valor.numeric' => 'El valor debe ser un número',
        'valor.min' => 'El valor debe ser mayor a 0',
        'fecha_gasto.required' => 'La fecha es obligatoria',
        'fecha_gasto.date' => 'La fecha no es válida',
    ];

    public function mount()
    {
        $this->fecha_gasto = now()->format('Y-m-d');
        $this->mes = now()->format('m');
        $this->anio = now()->format('Y');
    }

    public function guardar()
    {
        if ($this->valor !== null && $this->valor !== '') {

            // El usuario escribe:
            // 20.000
            // 100.000
            // 1.500.000

            // Quitamos los puntos de miles
            $this->valor = str_replace('.', '', $this->valor);

            // Convertimos a número
            $this->valor = (float) $this->valor;
        }

        $this->validate();

        // Guardar fecha original si es edición
        $fechaOriginal = null;

        if ($this->gasto_id) {
            $gastoOriginal = Gasto::find($this->gasto_id);

            if ($gastoOriginal) {
                $fechaOriginal = $gastoOriginal->fecha_gasto;
            }
        }

        Gasto::updateOrCreate(
            ['id' => $this->gasto_id],
            [
                'categorias_gasto_id' => $this->categorias_gasto_id,
                'concepto' => $this->concepto,
                'valor' => $this->valor,
                'fecha_gasto' => $this->fecha_gasto,
                'tipo' => $this->tipo,
                'estado' => $this->estado,
                'descripcion' => $this->descripcion,
                'metodo_pago' => $this->metodo_pago,
                'user_id' => auth()->id(),
            ]
        );

        $fecha = Carbon::parse($this->fecha_gasto);

        SaldoAcumulado::recalcularMes(
            $fecha->year,
            $fecha->month
        );

        if ($fechaOriginal && $fechaOriginal != $this->fecha_gasto) {

            $fechaAnt = Carbon::parse($fechaOriginal);

            SaldoAcumulado::recalcularMes(
                $fechaAnt->year,
                $fechaAnt->month
            );
        }

        $esEdicion = $this->gasto_id ? true : false;

        $this->limpiar();

        $this->dispatch(
            'notify',
            type: 'success',
            message: $esEdicion
                ? 'Gasto actualizado correctamente'
                : 'Gasto registrado correctamente'
        );
    }

    public function editar($id)
    {
        $gasto = Gasto::findOrFail($id);

        $this->gasto_id = $gasto->id;
        $this->categorias_gasto_id = $gasto->categorias_gasto_id;
        $this->concepto = $gasto->concepto;

        // Mostrar el valor con separador de miles
        // BD: 20000.00
        // Input: 20.000
        $this->valor = number_format(
            (float) $gasto->valor,
            0,
            ',',
            '.'
        );

        $this->fecha_gasto = $gasto->fecha_gasto->format('Y-m-d');
        $this->tipo = $gasto->tipo;
        $this->estado = $gasto->estado;
        $this->descripcion = $gasto->descripcion;
        $this->metodo_pago = $gasto->metodo_pago;

        $this->dispatch(
            'notify',
            type: 'info',
            message: 'Editando gasto: ' . $gasto->concepto
        );
    }

    public function eliminar($id)
    {
        try {

            $gasto = Gasto::findOrFail($id);

            $concepto = $gasto->concepto;
            $fechaGasto = $gasto->fecha_gasto;

            $gasto->delete();

            // Recalcular saldo después de eliminar
            $fecha = Carbon::parse($fechaGasto);

            SaldoAcumulado::recalcularMes(
                $fecha->year,
                $fecha->month
            );

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Gasto "' . $concepto . '" eliminado correctamente'
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
            'gasto_id',
            'categorias_gasto_id',
            'concepto',
            'valor',
            'descripcion',
        ]);

        $this->fecha_gasto = now()->format('Y-m-d');
        $this->tipo = 'variable';
        $this->estado = 'pagado';
        $this->metodo_pago = 'efectivo';

        $this->resetValidation();
    }

    public function render()
    {
        $categorias = CategoriaGasto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $query = Gasto::with(['categoria', 'usuario'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where(
                        'concepto',
                        'like',
                        '%' . $this->buscar . '%'
                    )
                        ->orWhere(
                            'descripcion',
                            'like',
                            '%' . $this->buscar . '%'
                        );
                });
            })
            ->when($this->mes, function ($query) {
                $query->whereMonth('fecha_gasto', $this->mes);
            })
            ->when($this->anio, function ($query) {
                $query->whereYear('fecha_gasto', $this->anio);
            });

        // TODOS los registros que cumplen los filtros
        $totalGastos = (clone $query)->sum('valor');

        $totalPagado = (clone $query)
            ->where('estado', 'pagado')
            ->sum('valor');

        $totalPendiente = (clone $query)
            ->where('estado', 'pendiente')
            ->sum('valor');

        // Solo aquí se limita a 15 por página
        $gastos = $query
            ->orderBy('fecha_gasto', 'desc')
            ->paginate(15);

        $mesesDisponibles = Gasto::selectRaw(
            'YEAR(fecha_gasto) as anio, MONTH(fecha_gasto) as mes'
        )
            ->distinct()
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view(
            'livewire.finanzas.gastos-index',
            compact(
                'gastos',
                'categorias',
                'totalGastos',
                'totalPagado',
                'totalPendiente',
                'mesesDisponibles'
            )
        );
    }
}
