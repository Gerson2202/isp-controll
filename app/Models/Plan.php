<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'velocidad_bajada', 'velocidad_subida','rehuso','nodo_id'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
        // return $this->belongsTo(Nodo::class)->withDefault();  // Esto ayudará a manejar un nodo nulo
    }

    // / Definir la relación con el modelo Nodo
    public function nodo()
    {
        return $this->belongsTo(Nodo::class);  // Un plan pertenece a un nodo
    }

     // Relación con Contratos
     public function contratos()
     {
         return $this->hasMany(Contrato::class);
     }
}
