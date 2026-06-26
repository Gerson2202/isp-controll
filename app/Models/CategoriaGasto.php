<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaGasto extends Model
{
    protected $table = 'categorias_gastos';
    protected $fillable = [
        'nombre',
        'color',
        'descripcion',
        'activo'
    ];

    public function gastos()
    {
        return $this->hasMany(Gasto::class);
    }

    public function recurrentes()
    {
        return $this->hasMany(GastoRecurrente::class);
    }
}
