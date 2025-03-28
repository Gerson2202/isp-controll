<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    // En Visita.php
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'visita_user');
    }
}
