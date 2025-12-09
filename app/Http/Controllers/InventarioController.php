<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (!auth()->user()->can('agregar equipo')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('inventario.index');
    }

    public function ModeloIndex()
    {
        // Permisos para crear modelos
        if (!auth()->user()->can('agregar modelo de equipo',)) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('inventario.modeloIndex');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    // Enlistar equipos
    public function list()
    {
        // Ver lista de equipos
        if (!auth()->user()->can('ver equipos')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        // Obtener todos los inventarios con sus relaciones de nodo y cliente
        $inventarios = Inventario::with(['nodo', 'cliente', 'modelo'])->get();
        return view('inventario.list', compact('inventarios'));
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
        $inventario = Inventario::with('modelo')->findOrFail($id); // Carga el equipo con su modelo
        return view('inventario.show', compact('inventario')); // Pasa la variable a la vista

    }

    // Ruta para página de bodega
    public function bodegaIndex()
    {

        if (!auth()->user()->can('crear bodegas')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('bodega.index');
    }

    // Ruta para consumibles
    public function consumiblesIndex()
    {
        if (!auth()->user()->can('agregar consumibles')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('consumibles.index');
    }

    // Ruta para ver show bodegas
    public function showBodega(Bodega $bodega)
    {
        return view('bodega.show', compact('bodega'));
    }

    // Ruta para movimientos
    public function movimientosIndex()
    {
        if (!auth()->user()->can('registrar movientos')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('movimientos.index');
    }

    // Ruta para dashboard de inventario
    public function dashboard()
    {
        if (!auth()->user()->can('consulta global de inventario')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('inventario.dashboard');
    }

    // Ruta para detalle de inventario
    public function detalle($tipo, $id)
    {
        if (!auth()->user()->can('consulta global de inventario')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('inventario.detalle', compact('tipo', 'id'));
    }

    // Ruta para historial de inventario
    public function historial()
    {
        if (!auth()->user()->can('ver historial de movimientos')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('inventario.historial');
    }

    public function edit(Inventario $inventario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventario $inventario)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventario $inventario)
    {
        //
    }
}
