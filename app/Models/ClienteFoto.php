<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFoto extends Model
{
   protected $fillable = [
        'cliente_id',
        'ruta',
        'nombre_original',
        'descripcion' 
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
