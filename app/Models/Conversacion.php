<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversacion extends Model
{

    use HasFactory;

    protected $table = 'conversaciones'; // <--- Asegurar que use el nombre correcto
    protected $fillable = [
        'cliente_id',
        'telefono',
        'nombre_contacto',
        'estado',
        'ia_activa',
        'asignado_a',
        'ultima_actividad',
    ];

    protected $casts = [
        'ultima_actividad' => 'datetime',
        'ia_activa' => 'boolean',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function mensajes()
    {
        return $this->hasMany(Mensaje::class);
    }

    public function agente()
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }
}
