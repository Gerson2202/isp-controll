<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Inventario;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;


class ClienteController extends Controller
{
   

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Crear clientes
        if (!auth()->user()->can('crear clientes')) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('clientes.create');
    }
    // Enviar ala vista imagenes
    public function imagenes($id)
    {
        // Permiso para ver imagenes del cliente
        if (!auth()->user()->can('ver imagenes del cliente')) {
        abort(403, 'No tienes permiso para ver imagenes clientes');
        } 

        $cliente = Cliente::findOrFail($id);
        // Retorna la vista con las imágenes
        return view('clientes.imagenes', compact('cliente'));
    }

    // Funcion ver clientes pagina del buscado
    public function search()
    {
        // Permiso para ver cliente
        if (!auth()->user()->can('ver clientes')) {
        abort(403, 'No tienes permiso para ver clientes');
        }   

        return view('clientes.search');
    }

    public function historialFacturas(Cliente $cliente)
    {
        // Ver historial de facturas
         if (!auth()->user()->can('ver historico de facturas',)) {
            abort(403, 'No tienes permiso para acceder a esta pagina');
        }
        return view('clientes.historial-facturas', compact('cliente'));
    }

    public function graficas($id)
    {   
        // Permiso para ver grafica del cliente
        if (!auth()->user()->can('ver clientes')) {
        abort(403, 'No tienes permiso para ver grafica clientes');
        }   
        $cliente = Cliente::findOrFail($id);
        return view('clientes.graficas', compact('cliente'));
    }
    
    // Funcion para mostrar clientes sin ip asignadas 
    public function asignarIPindex()
    {
        
         // Obtener todos los clientes donde el campo 'ip' sea nulo y que tengan un contrato asignado y el contrato este activo o suspendido
         //    contratos en cancelado no apareceran
            $clientes = Cliente::whereNull('ip')
            ->whereHas('contratos', function($query) {
                $query->where('estado', '!=', 'cancelado');
            })
            ->get();

        // Retornar la vista con los clientes
        return view('clientes.asignar_ip', compact('clientes'));
    }

     // Vista para asignar IP a un cliente en particular 

    public function asignarIpCliente($id_cliente)
    {
        // Permiso para asignar ip al cliente 
        if (!auth()->user()->can('asignar ip')) {
            abort(403, 'No tienes permiso asignar ip a clientes');
        }     
       $cliente= Cliente::findOrFail($id_cliente);
       return view('clientes.asignaripshow', compact('cliente'));
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
       // Permiso para ver cliente
         if (!auth()->user()->can('ver clientes')) {
         abort(403, 'No tienes permiso para ver clientes');
         } 

        $inventarios = Inventario::where('cliente_id', $id)
        ->get();
        
        // Cargar tickets con sus visitas relacionadas
        $cliente = Cliente::with(['contrato.plan', 'tickets.visita'])->find($id);
       
        // Obtener los tickets del cliente
        $tickets = $cliente->tickets;
        // Contar el total de tickets abiertos
        $totalTicketsAbiertos = $cliente->tickets()->where('estado', 'abierto')->count();
        return view('clientes.show', compact('cliente','tickets','totalTicketsAbiertos','inventarios'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cliente $cliente)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {   
        // Permiso para editar cliente
        if (!auth()->user()->can('editar informacion de cliente')) {
        abort(403, 'No tienes permiso para editar clientes');
        } 
        // return $request;
        $request->validate([
            'cedula' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email',
            'direccion' => 'nullable|string',
            'latitud' => 'nullable|numeric',
            'longitud' => 'nullable|numeric',
            'nombre' => 'string',
            'descripcion' => 'nullable|string',
        ]);
    
        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
    
        return redirect()->back()->with('success', 'Información del cliente actualizada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $cliente)
    {
        //
    }
}
