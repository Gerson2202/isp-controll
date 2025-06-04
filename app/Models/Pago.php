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
        'notas',
        'user_id' // Nuevo campo
    ];
    
    protected $casts = [
        'fecha_pago' => 'date',
        'monto' => 'decimal:2'
    ];
    
    public function factura(): BelongsTo
    {
        return $this->belongsTo(factura::class);
    }
    // Relación con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
