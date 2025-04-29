<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
   
    protected $table = 'pagos';
    
    protected $fillable = [
        'factura_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'referencia',
        'notas'
    ];
    
    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];
    
    public function factura(): BelongsTo
    {
        return $this->belongsTo(factura::class);
    }
}
