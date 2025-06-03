<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    {
      // Verificar si el usuario actual tiene ID 1 o 2
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            
        }
       return view('facturacion.index');
    }

    public function dashboard()
    {
       // Verificar si el usuario actual tiene ID 1 o 2
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            
        }
       return view('facturacion.dashboard');
    }

    public function cortes()
    {
         // Verificar si el usuario actual tiene ID 1 o 2
         if (!in_array(auth()->id(), [1, 2,3])) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
            
        }
       return view('clientes.tablaCortes');
    }
}
