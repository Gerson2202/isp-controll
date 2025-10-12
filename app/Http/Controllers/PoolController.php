<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index(){

        // Permisos para acceder a crear pooles
        if (!auth()->user()->can('crear pool')) {
            abort(403, 'No tienes permiso para crear Pooles de ip');
        }   
        return view('pooles.index');
    }
}
