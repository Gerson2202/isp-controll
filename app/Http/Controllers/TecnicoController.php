<?php

namespace App\Http\Controllers;

use App\Models\Visita;
use Illuminate\Http\Request;

class TecnicoController extends Controller
{
    public function index()
    {
        return view('tecnico.index');
    }

    public function bodega()
    {
        return view('tecnico.bodega');
    }

    public function visitas()
    {
        return view('tecnico.visitas');
    }

    public function actividades()
    {
        return view('tecnico.actividades');
    }

    public function asignaciones()
    {
        return view('tecnico.asignaciones');
    }

     public function cerrar(Visita $visita)
    {
       
        return view('tecnico.cerrarVisita', compact('visita'));
    }
 
}
