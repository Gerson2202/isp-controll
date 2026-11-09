<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $fillable = [
        'categorias_gasto_id',
        'concepto',
        'valor',
        'fecha_gasto',
        'tipo',
        'estado',
        'descripcion',
        'user_id',
        'metodo_pago',
        'gasto_recurrente_id',
    ];

    protected $casts = [
        'fecha_gasto' => 'date'
    ];

    public function gastoRecurrente()
    {
        return $this->belongsTo(GastoRecurrente::class);
    }
    public function categoria()
    {
        // Le indicamos explícitamente que la columna en la DB lleva la "s" -> 'categorias_gasto_id'
        return $this->belongsTo(CategoriaGasto::class, 'categorias_gasto_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function adjuntos()
    {
        return $this->hasMany(GastoAdjunto::class);
    }
}
