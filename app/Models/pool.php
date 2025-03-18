<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class pool extends Model
{
    // Protege los campos para evitar asignaciones masivas no deseadas
    protected $fillable = ['nombre', 'start_ip', 'end_ip','nodo_id','descripcion'];

    // Relación con Nodo
    public function nodo()
    {
        return $this->belongsTo(Nodo::class);
    }

    // Relación con Clientes
    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}
