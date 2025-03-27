<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'modelo_id', 'mac', 'descripcion', 'foto', 'cliente_id','nodo_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id'); // 'modelo_id' es la clave foránea
    }

    // Relación inversa (Un inventario pertenece a un nodo)
    public function nodo()
    {
        return $this->belongsTo(Nodo::class, 'nodo_id');  // Un inventario pertenece a un nodo
    }
}
