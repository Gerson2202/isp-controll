<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnvioMasivoLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'factura_id',
        'estado',
        'plantilla_usada',
        'fecha_envio',
        'response_data',
        'ip_cliente',
        'error_message'
    ];

    protected $casts = [
        'response_data' => 'array',
        'fecha_envio' => 'datetime',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
}