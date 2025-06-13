<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'tipo_reporte', 'situacion', 'fecha_creacion', 'fecha_cierre', 'estado', 'cliente_id','solucion','user_id'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    // Relación: Un ticket puede ser asignado a muchos usuarios
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'ticket_usuario');
    }

    public function visita()
    {
        return $this->hasOne(Visita::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
