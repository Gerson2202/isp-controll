<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nodo extends Model
{
    protected $fillable = [
        'nombre', 'ip', 'latitud', 'longitud', 'puerto_api','user','pass',
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
        return $this->hasMany(Plan::class);
    }

    public function planes()
    {
        return $this->hasMany(Plan::class);
    }

     // Relación con Pools
     public function pools()
     {
         return $this->hasMany(Pool::class);
     }
}
