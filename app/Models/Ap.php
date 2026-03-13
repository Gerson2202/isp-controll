<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ap extends Model
{
    
    protected $table = 'aps';

    protected $fillable = [
        'nombre',
        'ip_lan',
        'ip_wan',
        'puerto_lan',
        'puerto_wan',
        'ssid',
        'clave',
        'user_login',
        'clave_login',
        'inventario_id',
        'clientes_max',
        'estado',
        'frecuencia',
        'ancho_canal'
    ];

    // AP pertenece a inventario
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    // AP tiene muchos clientes
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}
