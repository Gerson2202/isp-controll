<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GastoAdjunto extends Model
{
    protected $fillable = [
        'gasto_id',
        'archivo',
        'nombre_original',
        'mime_type',
        'size'
    ];

    public function gasto()
    {
        return $this->belongsTo(Gasto::class);
    }
}
