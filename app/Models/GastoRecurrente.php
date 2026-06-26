<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoRecurrente extends Model
{
    protected $table = 'gastos_recurrentes';

    protected $fillable = [
        'categorias_gasto_id',
        'concepto',
        'valor',
        'frecuencia',
        'dia_ejecucion',
        'tipo',          
        'activo',
        'descripcion'
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaGasto::class, 'categorias_gasto_id');
    }
}