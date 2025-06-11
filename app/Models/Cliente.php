<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;  

    protected $fillable = [
        'nombre', 'telefono', 'cedula','direccion', 'latitud', 'longitud', 'ip', 'correo', 'punto_referencia', 'electronico', 'plan_id', 'estado', 'nodo_id','descripcion', 'pool_id'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function nodo()
    {
        return $this->belongsTo(Nodo::class);
    }

     // Relación con Contratos
     public function contratos()
     {
         return $this->hasMany(Contrato::class);
     }
     public function contrato()
     {
         return $this->hasOne(Contrato::class); // Un cliente tiene un contrato
     }
     // Relación con Pool
    public function pool()
    {
        return $this->belongsTo(Pool::class);
    }

    // ---- FACTURAACION 
   
    public function facturas()
    {
        return $this->hasManyThrough(Factura::class, Contrato::class);
    }

    public function cortes()
    {
        return $this->hasMany(HistorialCorte::class);
    }

    
    public function fotos()
    {
        return $this->hasMany(ClienteFoto::class);
    }
}
