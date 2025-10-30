<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumibleMovimiento extends Model
{
     use HasFactory;

    protected $table = 'consumible_movimientos';

    protected $fillable = [
        'consumible_id',
        'cantidad',
        'tipo_movimiento',
        'origen_tipo',
        'origen_id',
        'destino_tipo',
        'destino_id',
        'descripcion',
        'user_id'
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    // Relaciones básicas
    public function consumible()
    {
        return $this->belongsTo(Consumible::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Eliminamos las relaciones polimórficas y hacemos métodos manuales
    public function getOrigenAttribute()
    {
        if (!$this->origen_tipo || !$this->origen_id) {
            return null;
        }

        return $this->getModelByType($this->origen_tipo, $this->origen_id);
    }

    public function getDestinoAttribute()
    {
        if (!$this->destino_tipo || !$this->destino_id) {
            return null;
        }

        return $this->getModelByType($this->destino_tipo, $this->destino_id);
    }

    public function getOrigenNombreAttribute()
    {
        $origen = $this->origen;
        if (!$origen) return 'N/A';

        return match($this->origen_tipo) {
            'bodega' => 'Bodega: ' . $origen->nombre,
            'usuario' => 'Usuario: ' . $origen->name,
            'nodo' => 'Nodo: ' . $origen->nombre,
            'cliente' => 'Cliente: ' . $origen->nombre,
            default => 'N/A'
        };
    }

    public function getDestinoNombreAttribute()
    {
        $destino = $this->destino;
        if (!$destino) return 'N/A';

        return match($this->destino_tipo) {
            'bodega' => 'Bodega: ' . $destino->nombre,
            'usuario' => 'Usuario: ' . $destino->name,
            'nodo' => 'Nodo: ' . $destino->nombre,
            'cliente' => 'Cliente: ' . $destino->nombre,
            default => 'N/A'
        };
    }

    // Método auxiliar para obtener el modelo según el tipo
    private function getModelByType($type, $id)
    {
        $modelClass = match($type) {
            'bodega' => \App\Models\Bodega::class,
            'usuario' => \App\Models\User::class,
            'nodo' => \App\Models\Nodo::class,
            'cliente' => \App\Models\Cliente::class,
            default => null
        };

        return $modelClass ? $modelClass::find($id) : null;
    }

    public function getCantidadFormateadaAttribute()
    {
        return $this->cantidad . ' ' . ($this->consumible->unidad ?? 'unidades');
    }

    public function getBadgeColorAttribute()
    {
        return match($this->tipo_movimiento) {
            'entrada' => 'success',
            'salida' => 'danger',
            'traslado' => 'warning',
            default => 'secondary'
        };
    }
    

    

    // Relaciones dinámicas
    public function origen()
    {
        return $this->resolveRelation($this->origen_tipo, $this->origen_id);
    }

    public function destino()
    {
        return $this->resolveRelation($this->destino_tipo, $this->destino_id);
    }

    protected function resolveRelation($tipo, $id)
    {
        if (!$tipo || !$id) return null;

        $modelMap = [
            'usuario' => User::class,
            'bodega'  => Bodega::class,
            'cliente' => Cliente::class,
            'nodo'    => Nodo::class,
            'visita'  => Visita::class,
        ];

        $model = $modelMap[strtolower($tipo)] ?? null;
        return $model ? $model::find($id) : null;
    }
}
