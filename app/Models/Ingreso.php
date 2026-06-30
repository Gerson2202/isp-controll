<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $fillable = [
        'visita_id',
        'concepto',
        'monto',
        'fecha_ingreso',
        'tipo',
        'cliente_id',
        'estado',
        'descripcion',
        'numero_documento',
        'metodo_pago',
        'user_id'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'monto' => 'decimal:2'
    ];

    // Relaciones
    public function visita(): BelongsTo
    {
        return $this->belongsTo(Visita::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para filtrar por fecha
    public function scopeMes($query, $year, $month)
    {
        return $query->whereYear('fecha_ingreso', $year)
                    ->whereMonth('fecha_ingreso', $month);
    }
}