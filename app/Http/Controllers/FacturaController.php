<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    public function index()
    { 
      // Crear facturas
       if (!auth()->user()->can('crear facturas')) {
        abort(403, 'No tienes permiso para acceder a esta pagina');
      }
       return view('facturacion.index');
    }

    public function dashboard()
    { 
         // Ver dasboard financiero
        if (!auth()->user()->can('ver dashborad financiero')) {
          abort(403, 'No tienes permiso para acceder a esta pagina');
        }
       return view('facturacion.dashboard');
    }

    public function cortes()
    {
        if (!auth()->user()->can('cortar clientes masivos')) {
        abort(403, 'No tienes permisos para acceder a esta pagina');
       }
       return view('clientes.tablaCortes');
    }

     
}
