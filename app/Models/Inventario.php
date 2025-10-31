<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    protected $fillable = [
        'modelo_id', 'mac', 'descripcion', 'foto', 'cliente_id','nodo_id','user_id','bodega_id', 'fecha','visita_id'
    ];

    public function modelo()
    {
        return $this->belongsTo(Modelo::class, 'modelo_id'); // 'modelo_id' es la clave foránea
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

     public function bodega()
    {
        return $this->belongsTo(Bodega::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'modelo_id'); // 'modelo_id' es la clave foránea
    }
    

    // Relación inversa (Un inventario pertenece a un nodo)
    public function nodo()
    {
        return $this->belongsTo(Nodo::class, 'nodo_id');  // Un inventario pertenece a un nodo
    }
    
     public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class);
    }
}
