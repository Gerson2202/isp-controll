<?php

namespace App\Livewire\Finanzas;

use App\Models\Cliente;
use App\Models\Ingreso;
use App\Models\SaldoAcumulado;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class GestionIngresos extends Component
{
    use WithPagination;

    public $concepto;
    public $monto;
    public $fecha_ingreso;
    public $tipo = 'otro';
    public $cliente_id;
    public $cliente_nombre = '';
    public $descripcion;
    public $metodo_pago;
    public $numero_documento;

    public $searchCliente = '';
    public $showClienteList = false;

    public $mostrarFormulario = false;
    public $ingresoEditando = null;
    public $search = '';

    public $filtroEstado = '';
    public $filtroTipo = '';
    public $filtroMes = '';

    public $meses = [
        '' => 'Todos los meses',
        '01' => 'Enero',
        '02' => 'Febrero',
        '03' => 'Marzo',
        '04' => 'Abril',
        '05' => 'Mayo',
        '06' => 'Junio',
        '07' => 'Julio',
        '08' => 'Agosto',
        '09' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre'
    ];

    protected $rules = [
        'concepto' => 'required|string|max:255',
        'monto' => 'required|numeric|min:0.01',
        'fecha_ingreso' => 'required|date',
        'tipo' => 'required|in:instalacion,servicio_extra,venta_producto,consultoria,otro',
        'cliente_id' => 'nullable|exists:clientes,id',
        'descripcion' => 'nullable|string',
        'metodo_pago' => 'nullable|string|max:50',
        'numero_documento' => 'nullable|string|max:50',
    ];

    protected $messages = [
        'concepto.required' => 'El concepto es obligatorio.',
        'concepto.max' => 'El concepto no puede tener más de 255 caracteres.',
        'monto.required' => 'El monto es obligatorio.',
        'monto.numeric' => 'El monto debe ser un número válido.',
        'monto.min' => 'El monto debe ser mayor a 0.',
        'fecha_ingreso.required' => 'La fecha es obligatoria.',
        'fecha_ingreso.date' => 'La fecha no es válida.',
        'tipo.required' => 'El tipo es obligatorio.',
        'tipo.in' => 'El tipo seleccionado no es válido.',
        'cliente_id.exists' => 'El cliente seleccionado no existe.',
        'metodo_pago.max' => 'El método de pago no puede tener más de 50 caracteres.',
        'numero_documento.max' => 'El número de documento no puede tener más de 50 caracteres.',
    ];

    public function render()
    {
        $anioActual = Carbon::now()->year;

        // 🔥 1. PRIMERO: Construir la consulta base (sin paginar)
        $query = Ingreso::with(['cliente', 'usuario'])
            ->when($this->search, function ($query) {
                $query->where('concepto', 'like', '%' . $this->search . '%')
                    ->orWhere('numero_documento', 'like', '%' . $this->search . '%');
            })
            ->when($this->filtroEstado, function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->when($this->filtroTipo, function ($query) {
                $query->where('tipo', $this->filtroTipo);
            })
            ->when($this->filtroMes, function ($query) use ($anioActual) {
                $query->whereYear('fecha_ingreso', $anioActual)
                    ->whereMonth('fecha_ingreso', $this->filtroMes);
            });

        // 🔥 2. CALCULAR TOTALES (sobre TODOS los registros filtrados)
        $totalIngresos = $query->sum('monto');
        $totalConfirmados = (clone $query)->where('estado', 'confirmado')->sum('monto');
        $totalAnulados = (clone $query)->where('estado', 'anulado')->sum('monto');

        // 🔥 3. PAGINAR (sobre la misma consulta)
        $ingresos = $query
            ->orderBy('fecha_ingreso', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // Buscar clientes
        $clientes = collect();
        if (strlen($this->searchCliente) > 0) {
            $clientes = Cliente::whereHas('contratos', function ($query) {
                $query->where('estado', 'activo');
            })
                ->where('nombre', 'like', '%' . $this->searchCliente . '%')
                ->orderBy('nombre')
                ->limit(10)
                ->get(['id', 'nombre']);
        }

        return view('livewire.finanzas.gestion-ingresos', [
            'ingresos' => $ingresos,
            'clientes' => $clientes,
            'anioActual' => $anioActual,
            'totalIngresos' => $totalIngresos,
            'totalConfirmados' => $totalConfirmados,
            'totalAnulados' => $totalAnulados,
        ]);
    }

    public function mount()
    {
        $this->filtroMes = Carbon::now()->format('m');
        $this->fecha_ingreso = Carbon::now()->format('Y-m-d');
    }

    // 🔥 MÉTODOS PARA RESETEAR LA PÁGINA CUANDO CAMBIAN LOS FILTROS
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    public function updatedFiltroTipo()
    {
        $this->resetPage();
    }

    public function updatedFiltroMes()
    {
        $this->resetPage();
    }

    public function limpiarFiltros()
    {
        $this->filtroEstado = '';
        $this->filtroTipo = '';
        $this->filtroMes = '';
        $this->search = '';
        $this->resetPage();
    }

    public function selectCliente($id, $nombre)
    {
        $this->cliente_id = $id;
        $this->cliente_nombre = $nombre;
        $this->searchCliente = $nombre;
        $this->showClienteList = false;
    }

    public function clearCliente()
    {
        $this->cliente_id = null;
        $this->cliente_nombre = '';
        $this->searchCliente = '';
        $this->showClienteList = false;
    }

    public function guardarIngreso()
    {
        $this->validate();

        $montoLimpio = $this->limpiarMonto($this->monto);

        $ingreso = Ingreso::create([
            'concepto' => $this->concepto,
            'monto' => $montoLimpio,
            'fecha_ingreso' => $this->fecha_ingreso,
            'tipo' => $this->tipo,
            'cliente_id' => $this->cliente_id,
            'descripcion' => $this->descripcion,
            'metodo_pago' => $this->metodo_pago,
            'numero_documento' => $this->numero_documento,
            'user_id' => Auth::id(),
            'estado' => 'confirmado',
        ]);

        // 🔥 ACTUALIZAR SALDO ACUMULADO DESPUÉS DE CREAR
        $this->actualizarSaldoAcumulado($this->fecha_ingreso);

        $this->resetearFormulario();

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Ingreso registrado exitosamente #' . $ingreso->id
        );
    }

    public function limpiarMonto($valor)
    {
        if (is_string($valor)) {
            $valor = str_replace(['.', ','], '', $valor);
        }
        return number_format((float) $valor, 2, '.', '');
    }

    public function editarIngreso($id)
    {
        $this->resetValidation();

        $ingreso = Ingreso::findOrFail($id);
        $this->ingresoEditando = $ingreso->id;

        $this->concepto = $ingreso->concepto;
        $this->monto = $ingreso->monto;
        $this->fecha_ingreso = $ingreso->fecha_ingreso->format('Y-m-d');
        $this->tipo = $ingreso->tipo;

        $this->cliente_id = $ingreso->cliente_id;
        $this->cliente_nombre = $ingreso->cliente?->nombre ?? '';
        $this->searchCliente = $this->cliente_nombre;

        $this->descripcion = $ingreso->descripcion;
        $this->metodo_pago = $ingreso->metodo_pago;
        $this->numero_documento = $ingreso->numero_documento;

        $this->mostrarFormulario = true;
    }

    public function actualizarIngreso()
    {
        $this->validate();

        $montoLimpio = $this->limpiarMonto($this->monto);

        $ingreso = Ingreso::findOrFail($this->ingresoEditando);
        
        // Guardar fecha original antes de actualizar
        $fechaOriginal = $ingreso->fecha_ingreso->format('Y-m-d');
        
        $ingreso->update([
            'concepto' => $this->concepto,
            'monto' => $montoLimpio,
            'fecha_ingreso' => $this->fecha_ingreso,
            'tipo' => $this->tipo,
            'cliente_id' => $this->cliente_id,
            'descripcion' => $this->descripcion,
            'metodo_pago' => $this->metodo_pago,
            'numero_documento' => $this->numero_documento,
        ]);

        // 🔥 ACTUALIZAR SALDO ACUMULADO PARA LA NUEVA FECHA
        $this->actualizarSaldoAcumulado($this->fecha_ingreso);
        
        // 🔥 SI CAMBIÓ LA FECHA, ACTUALIZAR TAMBIÉN EL MES ANTERIOR
        if ($fechaOriginal != $this->fecha_ingreso) {
            $this->actualizarSaldoAcumulado($fechaOriginal);
        }

        $this->resetearFormulario();

        $this->dispatch(
            'notify',
            type: 'success',
            message: 'Ingreso actualizado exitosamente'
        );
    }

    public function cambiarEstado($id, $nuevoEstado)
    {
        $ingreso = Ingreso::findOrFail($id);

        if (!in_array($nuevoEstado, ['confirmado', 'anulado'])) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Estado no válido'
            );
            return;
        }

        if ($ingreso->estado === $nuevoEstado) {
            $this->dispatch(
                'notify',
                type: 'warning',
                message: 'El ingreso ya está en este estado'
            );
            return;
        }

        $ingreso->update(['estado' => $nuevoEstado]);

        // 🔥 ACTUALIZAR SALDO ACUMULADO CUANDO CAMBIA EL ESTADO
        $this->actualizarSaldoAcumulado($ingreso->fecha_ingreso->format('Y-m-d'));

        $mensajes = [
            'confirmado' => 'Ingreso confirmado exitosamente',
            'anulado' => 'Ingreso anulado correctamente'
        ];

        $this->dispatch(
            'notify',
            type: 'success',
            message: $mensajes[$nuevoEstado]
        );
    }

    public function eliminarIngreso($id)
    {
        try {
            $ingreso = Ingreso::findOrFail($id);
            $fechaIngreso = $ingreso->fecha_ingreso->format('Y-m-d');
            $concepto = $ingreso->concepto;
            
            $ingreso->delete();

            // 🔥 ACTUALIZAR SALDO ACUMULADO DESPUÉS DE ELIMINAR
            $this->actualizarSaldoAcumulado($fechaIngreso);

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Ingreso "' . $concepto . '" eliminado correctamente'
            );
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al eliminar el ingreso: ' . $e->getMessage()
            );
        }
    }

    /**
     * 🔥 MÉTODO PARA ACTUALIZAR EL SALDO ACUMULADO
     */
    private function actualizarSaldoAcumulado($fecha)
    {
        if (!$fecha) return;
        
        $fechaCarbon = Carbon::parse($fecha);
        $ano = $fechaCarbon->year;
        $mes = $fechaCarbon->month;
        
        // Recalcular saldo para este mes
        SaldoAcumulado::recalcularMes($ano, $mes);
        
        // También recalcular los meses siguientes para mantener consistencia
        $this->recalcularMesesSiguientes($ano, $mes);
    }

    /**
     * Recalcula los meses siguientes para mantener consistencia
     */
    private function recalcularMesesSiguientes($ano, $mes)
    {
        $mesesSiguientes = SaldoAcumulado::where(function($query) use ($ano, $mes) {
                $query->where('ano', '>', $ano)
                      ->orWhere(function($q) use ($ano, $mes) {
                          $q->where('ano', $ano)
                            ->where('mes', '>', $mes);
                      });
            })
            ->orderBy('ano')
            ->orderBy('mes')
            ->get();

        foreach ($mesesSiguientes as $siguiente) {
            SaldoAcumulado::recalcularMes($siguiente->ano, $siguiente->mes);
        }
    }

    public function resetearFormulario()
    {
        $this->resetValidation();

        $this->reset([
            'concepto',
            'monto',
            'tipo',
            'cliente_id',
            'descripcion',
            'metodo_pago',
            'numero_documento',
            'ingresoEditando',
            'cliente_nombre',
            'searchCliente'
        ]);
        
        $this->fecha_ingreso = Carbon::now()->format('Y-m-d');
        $this->mostrarFormulario = false;
        $this->showClienteList = false;
    }

    public function updatedSearchCliente()
    {
        $this->showClienteList = true;
        if (empty($this->searchCliente)) {
            $this->cliente_id = null;
            $this->cliente_nombre = '';
            $this->showClienteList = false;
        }
    }
}