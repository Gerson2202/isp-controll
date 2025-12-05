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
        'observacion',
        'titulo'
    ];

    // En Visita.php
    // public function usuarios()
    // {
    //     return $this->belongsToMany(User::class, 'visita_user');
    // }

    // Relación con Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    // app/Models/Visita.php
    public function fotos()
    {
        return $this->hasMany(VisitaFoto::class);
    }
    public function consumibles()
    {
        return $this->belongsToMany(Consumible::class, 'consumible_visita')
            ->withTimestamps();
    }
    // para registrar datos
    // public function users()
    // {
    //     return $this->belongsToMany(User::class, 'visita_user')->withTimestamps();
    // }
    public function users()
    {
        return $this->belongsToMany(User::class, 'visita_user')
            ->withPivot(['fecha_inicio', 'fecha_cierre'])
            ->withTimestamps();
    }
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'visita_user')
            ->withPivot(['fecha_inicio', 'fecha_cierre'])
            ->withTimestamps();
    }

    // USADO PARA LA VISTA VISITA SHOW
    public function inventarios()
    {
        return $this->hasMany(\App\Models\Inventario::class);
    }

    public function consumibleStock()
    {
        return $this->hasMany(\App\Models\ConsumibleStock::class);
    }
    
    //  public function users()
    //  {
    //     return $this->belongsToMany(User::class)
    //          ->withPivot(['fecha_inicio', 'fecha_cierre'])
    //          ->withTimestamps();
    //  }

}
