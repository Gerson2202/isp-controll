<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketUsuario extends Model
{
    use HasFactory;

    // Si no necesitas la columna "id" autoincremental, puedes configurarlo
    public $incrementing = false; // Si es necesario.

    // Definir la tabla si el nombre no es plural
    // protected $table = 'ticket_usuarios';

    // Los atributos que se pueden asignar masivamente
    protected $fillable = ['ticket_id', 'usuario_id'];

    // Relación con Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relación con Usuario
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
