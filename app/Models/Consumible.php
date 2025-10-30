<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consumible extends Model
{
   use HasFactory;

    protected $fillable = ['nombre','descripcion','unidad'];

    public function stocks()
    {
        return $this->hasMany(ConsumibleStock::class);
    }

    public function movimientos()
    {
        return $this->hasMany(ConsumibleMovimiento::class);
    }
        use HasFactory;

    

    // Calcular stock actual
    public function getStockActualAttribute()
    {
        $entradas = $this->movimientos()->where('tipo_movimiento', 'entrada')->sum('cantidad');
        $salidas = $this->movimientos()->where('tipo_movimiento', 'salida')->sum('cantidad');
        
        return $entradas - $salidas;
    }

     public function visitas()
    {
        return $this->belongsToMany(Visita::class, 'consumible_visita')
                    ->withTimestamps();
    }

}
