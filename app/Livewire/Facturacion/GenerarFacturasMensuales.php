<?php

namespace App\Livewire\Facturacion;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Contrato;
use App\Models\Factura;
use App\Models\ItemFactura;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class GenerarFacturasMensuales extends Component
{
    use WithPagination; // 👈 Añade esto
    public $mes;
    public $anio;
    public $resultados = [];
    public $search = '';
    public $perPage = 10;

    public function mount()
    {
        $hoy = now();
        $this->anio = $hoy->year;

        if ($hoy->day > 25) {
            $hoy = $hoy->addMonth(); // Pasa al mes siguiente
        }

        $this->mes = $hoy->month;
        $this->anio = $hoy->year;
    }


    // Validaciones implementadas:

    // Clientes nuevos: Sin facturas → Genera factura normal

    //  Facturas pendientes: No genera nueva factura

    //  Pagos en mes actual: No genera factura (aunque el pago sea para meses anteriores)

    public function generarFacturas()
    {
        $this->validate([
            'mes' => 'required|numeric|between:1,12',
            'anio' => 'required|numeric|digits:4',
        ]);

        // Obtener contratos activos sin factura este mes
        $contratos = Contrato::with(['cliente', 'plan', 'facturas.pagos'])
            ->where('estado', 'activo')
            ->whereDoesntHave('facturas', function ($query) {
                $query->whereMonth('fecha_emision', $this->mes)
                    ->whereYear('fecha_emision', $this->anio);
            })
            ->get();

        foreach ($contratos as $contrato) {
            try {

                // NUEVA CONDICIÓN SOLO PARA FERNET --- 
                $inicioPeriodo = now()->setDate($this->anio, $this->mes, 1)->startOfDay();

                $inicioPeriodo = now()->setDate($this->anio, $this->mes, 1)->startOfMonth();

                if (
                    $contrato->fecha_inicio &&
                    $contrato->fecha_inicio->gte($inicioPeriodo)
                ) {
                    $this->resultados[] = [
                        'cliente' => $contrato->cliente->nombre,
                        'estado' => 'omitido',
                        'mensaje' => 'Contrato iniciado este mes, se facturará el próximo'
                    ];
                    continue;
                }
                // FIN DE NUEVA CODICION SOLO PARA FERNET--
                // 1. Verificar facturas pendientes (sin pagos completos)
                $tieneFacturasPendientes = $contrato->facturas()
                    ->where('estado', 'pendiente')
                    ->exists();

                if ($tieneFacturasPendientes) {
                    $this->resultados[] = [
                        'cliente' => $contrato->cliente->nombre,
                        'estado' => 'omitido',
                        'mensaje' => 'Tiene facturas pendientes'
                    ];
                    continue;
                }

                // 2. Verificar si hay pagos recientes (a través de facturas)
                $pagoEnMesActual = false;
                foreach ($contrato->facturas as $factura) {
                    if ($factura->pagos()
                        ->whereMonth('fecha_pago', $this->mes)
                        ->whereYear('fecha_pago', $this->anio)
                        ->exists()
                    ) {
                        $pagoEnMesActual = true;
                        break;
                    }
                }

                if ($pagoEnMesActual) {
                    $this->resultados[] = [
                        'cliente' => $contrato->cliente->nombre,
                        'estado' => 'omitido',
                        'mensaje' => 'Realizó pago reciente ; en ' . $this->mes . '/' . $this->anio
                    ];
                    continue;
                }

                // 3. Generar nueva factura
                $fechaEmision = now()->setDate($this->anio, $this->mes, 3);
                $fechaVencimiento = $fechaEmision->copy()->addMonth()->day(3);

                $factura = Factura::create([
                    'contrato_id' => $contrato->id,
                    'numero_factura' => $this->generarNumeroFactura(),
                    'fecha_emision' => $fechaEmision,
                    'fecha_vencimiento' => $fechaVencimiento,
                    'monto_total' => $contrato->precio,
                    'saldo_pendiente' => $contrato->precio,
                    'estado' => 'pendiente',
                ]);

                $factura->items()->create([
                    'descripcion' => 'Servicio de internet - ' . $contrato->plan->nombre,
                    'monto' => $contrato->precio,
                ]);

                $this->resultados[] = [
                    'cliente' => $contrato->cliente->nombre,
                    'estado' => 'éxito',
                    'mensaje' => 'Factura generada: ' . $factura->numero_factura
                ];
            } catch (\Exception $e) {
                $this->resultados[] = [
                    'cliente' => $contrato->cliente->nombre,
                    'estado' => 'error',
                    'mensaje' => 'Error: ' . $e->getMessage()
                ];
            }
        }
        // === AGREGAR AQUÍ === //
        if (empty(array_filter($this->resultados, function ($item) {
            return $item['estado'] === 'éxito';
        }))) {
            $this->resultados[] = [
                'estado' => 'info',
                'mensaje' => 'No se generaron facturas nuevas. Todos los contratos están al día, tienen pagos recientes o ya tienen facturas para este periodo.'
            ];
        }
        // === FIN DEL BLOQUE NUEVO === //

        return $this->resultados; // Si usas return
    }

    protected function generarNumeroFactura()
    {
        return 'FAC-' . strtoupper(Str::random(6)) . '-' . now()->format('YmdHis');
    }

    public function eliminarUltimoLote()
    {
        // Verificar si hay facturas en el período actual
        $facturas = Factura::whereMonth('fecha_emision', $this->mes)
            ->whereYear('fecha_emision', $this->anio)
            ->get();

        if ($facturas->isEmpty()) {
            $this->dispatch(
                'notify',
                type: 'warning',
                message: 'No hay facturas para el período ' . $this->mes . '/' . $this->anio
            );
            return;
        }

        // Verificar pagos
        $facturasConPagos = $facturas->filter(function ($factura) {
            return $factura->pagos()->exists();
        });

        if ($facturasConPagos->isNotEmpty()) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: $facturasConPagos->count() . ' facturas no pueden eliminarse porque tienen pagos asociados'
            );
            return;
        }

        // Eliminar en transacción
        try {
            DB::transaction(function () use ($facturas) {
                ItemFactura::whereIn('factura_id', $facturas->pluck('id'))->delete();
                Factura::whereIn('id', $facturas->pluck('id'))->delete();
            });

            $this->dispatch(
                'notify',
                type: 'success',
                message: 'Se eliminaron ' . $facturas->count() . ' facturas'
            );

            $this->resultados = [];
        } catch (\Exception $e) {
            $this->dispatch(
                'notify',
                type: 'error',
                message: 'Error al eliminar facturas: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.facturacion.generar-facturas-mensuales', [
            'resultados' => $this->resultados // Pasa explícitamente la variable
        ]);
    }
}
