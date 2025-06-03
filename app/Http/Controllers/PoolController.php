<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PoolController extends Controller
{
    public function index(){
         // Verificar si el usuario actual tiene ID 1 o 2 solo permite esos usurios a esas paginas
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            
        }
        return view('pooles.index');
    }
}
