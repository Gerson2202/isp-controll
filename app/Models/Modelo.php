<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',  // El nombre del modelo
        'foto',    // La foto del modelo (path de la imagen)
    ];
}
