<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'modelo', 'mac', 'descripcion', 'foto', 'cliente_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
