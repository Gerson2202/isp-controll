<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $fillable = [
        'conversacion_id',
        'tipo',
        'tipo_contenido',
        'mensaje',
        'archivo_url',
        'whatsapp_message_id',
        'estado_whatsapp',
        'fecha_mensaje',
    ];

    protected $casts = [
        'fecha_mensaje' => 'datetime',
    ];

    public function conversacion()
    {
        return $this->belongsTo(Conversacion::class);
    }
}