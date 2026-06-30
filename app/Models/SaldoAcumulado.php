<?php
// app/Models/SaldoAcumulado.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SaldoAcumulado extends Model
{
    protected $table = 'saldos_acumulados';

    protected $fillable = [
        'ano',
        'mes',
        'saldo_acumulado'
    ];

    protected $casts = [
        'ano' => 'integer',
        'mes' => 'integer',
        'saldo_acumulado' => 'decimal:2'
    ];

    /**
     * Recalcula y guarda el saldo para un mes específico
     */
    public static function recalcularMes($ano, $mes)
    {
        // Obtener saldo del mes anterior
        $saldoAnterior = self::obtenerSaldoAnterior($ano, $mes);
        
        // Calcular ingresos del mes
        $ingresos = self::calcularIngresosMes($ano, $mes);
        
        // Calcular gastos del mes
        $gastos = self::calcularGastosMes($ano, $mes);
        
        // Calcular saldo neto
        $saldoNeto = $ingresos - $gastos;
        $saldoAcumulado = $saldoAnterior + $saldoNeto;
        
        // Guardar o actualizar
        return self::updateOrCreate(
            ['ano' => $ano, 'mes' => $mes],
            ['saldo_acumulado' => $saldoAcumulado]
        );
    }

    /**
     * Obtener saldo del mes anterior
     */
    public static function obtenerSaldoAnterior($ano, $mes)
    {
        $mesAnterior = $mes - 1;
        $anoAnterior = $ano;
        
        if ($mesAnterior < 1) {
            $mesAnterior = 12;
            $anoAnterior = $ano - 1;
        }
        
        $saldo = self::where('ano', $anoAnterior)
                    ->where('mes', $mesAnterior)
                    ->first();
        
        return $saldo ? $saldo->saldo_acumulado : 0;
    }

    /**
     * Calcular ingresos del mes
     */
    public static function calcularIngresosMes($ano, $mes)
    {
        $fechaInicio = Carbon::create($ano, $mes, 1)->startOfMonth();
        $fechaFin = Carbon::create($ano, $mes, 1)->endOfMonth();

        // Sumar pagos de facturas
        $pagos = Pago::whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
            ->sum('monto');

        // Sumar otros ingresos
        $ingresos = Ingreso::whereBetween('fecha_ingreso', [$fechaInicio, $fechaFin])
            ->where('estado', '!=', 'anulado')
            ->sum('monto');

        return $pagos + $ingresos;
    }

    /**
     * Calcular gastos del mes
     */
    public static function calcularGastosMes($ano, $mes)
    {
        $fechaInicio = Carbon::create($ano, $mes, 1)->startOfMonth();
        $fechaFin = Carbon::create($ano, $mes, 1)->endOfMonth();

        // Gastos normales
        $gastosNormales = Gasto::whereBetween('fecha_gasto', [$fechaInicio, $fechaFin])
            ->where('estado', 'pagado')
            ->sum('valor');

        // Gastos recurrentes
        $gastosRecurrentes = GastoRecurrente::where('activo', true)
            ->where('frecuencia', 'mensual')
            ->sum('valor');

        return $gastosNormales + $gastosRecurrentes;
    }
}