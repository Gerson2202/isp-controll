<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Permisos para editar tickets
        if (!auth()->user()->can('editar tickets',)) {
        abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('tickets.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    
    public function Tablahistorial()
    {
         // Permisos para ver historial de  tickets
        if (!auth()->user()->can('ver historial de tickets')) {
        abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('tickets.historial');
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
    public function show(Ticket $ticket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        $ticketId=$ticket;
        // print $ticketId;
        return view('tickets.edit',compact('ticketId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        //
    }
}
