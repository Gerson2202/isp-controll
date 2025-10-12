<?php

namespace App\Http\Controllers;

use App\Models\Nodo;
use Illuminate\Http\Request;

class NodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        // Gestionar nodos
        if (!auth()->user()->can('gestionar nodos')) {
         abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('nodos.index');
    }
    public function index1()
    {
        // Monitoreo de nodos
        if (!auth()->user()->can('ver monitoreo de nodos')) {
         abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('nodos.monitoreo');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $nodo = Nodo::findOrFail($id);
        return view('nodos.show', compact('nodo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nodo $nodo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nodo $nodo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nodo $nodo)
    {
        //
    }
}
