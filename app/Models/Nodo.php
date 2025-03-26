<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nodo extends Model
{
    use HasFactory;
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

   // Relación de uno a muchos (Un nodo tiene muchos inventarios)
   public function inventarios()
   {
       return $this->hasMany(Inventario::class, 'nodo_id');  // Un nodo tiene muchos inventarios
   }
}
