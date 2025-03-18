<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrato extends Model
{
    use HasFactory;

     // Campos que pueden ser asignados masivamente
     protected $fillable = [
        'cliente_id',  // ID del cliente
        'plan_id',     // ID del plan
        'fecha_inicio',
        'fecha_fin',
        'precio',      // Cambié 'monto' por 'precio'
    ];

     // Relación con Cliente
     public function cliente()
     {
         return $this->belongsTo(Cliente::class);
     }
 
     // Relación con Plan
     public function plan()
     {
         return $this->belongsTo(Plan::class);
     }
}
