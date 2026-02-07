<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    // protected $table = 'plans'; // Nombre de la tabla

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',

        // Velocidades base (Mbps)
        'velocidad_bajada',
        'velocidad_subida',

        // Burst
        'rafaga_max_bajada',
        'rafaga_max_subida',

        // Burst threshold
        'velocidad_media_bajada',
        'velocidad_media_subida',

        // Burst time
        'tiempo_rafaga_bajada',
        'tiempo_rafaga_subida',

        // Queue
        'prioridad',
        'rehuso',

        // Relación
        'nodo_id',
    ];

    protected $casts = [
        'velocidad_bajada' => 'integer',
        'velocidad_subida' => 'integer',
        'rafaga_max_bajada' => 'integer',
        'rafaga_max_subida' => 'integer',
        'velocidad_media_bajada' => 'integer',
        'velocidad_media_subida' => 'integer',
        'tiempo_rafaga_bajada' => 'integer',
        'tiempo_rafaga_subida' => 'integer',
        'prioridad' => 'integer',
        'precio' => 'decimal:2',
    ];
    protected $attributes = [
        'prioridad' => 8,
    ];


    public function clientes()
    {
        return $this->hasMany(Cliente::class);
        // return $this->belongsTo(Nodo::class)->withDefault();  // Esto ayudará a manejar un nodo nulo
    }

    // / Definir la relación con el modelo Nodo
    public function nodo()
    {
        return $this->belongsTo(Nodo::class);  // Un plan pertenece a un nodo
    }

    // Relación con Contratos
    public function contratos()
    {
        return $this->hasMany(Contrato::class);
    }
}
