<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bodega extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'descripcion',
    ];

    public function inventarios()
    {
        return $this->hasMany(Inventario::class);
    }

    // relacion con usuarios
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function consumiblesStock()
    {
        return $this->hasMany(\App\Models\ConsumibleStock::class, 'bodega_id');
    }
}
