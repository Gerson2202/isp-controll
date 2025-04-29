<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ItemFactura extends Model
{
     protected $table = 'items_factura'; // Forzar nombre de tabla

    protected $fillable = [
        'factura_id', 
        'descripcion',
        'monto'
    ];

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }
}
