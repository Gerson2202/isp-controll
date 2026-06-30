<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class GastoRecurrente extends Model
{
    protected $table = 'gastos_recurrentes';

    protected $fillable = [
        'categorias_gasto_id',
        'concepto',
        'valor',
        'frecuencia',
        'dia_ejecucion',
        'activo',
        'descripcion',
        'tipo',
        'ano',
        'mes',
        'fecha_pago',
        'pagado',
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'activo' => 'boolean',
        'pagado' => 'boolean',
        'fecha_pago' => 'date',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaGasto::class, 'categorias_gasto_id');
    }

    /**
     * Obtener los gastos recurrentes base (los que tienen año y mes nulos)
     */
    public static function getGastosBase()
    {
        return self::whereNull('ano')
            ->whereNull('mes')
            ->where('activo', true)
            ->get();
    }

    /**
     * Marcar como pagado (CREA NUEVO REGISTRO)
     */
    public static function marcarComoPagado($concepto, $valor, $categoriaId, $fecha = null, $diaEjecucion = 1)
    {
        $fecha = $fecha ?? Carbon::now();
        $fechaCarbon = Carbon::parse($fecha);

        // Verificar si ya está pagado este mes
        $yaPagado = self::where('concepto', $concepto)
            ->where('ano', $fechaCarbon->year)
            ->where('mes', $fechaCarbon->month)
            ->where('pagado', true)
            ->exists();

        if ($yaPagado) {
            return null;
        }

        // Crear nuevo registro
        return self::create([
            'categorias_gasto_id' => $categoriaId,
            'concepto' => $concepto,
            'valor' => $valor,
            'frecuencia' => 'mensual',
            'dia_ejecucion' => $diaEjecucion,
            'activo' => true,
            'tipo' => 'fijo',
            'ano' => $fechaCarbon->year,
            'mes' => $fechaCarbon->month,
            'fecha_pago' => $fechaCarbon->format('Y-m-d'),
            'pagado' => true,
        ]);
    }

    /**
     * Obtener los gastos recurrentes pagados en un mes específico
     */
    public static function getPagadosDelMes($mes, $ano)
    {
        return self::where('ano', $ano)
            ->where('mes', $mes)
            ->where('pagado', true)
            ->get();
    }

    /**
     * Verificar si un gasto recurrente ya fue pagado en el mes
     */
    public static function yaPagadoEsteMes($concepto, $mes, $ano)
    {
        return self::where('concepto', $concepto)
            ->where('ano', $ano)
            ->where('mes', $mes)
            ->where('pagado', true)
            ->exists();
    }

    /**
     * Obtener el total de gastos recurrentes pagados en un mes
     */
    public static function getTotalPagadosMes($mes, $ano)
    {
        return self::where('ano', $ano)
            ->where('mes', $mes)
            ->where('pagado', true)
            ->sum('valor');
    }

    /**
     * Obtener los gastos base con su estado de pago del mes
     */
    public static function getGastosBaseConEstado($mes, $ano)
    {
        $gastosBase = self::whereNull('ano')
            ->whereNull('mes')
            ->where('activo', true)
            ->get();

        foreach ($gastosBase as $gasto) {
            $gasto->pagado_este_mes = self::where('concepto', $gasto->concepto)
                ->where('ano', $ano)
                ->where('mes', $mes)
                ->where('pagado', true)
                ->exists();
                
            // Buscar el registro de pago si existe
            $registroPago = self::where('concepto', $gasto->concepto)
                ->where('ano', $ano)
                ->where('mes', $mes)
                ->where('pagado', true)
                ->first();
                
            $gasto->registro_pago = $registroPago;
            $gasto->fecha_pago_mes = $registroPago ? $registroPago->fecha_pago : null;
        }

        return $gastosBase;
    }
}