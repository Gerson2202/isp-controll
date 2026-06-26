<?php

namespace App\Livewire\Finanzas;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CategoriaGasto;
use App\Models\GastoRecurrente;

class GastosRecurrentesIndex extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $buscar = '';
    public $registro_id;
    public $categoria_gasto_id;
    public $concepto;
    public $valor;
    public $valor_formateado; // Nuevo campo para el valor con formato
    public $frecuencia = 'mensual';
    public $dia_ejecucion = 1;
    public $tipo = 'fijo';
    public $activo = true;
    public $descripcion;

    protected function rules()
    {
        return [
            'categoria_gasto_id' => 'required|exists:categorias_gastos,id',
            'concepto' => 'required|string|max:255',
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
        'valor.required' => 'El valor es obligatorio',
        'valor.numeric' => 'El valor debe ser un número',
        'valor.min' => 'El valor debe ser mayor a 0',
        'dia_ejecucion.required' => 'El día de ejecución es obligatorio',
        'dia_ejecucion.min' => 'El día debe ser entre 1 y 31',
        'dia_ejecucion.max' => 'El día debe ser entre 1 y 31',
        'tipo.required' => 'El tipo es obligatorio',
        'descripcion.max' => 'La descripción no puede tener más de 500 caracteres',
    ];

    // Método para formatear el valor al escribir
    public function updatedValor($value)
    {
        // Eliminar cualquier caracter que no sea número
        $limpio = preg_replace('/[^0-9]/', '', $value);
        
        if ($limpio) {
            // Convertir a número entero
            $this->valor = (int) $limpio;
            // Formatear con puntos
            $this->valor_formateado = number_format($this->valor, 0, ',', '.');
        } else {
            $this->valor = null;
            $this->valor_formateado = '';
        }
    }

    // Método para limpiar el formato al guardar
    public function guardar()
    {
        // Si hay valor formateado, limpiarlo para guardar
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
                'descripcion' => $this->descripcion
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

    public function eliminar($id)
    {
        try {
            $item = GastoRecurrente::findOrFail($id);
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

        $registros = GastoRecurrente::with('categoria')
            ->when($this->buscar, function ($q) {
                $q->where('concepto', 'like', '%' . $this->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->buscar . '%');
            })
            ->latest()
            ->paginate(10);

        // Calcular totales
        $totalMensual = $registros->sum('valor');

        return view('livewire.finanzas.gastos-recurrentes-index', compact(
            'categorias', 
            'registros',
            'totalMensual'
        ));
    }
}