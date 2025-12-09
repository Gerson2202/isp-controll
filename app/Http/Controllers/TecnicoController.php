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
        if (!auth()->user()->can('ver bodega personal')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('tecnico.bodega');
    }

    public function visitas()
    {
        if (!auth()->user()->can('cerrar tickets-tecnico')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('tecnico.visitas');
    }

    public function actividades()
    {
        if (!auth()->user()->can('ver actividades del dia')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('tecnico.actividades');
    }

    public function asignaciones()
    {
        return view('tecnico.asignaciones');
    }

    public function cerrar(Visita $visita)
    {
        if (!auth()->user()->can('cerrar tickets-tecnico')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('tecnico.cerrarVisita', compact('visita'));
    }
}
