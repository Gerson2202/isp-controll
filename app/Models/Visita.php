<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    use HasFactory;
    protected $table = 'visitas'; // Nombre de la tabla
    protected $dates = ['fecha_inicio', 'fecha_cierre']; // Asegúrate de que los campos de fecha sean manipulados como fechas

    protected $fillable = [
        'ticket_id',
        'fecha_inicio',
        'fecha_cierre',
        'descripcion',
        'color',
        'estado',
        'solucion',
    ];

    // En Visita.php
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'visita_user');
    }

    // Relación con Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
