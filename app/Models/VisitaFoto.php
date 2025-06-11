<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitaFoto extends Model
{
     protected $fillable = [
        'visita_id',
        'ruta',
        'nombre_original',
        'descripcion'
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class);
    }
}
