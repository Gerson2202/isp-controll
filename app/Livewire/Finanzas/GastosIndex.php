<?php

namespace App\Livewire\Finanzas;

use App\Models\Gasto;
use App\Models\CategoriaGasto;
use Livewire\Component;
use Livewire\WithPagination;

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

    protected function rules()
    {
        return [
            'categorias_gasto_id' => 'required|exists:categorias_gastos,id',
            'concepto' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'tipo' => 'required|in:fijo,variable',
            'estado' => 'required|in:pagado,pendiente',
            'descripcion' => 'nullable|string|max:500'
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
        // Limpiar formato del valor
        if ($this->valor) {
            $this->valor = str_replace(['.', ','], '', $this->valor);
        }

        $this->validate();

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
                'user_id' => auth()->id()
            ]
        );

        $this->limpiar();

        $this->dispatch(
            'notify',
            type: 'success',
            message: $this->gasto_id ? 'Gasto actualizado correctamente' : 'Gasto registrado correctamente'
        );
    }

    public function editar($id)
    {
        $gasto = Gasto::findOrFail($id);

        $this->gasto_id = $gasto->id;
        $this->categorias_gasto_id = $gasto->categorias_gasto_id;
        $this->concepto = $gasto->concepto;
        $this->valor = $gasto->valor;
        $this->fecha_gasto = $gasto->fecha_gasto->format('Y-m-d');
        $this->tipo = $gasto->tipo;
        $this->estado = $gasto->estado;
        $this->descripcion = $gasto->descripcion;

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
            $gasto->delete();

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
            'descripcion'
        ]);

        $this->fecha_gasto = now()->format('Y-m-d');
        $this->tipo = 'variable';
        $this->estado = 'pagado';
        
        $this->resetValidation();

    }

    public function render()
    {
        $categorias = CategoriaGasto::where('activo', true)
            ->orderBy('nombre')
            ->get();

        $gastos = Gasto::with(['categoria', 'usuario'])
            ->when($this->buscar, function ($query) {
                $query->where('concepto', 'like', '%' . $this->buscar . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->mes, function ($query) {
                $query->whereMonth('fecha_gasto', $this->mes);
            })
            ->when($this->anio, function ($query) {
                $query->whereYear('fecha_gasto', $this->anio);
            })
            ->orderBy('fecha_gasto', 'desc')
            ->paginate(15);

        // Calcular totales
        $totalGastos = $gastos->sum('valor');
        $totalPagado = $gastos->where('estado', 'pagado')->sum('valor');
        $totalPendiente = $gastos->where('estado', 'pendiente')->sum('valor');

        // Obtener meses disponibles
        $mesesDisponibles = Gasto::selectRaw('YEAR(fecha_gasto) as anio, MONTH(fecha_gasto) as mes')
            ->distinct()
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return view('livewire.finanzas.gastos-index', compact(
            'gastos', 
            'categorias', 
            'totalGastos', 
            'totalPagado', 
            'totalPendiente',
            'mesesDisponibles'
        ));
    }
}