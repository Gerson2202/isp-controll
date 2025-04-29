<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Factura extends Model
{
    protected $table = 'facturas';
    
    protected $fillable = [
        'contrato_id',
        'numero_factura',
        'fecha_emision',
        'fecha_vencimiento',
        'monto_total',
        'saldo_pendiente',
        'estado',
        'notas'
    ];
    
    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_vencimiento' => 'date',
        'monto_total' => 'decimal:2',
        'saldo_pendiente' => 'decimal:2'
    ];
    
    public function contrato(): BelongsTo
    {
        return $this->belongsTo(Contrato::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(ItemFactura::class);
    }
    
    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }
    
    public function cliente()
    {
        return $this->throughContrato()->cliente();
    }
    
    public function throughContrato()
    {
        return $this->contrato();
    }
}
