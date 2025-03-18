<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foto_Ticket extends Model
{
    protected $fillable = [
        'tipo_reporte', 'situacion', 'fecha_creacion', 'fecha_cierre', 'estado', 'cliente_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function fotos()
    {
        return $this->hasMany(Foto_Ticket::class);
    }
}
