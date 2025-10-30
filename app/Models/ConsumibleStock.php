<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumibleStock extends Model
{
     use HasFactory;

    protected $table = 'consumible_stock';

    protected $fillable = [
        'consumible_id','cantidad','bodega_id','cliente_id','nodo_id','usuario_id','visita_id','fecha_ingreso'
    ];

    public function consumible() { return $this->belongsTo(Consumible::class); }
    public function bodega() { return $this->belongsTo(Bodega::class); }
    public function cliente() { return $this->belongsTo(Cliente::class); }
    public function nodo() { return $this->belongsTo(Nodo::class); }
    public function usuario() { return $this->belongsTo(User::class); }
    public function visita() { return $this->belongsTo(Visita::class); }

}
