<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'inventario_id',
        'tipo_movimiento',
        'descripcion',
        'bodega_anterior_id',
        'user_anterior_id',
        'nodo_anterior_id',
        'cliente_anterior_id',
        'bodega_nueva_id',
        'user_nuevo_id',
        'nodo_nuevo_id',
        'cliente_nuevo_id',
        'user_id'
    ];

    // Relaciones
    public function inventario()
    {
        return $this->belongsTo(Inventario::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bodegaAnterior()
    {
        return $this->belongsTo(Bodega::class, 'bodega_anterior_id');
    }

    public function bodegaNueva()
    {
        return $this->belongsTo(Bodega::class, 'bodega_nueva_id');
    }

    public function userAnterior()
    {
        return $this->belongsTo(User::class, 'user_anterior_id');
    }

    public function userNuevo()
    {
        return $this->belongsTo(User::class, 'user_nuevo_id');
    }

    public function nodoAnterior()
    {
        return $this->belongsTo(Nodo::class, 'nodo_anterior_id');
    }

    public function nodoNuevo()
    {
        return $this->belongsTo(Nodo::class, 'nodo_nuevo_id');
    }

    public function clienteAnterior()
    {
        return $this->belongsTo(Cliente::class, 'cliente_anterior_id');
    }

    public function clienteNuevo()
    {
        return $this->belongsTo(Cliente::class, 'cliente_nuevo_id');
    }

    public function visitaAnterior()
    {
        return $this->belongsTo(Cliente::class, 'visita_anterior_id');
    }

    public function visitaNuevo()
    {
        return $this->belongsTo(Cliente::class, 'visita_nuevo_id');
    }
    // Usada para mostrar el historial de movimientos en l vista tecnicos
    public function usuarioAccion()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // --- ORIGEN ---
    public function visitaNueva()      { return $this->belongsTo(Visita::class, 'visita_nuevo_id'); }

}